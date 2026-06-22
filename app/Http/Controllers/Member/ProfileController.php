<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

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
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            if ($user->photo) {
                $oldPath = $base . '/uploads/' . $user->photo;
                if (file_exists($oldPath)) @unlink($oldPath);
            }
            $file = $request->file('photo');
            $filename = $file->hashName();
            $dir = $base . '/uploads/profiles';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['photo'] = 'profiles/' . $filename;
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function profilePdf()
    {
        $user   = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->route('member.profile')->with('error', 'Member record not found.');
        }

        $photoData = null;
        $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
        $photoPath = $user->photo ? $base . '/uploads/' . $user->photo : null;

        if ($photoPath && file_exists($photoPath)) {
            $ext       = strtolower(pathinfo($photoPath, PATHINFO_EXTENSION));
            $mime      = $ext === 'png' ? 'image/png' : 'image/jpeg';
            $photoData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($photoPath));
        }

        $pdf = Pdf::loadView('admin.reports.member-profile-pdf', compact('member', 'photoData'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("my-member-profile-{$member->member_number}.pdf");
    }
}
