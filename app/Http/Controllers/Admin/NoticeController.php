<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::with('publisher')->latest()->paginate(20);
        return view('admin.notices.index', compact('notices'));
    }

    public function create()
    {
        return view('admin.notices.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'type'         => 'required|in:notice,announcement,circular',
            'is_public'    => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $data['is_public']    = $request->boolean('is_public');
        $data['published_by'] = auth()->id();
        $data['published_at'] = $data['published_at'] ?? now();

        Notice::create($data);

        return redirect()->route('admin.notices.index')->with('success', 'Notice published.');
    }

    public function edit(Notice $notice)
    {
        return view('admin.notices.edit', compact('notice'));
    }

    public function update(Request $request, Notice $notice)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'type'         => 'required|in:notice,announcement,circular',
            'is_public'    => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $data['is_public'] = $request->boolean('is_public');
        $notice->update($data);

        return redirect()->route('admin.notices.index')->with('success', 'Notice updated.');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();
        return back()->with('success', 'Notice deleted.');
    }
}
