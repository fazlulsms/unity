<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->role) {
            $query->role($request->role);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $users = $query->latest()->paginate(25);
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show(User $user)
    {
        $user->load('roles', 'member');
        return view('admin.users.show', compact('user'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $oldRoles = $user->getRoleNames()->implode(', ');
        $user->syncRoles([$request->role]);
        $newRole = $request->role;

        AuditLog::record('user_role_updated', $user, ['roles' => $oldRoles], ['role' => $newRole],
            "Role changed for {$user->name}: {$oldRoles} → {$newRole}");

        return back()->with('success', "Role updated to \"{$newRole}\" for {$user->name}.");
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        AuditLog::record('user_status_changed', $user, ['status' => $user->status], ['status' => $newStatus],
            "User {$user->name} status changed to {$newStatus}");

        return back()->with('success', "User account {$newStatus}.");
    }

    public function resetPassword(User $user)
    {
        if (str_ends_with($user->email, '@unity.local')) {
            return back()->with('error', 'This account has no real email address — cannot send password reset.');
        }

        $password = Str::random(12);
        $user->update([
            'password'             => Hash::make($password),
            'must_change_password' => true,
        ]);

        AuditLog::record('user_password_reset', $user, [], [], "Password manually reset for {$user->name}");

        // Email the new password to the user
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\PasswordReset($user, $password));
        } catch (\Exception $e) {
            logger()->error("Password reset email failed for {$user->email}: " . $e->getMessage());
        }

        return back()->with('success', "Password reset. A new temporary password has been emailed to {$user->email}.");
    }
}
