<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingMinute;
use Illuminate\Http\Request;

class MeetingMinuteController extends Controller
{
    public function index()
    {
        $minutes = MeetingMinute::with('creator')->latest('meeting_date')->paginate(20);
        return view('admin.meeting-minutes.index', compact('minutes'));
    }

    public function create()
    {
        return view('admin.meeting-minutes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'content'      => 'required|string',
            'attachment'   => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:10240',
            'is_public'    => 'nullable|boolean',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = $file->hashName();
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            $dir = $base . '/uploads/minutes';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['attachment'] = 'minutes/' . $filename;
        }

        $data['is_public']  = $request->boolean('is_public');
        $data['created_by'] = auth()->id();

        MeetingMinute::create($data);

        return redirect()->route('admin.meeting-minutes.index')->with('success', 'Meeting minutes saved.');
    }

    public function edit(MeetingMinute $meetingMinute)
    {
        return view('admin.meeting-minutes.edit', compact('meetingMinute'));
    }

    public function update(Request $request, MeetingMinute $meetingMinute)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'content'      => 'required|string',
            'attachment'   => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:10240',
            'is_public'    => 'nullable|boolean',
        ]);

        if ($request->hasFile('attachment')) {
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            if ($meetingMinute->attachment) {
                $oldPath = $base . '/uploads/' . $meetingMinute->attachment;
                if (file_exists($oldPath)) @unlink($oldPath);
            }
            $file = $request->file('attachment');
            $filename = $file->hashName();
            $dir = $base . '/uploads/minutes';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['attachment'] = 'minutes/' . $filename;
        }

        $data['is_public'] = $request->boolean('is_public');
        $meetingMinute->update($data);

        return redirect()->route('admin.meeting-minutes.index')->with('success', 'Meeting minutes updated.');
    }

    public function destroy(MeetingMinute $meetingMinute)
    {
        if ($meetingMinute->attachment) {
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            $oldPath = $base . '/uploads/' . $meetingMinute->attachment;
            if (file_exists($oldPath)) @unlink($oldPath);
        }
        $meetingMinute->delete();
        return back()->with('success', 'Meeting minutes deleted.');
    }
}
