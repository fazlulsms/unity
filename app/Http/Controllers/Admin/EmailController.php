<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ApplicationSubmitted;
use App\Mail\MemberAccessEmail;
use App\Mail\PaymentReceipt;
use App\Mail\PaymentReminder;
use App\Mail\WelcomeMember;
use App\Models\Member;
use App\Models\MembershipApplication;
use App\Models\MonthlyFeeSubmission;
use App\Models\Receipt;
use App\Support\MailHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class EmailController extends Controller
{
    /** Resend application received confirmation to applicant */
    public function sendApplicationConfirmation(MembershipApplication $application)
    {
        if (!MailHelper::validEmail($application->email)) {
            return back()->with('error', 'No valid email address on this application.');
        }

        $sent = MailHelper::send(
            $application->email, $application->full_name,
            new ApplicationSubmitted($application),
            $application, auth()->id()
        );

        return back()->with($sent ? 'success' : 'error',
            $sent ? 'Application confirmation email sent.' : 'Email failed — check logs.');
    }

    /** Resend welcome/approval email with fresh setup link */
    public function sendMemberWelcome(Member $member)
    {
        $user = $member->user;

        if (!MailHelper::validEmail($user->email)) {
            return back()->with('error', 'No valid email address for this member.');
        }

        $token = Password::broker()->createToken($user);
        $setupUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        $sent = MailHelper::send(
            $user->email, $user->name,
            new WelcomeMember($user, $member, $setupUrl),
            $member->application ?? $member, auth()->id()
        );

        return back()->with($sent ? 'success' : 'error',
            $sent ? 'Welcome email with login setup link sent.' : 'Email failed — check logs.');
    }

    /** Send login access link (password reset) */
    public function sendLoginAccess(Member $member)
    {
        $user = $member->user;

        if (!MailHelper::validEmail($user->email)) {
            return back()->with('error', 'No valid email address for this member.');
        }

        $token = Password::broker()->createToken($user);
        $setupUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        $sent = MailHelper::send(
            $user->email, $user->name,
            new MemberAccessEmail($user, $setupUrl),
            $member, auth()->id()
        );

        return back()->with($sent ? 'success' : 'error',
            $sent ? 'Login access email sent.' : 'Email failed — check logs.');
    }

    /** Send payment reminder */
    public function sendPaymentReminder(Request $request, Member $member)
    {
        $user = $member->user;

        if (!MailHelper::validEmail($user->email)) {
            return back()->with('error', 'No valid email address for this member.');
        }

        $sent = MailHelper::send(
            $user->email, $user->name,
            new PaymentReminder($member, $request->admin_message),
            $member, auth()->id()
        );

        return back()->with($sent ? 'success' : 'error',
            $sent ? 'Payment reminder email sent.' : 'Email failed — check logs.');
    }

    /** Resend receipt to member */
    public function resendReceipt(Receipt $receipt)
    {
        $member = $receipt->member;
        $user = $member->user;

        if (!MailHelper::validEmail($user->email)) {
            return back()->with('error', 'No valid email address for this member.');
        }

        $sent = MailHelper::send(
            $user->email, $user->name,
            new PaymentReceipt($receipt),
            $member->feeSubmissions()->whereHas('receipt', fn($q) => $q->where('id', $receipt->id))->first() ?? $member,
            auth()->id()
        );

        return back()->with($sent ? 'success' : 'error',
            $sent ? 'Receipt email re-sent.' : 'Email failed — check logs.');
    }
}
