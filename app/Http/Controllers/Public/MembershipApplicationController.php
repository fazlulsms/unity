<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\ApplicationSubmitted;
use App\Mail\ApplicationSubmittedAdmin;
use App\Models\MembershipApplication;
use App\Support\MailHelper;
use Illuminate\Http\Request;

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

        if (MailHelper::validEmail($application->email)) {
            MailHelper::send(
                $application->email, $application->full_name,
                new ApplicationSubmitted($application),
                $application
            );
        }

        MailHelper::sendToAdmins(
            fn() => new ApplicationSubmittedAdmin($application),
            $application
        );

        return redirect()->route('apply.success')
            ->with('success', 'Your membership application has been submitted successfully. We will review it and contact you soon.');
    }

    public function success()
    {
        return view('public.apply-success');
    }
}
