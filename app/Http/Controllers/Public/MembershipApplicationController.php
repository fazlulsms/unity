<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\MembershipApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            $data['photo'] = $request->file('photo')->store('applications', 'public');
        }

        $data['is_existing_member'] = $request->boolean('is_existing_member');

        MembershipApplication::create($data);

        return redirect()->route('apply.success')
            ->with('success', 'Your membership application has been submitted successfully. We will review it and contact you soon.');
    }

    public function success()
    {
        return view('public.apply-success');
    }
}
