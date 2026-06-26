<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\ApplicationSubmitted;
use App\Mail\ApplicationSubmittedAdmin;
use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MembershipApplicationController extends Controller
{
    public function create()
    {
        return view('public.apply');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'          => 'required|string|max:255',
            'phone'              => 'required|string|max:20',
            'email'              => 'nullable|email|max:255',
            'address'            => 'required|string|max:1000',
            'date_of_birth'      => 'nullable|date|before:today',
            'profession'         => 'nullable|string|max:255',
            'emergency_contact'  => 'nullable|string|max:100',
            'nominee_name'       => 'nullable|string|max:255',
            'nominee_contact'    => 'nullable|string|max:100',
            'is_existing_member' => 'nullable|boolean',
            'membership_date'    => 'nullable|date',
            'monthly_fee_amount' => 'required|numeric|min:0',
            'notes'              => 'nullable|string|max:1000',
            'photo'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = $file->hashName();
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            $dir = $base . '/uploads/applications';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['photo'] = 'applications/' . $filename;
        }

        $data['is_existing_member'] = $request->boolean('is_existing_member');

        $application = MembershipApplication::create($data);

        // Email to applicant
        if ($application->email) {
            try {
                Mail::to($application->email)->send(new ApplicationSubmitted($application));
            } catch (\Exception $e) {
                logger()->error('Application submitted email (applicant) failed: ' . $e->getMessage());
            }
        }

        // Email to all admins and treasurers
        $this->notifyAdmins($application);

        return redirect()->route('apply.success')
            ->with('success', 'Your membership application has been submitted successfully. We will review it and contact you soon.');
    }

    public function success()
    {
        return view('public.apply-success');
    }

    private function notifyAdmins(MembershipApplication $application): void
    {
        $admins = User::role(['admin', 'treasurer'])
            ->whereNotNull('email')
            ->where('email', 'not like', '%@unity.local')
            ->get();

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new ApplicationSubmittedAdmin($application));
            } catch (\Exception $e) {
                logger()->error("Admin notification email failed for {$admin->email}: " . $e->getMessage());
            }
        }
    }
}
