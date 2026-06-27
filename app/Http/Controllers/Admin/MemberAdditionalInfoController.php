<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberAdditionalInfo;
use App\Models\MemberFamilyMember;
use App\Models\MemberProfileHistory;
use App\Traits\ResolvesUploadedPhoto;
use Illuminate\Http\Request;

class MemberAdditionalInfoController extends Controller
{
    use ResolvesUploadedPhoto;

    public function show(Member $member)
    {
        $member->load(['additionalInfo', 'familyMembers', 'user']);
        return view('admin.members.additional-info.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $member->load(['additionalInfo', 'user']);
        return view('admin.members.additional-info.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'present_address'                => 'nullable|string|max:1000',
            'permanent_address'              => 'nullable|string|max:1000',
            'business_address'               => 'nullable|string|max:1000',
            'primary_emergency_name'         => 'nullable|string|max:255',
            'primary_emergency_relationship' => 'nullable|string|max:100',
            'primary_emergency_phone'        => 'nullable|string|max:30',
            'secondary_emergency_name'       => 'nullable|string|max:255',
            'secondary_emergency_relationship' => 'nullable|string|max:100',
            'secondary_emergency_phone'      => 'nullable|string|max:30',
            'marital_status'                 => 'nullable|in:single,married,divorced,widowed',
            'religion'                       => 'nullable|string|max:100',
            'marriage_anniversary'           => 'nullable|date',
            'blood_group'                    => 'nullable|string|max:5',
            'nationality'                    => 'nullable|string|max:100',
            'nid_passport'                   => 'nullable|string|max:50',
            'notes'                          => 'nullable|string|max:2000',
        ]);

        $existing = $member->additionalInfo;

        // Build history changes
        $fieldLabels = [
            'present_address'                => 'Present Address',
            'permanent_address'              => 'Permanent Address',
            'business_address'               => 'Business Address',
            'primary_emergency_name'         => 'Primary Emergency Name',
            'primary_emergency_relationship' => 'Primary Emergency Relationship',
            'primary_emergency_phone'        => 'Primary Emergency Phone',
            'secondary_emergency_name'       => 'Secondary Emergency Name',
            'secondary_emergency_relationship' => 'Secondary Emergency Relationship',
            'secondary_emergency_phone'      => 'Secondary Emergency Phone',
            'marital_status'                 => 'Marital Status',
            'religion'                       => 'Religion',
            'marriage_anniversary'           => 'Marriage Anniversary',
            'blood_group'                    => 'Blood Group',
            'nationality'                    => 'Nationality',
            'nid_passport'                   => 'NID / Passport',
            'notes'                          => 'Notes',
        ];

        $changes = [];
        foreach ($fieldLabels as $field => $label) {
            $oldValue = $existing ? (string) ($existing->$field ?? '') : '';
            $newValue = (string) ($validated[$field] ?? '');
            if ($oldValue !== $newValue) {
                $changes[$label] = ['old' => $oldValue, 'new' => $newValue];
            }
        }

        MemberAdditionalInfo::updateOrCreate(
            ['member_id' => $member->id],
            $validated
        );

        if (!empty($changes)) {
            MemberProfileHistory::create([
                'member_id'  => $member->id,
                'changes'    => $changes,
                'updated_by' => auth()->id(),
            ]);
        }

        return redirect()->route('admin.members.additional-info.show', $member)
            ->with('success', 'Additional information updated successfully.');
    }

    public function createFamily(Member $member)
    {
        $member->load('user');
        return view('admin.members.additional-info.family-create', compact('member'));
    }

    public function storeFamily(Request $request, Member $member)
    {
        $validated = $request->validate([
            'type'         => 'required|in:spouse,child,father,mother,sibling,other',
            'relationship' => 'nullable|string|max:100',
            'name'         => 'required|string|max:255',
            'sex'          => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'profession'   => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:255',
            'photo'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes'        => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $this->uploadPhoto($request->file('photo'), 'family');
        }

        $member->familyMembers()->create($validated);

        return redirect()->route('admin.members.additional-info.show', $member)
            ->with('success', 'Family member added successfully.');
    }

    public function editFamily(Member $member, MemberFamilyMember $family)
    {
        $member->load('user');
        return view('admin.members.additional-info.family-edit', compact('member', 'family'));
    }

    public function updateFamily(Request $request, Member $member, MemberFamilyMember $family)
    {
        $validated = $request->validate([
            'type'         => 'required|in:spouse,child,father,mother,sibling,other',
            'relationship' => 'nullable|string|max:100',
            'name'         => 'required|string|max:255',
            'sex'          => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'profession'   => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:255',
            'photo'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes'        => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($family->photo) {
                $oldPath = static::uploadsBase() . '/uploads/' . $family->photo;
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $validated['photo'] = $this->uploadPhoto($request->file('photo'), 'family');
        }

        $family->update($validated);

        return redirect()->route('admin.members.additional-info.show', $member)
            ->with('success', 'Family member updated successfully.');
    }

    public function destroyFamily(Member $member, MemberFamilyMember $family)
    {
        if ($family->photo) {
            $oldPath = static::uploadsBase() . '/uploads/' . $family->photo;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $family->delete();

        return redirect()->route('admin.members.additional-info.show', $member)
            ->with('success', 'Family member removed.');
    }

    private function uploadPhoto($file, string $subdir): string
    {
        $uploadsDir = static::uploadsBase() . '/uploads/' . $subdir . '/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0775, true);
        }
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadsDir, $filename);
        return $subdir . '/' . $filename;
    }
}
