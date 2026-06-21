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

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = MembershipApplication::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(20);

        return view('admin.applications.index', compact('applications'));
    }

    public function show(MembershipApplication $application)
    {
        return view('admin.applications.show', compact('application'));
    }

    public function approve(Request $request, MembershipApplication $application)
    {
        if (!$application->isPending()) {
            return back()->with('error', 'Application is not pending.');
        }

        $request->validate([
            'review_remarks'    => 'nullable|string|max:500',
            'monthly_fee_amount' => 'required|numeric|min:0',
            'join_date'          => 'required|date',
        ]);

        DB::transaction(function () use ($application, $request) {
            $password = Str::random(10);

            $user = User::create([
                'name'     => $application->full_name,
                'email'    => $application->email ?? Str::slug($application->full_name) . '@unity.local',
                'password' => Hash::make($password),
                'phone'    => $application->phone,
                'photo'    => $application->photo,
                'address'  => $application->address,
                'date_of_birth'     => $application->date_of_birth,
                'profession'        => $application->profession,
                'emergency_contact' => $application->emergency_contact,
                'nominee_name'      => $application->nominee_name,
                'nominee_contact'   => $application->nominee_contact,
            ]);

            $user->assignRole('member');

            $memberCount = Member::count() + 1;
            $member = Member::create([
                'user_id'           => $user->id,
                'application_id'    => $application->id,
                'member_number'     => 'UC-' . str_pad($memberCount, 4, '0', STR_PAD_LEFT),
                'join_date'         => $request->join_date,
                'monthly_fee_amount' => $request->monthly_fee_amount,
                'status'            => 'active',
                'created_by'        => auth()->id(),
            ]);

            $application->update([
                'status'            => 'approved',
                'review_remarks'    => $request->review_remarks,
                'reviewed_by'       => auth()->id(),
                'reviewed_at'       => now(),
                'user_id'           => $user->id,
                'monthly_fee_amount' => $request->monthly_fee_amount,
            ]);

            AuditLog::record('application_approved', $application, [], [], "Approved application for {$application->full_name}");

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
        if (!$application->isPending()) {
            return back()->with('error', 'Application is not pending.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $application->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
        ]);

        AuditLog::record('application_rejected', $application, [], [], "Rejected application for {$application->full_name}");

        return redirect()->route('admin.applications.index')
            ->with('success', 'Application rejected.');
    }
}
