<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('member.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'phone'             => 'nullable|string|max:20',
            'address'           => 'nullable|string|max:1000',
            'profession'        => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:100',
            'nominee_name'      => 'nullable|string|max:255',
            'nominee_contact'   => 'nullable|string|max:100',
            'photo'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('profiles', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}
