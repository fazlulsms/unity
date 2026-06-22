<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Member;
use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = MembershipApplication::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(25);
        $counts = [
            'all'                => MembershipApplication::count(),
            'pending'            => MembershipApplication::where('status', 'pending')->count(),
            'under_review'       => MembershipApplication::where('status', 'under_review')->count(),
            'more_info_required' => MembershipApplication::where('status', 'more_info_required')->count(),
            'photo_required'     => MembershipApplication::where('status', 'photo_required')->count(),
            'approved'           => MembershipApplication::where('status', 'approved')->count(),
            'rejected'           => MembershipApplication::where('status', 'rejected')->count(),
        ];

        return view('admin.applications.index', compact('applications', 'counts'));
    }

    public function show(MembershipApplication $application)
    {
        $application->load('reviewer', 'member.user');
        return view('admin.applications.show', compact('application'));
    }

    public function edit(MembershipApplication $application)
    {
        return view('admin.applications.edit', compact('application'));
    }

    public function update(Request $request, MembershipApplication $application)
    {
        $data = $request->validate([
            'full_name'          => 'required|string|max:255',
            'phone'              => 'required|string|max:20',
            'email'              => 'nullable|email|max:255',
            'address'            => 'required|string',
            'date_of_birth'      => 'nullable|date',
            'profession'         => 'nullable|string|max:255',
            'emergency_contact'  => 'nullable|string|max:255',
            'nominee_name'       => 'nullable|string|max:255',
            'nominee_contact'    => 'nullable|string|max:255',
            'is_existing_member' => 'boolean',
            'membership_date'    => 'nullable|date',
            'monthly_fee_amount' => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string|max:1000',
            'photo'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = $file->hashName();
            $dir = rtrim(config('filesystems.disks.public.root'), '/') . '/applications';
            @mkdir($dir, 0755, true);
            try {
                $file->move($dir, $filename);
                $data['photo'] = 'applications/' . $filename;
            } catch (\Exception $e) {
                \Log::error('Photo move failed: ' . $e->getMessage() . ' dir=' . $dir);
                unset($data['photo']);
            }
        } else {
            unset($data['photo']);
        }

        $data['is_existing_member'] = $request->boolean('is_existing_member');

        $application->update($data);
        AuditLog::record('application_updated', $application, [], $data, "Updated application data for {$application->full_name}");

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'Application updated.');
    }

    public function underReview(MembershipApplication $application)
    {
        if (!$application->isOpen()) {
            return back()->with('error', 'Application is already finalised.');
        }
        $application->update(['status' => 'under_review', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
        AuditLog::record('application_under_review', $application, [], [], "Marked under review: {$application->full_name}");
        return back()->with('success', 'Marked as Under Review.');
    }

    public function moreInfo(Request $request, MembershipApplication $application)
    {
        if (!$application->isOpen()) {
            return back()->with('error', 'Application is already finalised.');
        }
        $request->validate(['note' => 'nullable|string|max:500']);

        $note = $request->note
            ? "\n[" . now()->format('d M Y') . " – " . auth()->user()->name . "] More info requested: " . $request->note
            : null;

        $application->update([
            'status'         => 'more_info_required',
            'reviewed_by'    => auth()->id(),
            'reviewed_at'    => now(),
            'internal_notes' => trim(($application->internal_notes ?? '') . ($note ?? '')),
        ]);
        AuditLog::record('application_more_info', $application, [], [], "More info requested: {$application->full_name}");
        return back()->with('success', 'Marked as More Information Required.');
    }

    public function photoRequired(MembershipApplication $application)
    {
        if (!$application->isOpen()) {
            return back()->with('error', 'Application is already finalised.');
        }
        $application->update(['status' => 'photo_required', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
        AuditLog::record('application_photo_required', $application, [], [], "Photo requested: {$application->full_name}");
        return back()->with('success', 'Marked as Photo Required.');
    }

    public function addNote(Request $request, MembershipApplication $application)
    {
        $request->validate(['note' => 'required|string|max:1000']);

        $entry = "\n[" . now()->format('d M Y H:i') . " – " . auth()->user()->name . "] " . $request->note;
        $application->update([
            'internal_notes' => trim(($application->internal_notes ?? '') . $entry),
        ]);

        return back()->with('success', 'Internal note added.');
    }

    public function approve(Request $request, MembershipApplication $application)
    {
        if (!$application->isOpen()) {
            return back()->with('error', 'Application is already finalised.');
        }

        $request->validate([
            'review_remarks'     => 'nullable|string|max:500',
            'monthly_fee_amount' => 'required|numeric|min:0',
            'join_date'          => 'required|date',
        ]);

        DB::transaction(function () use ($application, $request) {
            $password = Str::random(10);

            $user = User::create([
                'name'              => $application->full_name,
                'email'             => $application->email ?? Str::slug($application->full_name) . '@unity.local',
                'password'          => Hash::make($password),
                'email_verified_at' => now(),
                'phone'             => $application->phone,
                'photo'             => $application->photo,
                'address'           => $application->address,
                'date_of_birth'     => $application->date_of_birth,
                'profession'        => $application->profession,
                'emergency_contact' => $application->emergency_contact,
                'nominee_name'      => $application->nominee_name,
                'nominee_contact'   => $application->nominee_contact,
            ]);

            Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
            $user->assignRole('member');

            $memberCount = Member::count() + 1;
            $member = Member::create([
                'user_id'            => $user->id,
                'application_id'     => $application->id,
                'member_number'      => 'UC-' . str_pad($memberCount, 4, '0', STR_PAD_LEFT),
                'join_date'          => $request->join_date,
                'monthly_fee_amount' => $request->monthly_fee_amount,
                'status'             => 'active',
                'created_by'         => auth()->id(),
            ]);

            $application->update([
                'status'             => 'approved',
                'review_remarks'     => $request->review_remarks,
                'reviewed_by'        => auth()->id(),
                'reviewed_at'        => now(),
                'user_id'            => $user->id,
                'monthly_fee_amount' => $request->monthly_fee_amount,
            ]);

            AuditLog::record('application_approved', $application, [], [], "Approved: {$application->full_name} → {$member->member_number}");

            if ($application->email) {
                try {
                    Mail::to($application->email)->send(new \App\Mail\WelcomeMember($user, $member, $password));
                } catch (\Exception $e) {
                    logger()->error('Welcome email failed: ' . $e->getMessage());
                }
            }
        });

        return redirect()->route('admin.applications.index')
            ->with('success', 'Application approved and member account created.');
    }

    public function reject(Request $request, MembershipApplication $application)
    {
        if (!$application->isOpen()) {
            return back()->with('error', 'Application is already finalised.');
        }

        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $application->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
        ]);

        AuditLog::record('application_rejected', $application, [], [], "Rejected: {$application->full_name}");

        return redirect()->route('admin.applications.index')
            ->with('success', 'Application rejected.');
    }
}
