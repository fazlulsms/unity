<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberAdditionalInfo;
use App\Models\MemberProfileHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdditionalInfoController extends Controller
{
    public function show()
    {
        $member = Auth::user()->member;
        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }
        $member->load(['additionalInfo', 'familyMembers']);

        return view('member.additional-info.show', compact('member'));
    }

    public function edit()
    {
        $member = Auth::user()->member;
        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }
        $member->load('additionalInfo');

        return view('member.additional-info.edit', compact('member'));
    }

    public function update(Request $request)
    {
        $member = Auth::user()->member;
        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }

        $validated = $request->validate([
            'present_address'                  => 'nullable|string|max:1000',
            'permanent_address'                => 'nullable|string|max:1000',
            'business_address'                 => 'nullable|string|max:1000',
            'primary_emergency_name'           => 'nullable|string|max:255',
            'primary_emergency_relationship'   => 'nullable|string|max:100',
            'primary_emergency_phone'          => 'nullable|string|max:30',
            'secondary_emergency_name'         => 'nullable|string|max:255',
            'secondary_emergency_relationship' => 'nullable|string|max:100',
            'secondary_emergency_phone'        => 'nullable|string|max:30',
            'marital_status'                   => 'nullable|in:single,married,divorced,widowed',
            'religion'                         => 'nullable|string|max:100',
            'marriage_anniversary'             => 'nullable|date',
            'blood_group'                      => 'nullable|string|max:5',
            'nationality'                      => 'nullable|string|max:100',
            'nid_passport'                     => 'nullable|string|max:50',
            'notes'                            => 'nullable|string|max:2000',
        ]);

        $existing = $member->additionalInfo;

        // Mirror the admin field labels so the change history reads consistently.
        $fieldLabels = [
            'present_address'                  => 'Present Address',
            'permanent_address'                => 'Permanent Address',
            'business_address'                 => 'Business Address',
            'primary_emergency_name'           => 'Primary Emergency Name',
            'primary_emergency_relationship'   => 'Primary Emergency Relationship',
            'primary_emergency_phone'          => 'Primary Emergency Phone',
            'secondary_emergency_name'         => 'Secondary Emergency Name',
            'secondary_emergency_relationship' => 'Secondary Emergency Relationship',
            'secondary_emergency_phone'        => 'Secondary Emergency Phone',
            'marital_status'                   => 'Marital Status',
            'religion'                         => 'Religion',
            'marriage_anniversary'             => 'Marriage Anniversary',
            'blood_group'                      => 'Blood Group',
            'nationality'                      => 'Nationality',
            'nid_passport'                     => 'NID / Passport',
            'notes'                            => 'Notes',
        ];

        $changes = [];
        foreach ($fieldLabels as $field => $label) {
            $oldValue = $existing ? (string) ($existing->$field ?? '') : '';
            $newValue = (string) ($validated[$field] ?? '');
            if ($oldValue !== $newValue) {
                $changes[$label] = ['old' => $oldValue, 'new' => $newValue];
            }
        }

        MemberAdditionalInfo::updateOrCreate(['member_id' => $member->id], $validated);

        if (!empty($changes)) {
            MemberProfileHistory::create([
                'member_id'  => $member->id,
                'changes'    => $changes,
                'updated_by' => Auth::id(),
            ]);
        }

        return redirect()->route('member.additional-info.show')
            ->with('success', 'Your additional information has been updated.');
    }
}
