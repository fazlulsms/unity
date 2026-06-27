<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Member;
use App\Http\Controllers\Public as PublicCtrl;
use Illuminate\Support\Facades\Route;

// ─── Public Website ─────────────────────────────────────────────────────────
Route::get('/', [PublicCtrl\HomeController::class, 'index'])->name('home');
Route::get('/about', [PublicCtrl\HomeController::class, 'about'])->name('about');
Route::get('/contact', [PublicCtrl\HomeController::class, 'contact'])->name('contact');
Route::get('/events', [PublicCtrl\HomeController::class, 'events'])->name('events');
Route::get('/notices', [PublicCtrl\HomeController::class, 'notices'])->name('notices');
Route::get('/transparency', [PublicCtrl\HomeController::class, 'transparency'])->name('transparency');

Route::get('/apply', [PublicCtrl\MembershipApplicationController::class, 'create'])->name('apply');
Route::post('/apply', [PublicCtrl\MembershipApplicationController::class, 'store'])->name('apply.store');
Route::get('/apply/success', [PublicCtrl\MembershipApplicationController::class, 'success'])->name('apply.success');

// ─── Force Password Change ───────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'show'])->name('password.change');
    Route::post('/change-password', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'update'])->name('password.change.update');
});

// ─── Auth redirect ───────────────────────────────────────────────────────────
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isAdminOrTreasurer()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('member.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ─── Member Portal ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:member|admin|treasurer'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [Member\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notices', [Member\DashboardController::class, 'notices'])->name('notices');
    Route::get('/transparency', [Member\DashboardController::class, 'transparency'])->name('transparency');

    Route::get('/profile', [Member\ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [Member\ProfileController::class, 'update'])->name('profile.update');

    Route::get('/fees', [Member\FeeSubmissionController::class, 'index'])->name('fees.index');
    Route::get('/fees/submit', [Member\FeeSubmissionController::class, 'create'])->name('fees.create');
    Route::post('/fees', [Member\FeeSubmissionController::class, 'store'])->name('fees.store');
    Route::get('/fees/{submission}', [Member\FeeSubmissionController::class, 'show'])->name('fees.show');
    Route::get('/receipts/{receipt}/download', [Member\FeeSubmissionController::class, 'downloadReceipt'])->name('receipts.download');
    Route::get('/statement', [Member\FeeSubmissionController::class, 'statement'])->name('statement');
    Route::get('/profile-pdf', [Member\ProfileController::class, 'profilePdf'])->name('profile-pdf');
});

// ─── Admin / Treasurer Panel ─────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin|treasurer'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Membership Applications
    Route::get('/applications', [Admin\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [Admin\ApplicationController::class, 'show'])->name('applications.show');
    Route::get('/applications/{application}/edit', [Admin\ApplicationController::class, 'edit'])->name('applications.edit');
    Route::patch('/applications/{application}', [Admin\ApplicationController::class, 'update'])->name('applications.update');
    Route::post('/applications/{application}/approve', [Admin\ApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject', [Admin\ApplicationController::class, 'reject'])->name('applications.reject');
    Route::post('/applications/{application}/under-review', [Admin\ApplicationController::class, 'underReview'])->name('applications.under-review');
    Route::post('/applications/{application}/more-info', [Admin\ApplicationController::class, 'moreInfo'])->name('applications.more-info');
    Route::post('/applications/{application}/photo-required', [Admin\ApplicationController::class, 'photoRequired'])->name('applications.photo-required');
    Route::post('/applications/{application}/note', [Admin\ApplicationController::class, 'addNote'])->name('applications.note');

    // Members
    Route::get('/members', [Admin\MemberController::class, 'index'])->name('members.index');
    Route::get('/members/{member}/statement', [Admin\MemberController::class, 'statement'])->name('members.statement');
    Route::get('/members/{member}/profile-pdf', [Admin\MemberController::class, 'profilePdf'])->name('members.profile-pdf');
    Route::get('/members/{member}/history', [Admin\MemberController::class, 'history'])->name('members.history');
    Route::get('/members/{member}/emails', [Admin\MemberController::class, 'emails'])->name('members.emails');
    Route::get('/members/{member}', [Admin\MemberController::class, 'show'])->name('members.show');
    Route::get('/members/{member}/edit', [Admin\MemberController::class, 'edit'])->name('members.edit');
    Route::patch('/members/{member}', [Admin\MemberController::class, 'update'])->name('members.update');
    Route::post('/members/{member}/payment', [Admin\MemberController::class, 'addPayment'])->name('members.payment');
    Route::post('/members/{member}/deactivate', [Admin\MemberController::class, 'deactivate'])->name('members.deactivate');
    Route::post('/members/{member}/reactivate', [Admin\MemberController::class, 'reactivate'])->name('members.reactivate');

    // Collections
    Route::get('/collections',                        [Admin\CollectionController::class, 'index'])->name('collections.index');
    Route::get('/collections/create',                 [Admin\CollectionController::class, 'create'])->name('collections.create');
    Route::post('/collections',                       [Admin\CollectionController::class, 'store'])->name('collections.store');
    Route::get('/collections/bulk',                   [Admin\CollectionController::class, 'bulk'])->name('collections.bulk');
    Route::post('/collections/bulk',                  [Admin\CollectionController::class, 'bulkStore'])->name('collections.bulk-store');
    Route::get('/collections/due',                    [Admin\CollectionController::class, 'due'])->name('collections.due');
    Route::get('/collections/{collection}',           [Admin\CollectionController::class, 'show'])->name('collections.show');

    // Payment Approvals
    Route::get('/payments', [Admin\PaymentApprovalController::class, 'index'])->name('payments.index');
    Route::get('/payments/{submission}', [Admin\PaymentApprovalController::class, 'show'])->name('payments.show');
    Route::post('/payments/{submission}/approve', [Admin\PaymentApprovalController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{submission}/reject', [Admin\PaymentApprovalController::class, 'reject'])->name('payments.reject');

    // Expenses
    Route::resource('expenses', Admin\ExpenseController::class)->except(['destroy']);
    Route::post('/expenses/{expense}/void', [Admin\ExpenseController::class, 'void'])->name('expenses.void');

    // Income
    Route::resource('income', Admin\IncomeController::class)->except(['destroy']);
    Route::post('/income/{income}/void', [Admin\IncomeController::class, 'void'])->name('income.void');

    // FDR
    Route::resource('fdr', Admin\FdrController::class)->except(['destroy']);
    Route::get('/fdr/{fdr}/close', [Admin\FdrController::class, 'closeForm'])->name('fdr.close');
    Route::post('/fdr/{fdr}/close', [Admin\FdrController::class, 'close'])->name('fdr.close.store');

    // Notices
    Route::resource('notices', Admin\NoticeController::class);

    // Meeting Minutes
    Route::resource('meeting-minutes', Admin\MeetingMinuteController::class);

    // Email actions (manual sends)
    Route::post('/email/application/{application}/confirmation', [Admin\EmailController::class, 'sendApplicationConfirmation'])->name('email.application.confirmation');
    Route::post('/email/member/{member}/welcome', [Admin\EmailController::class, 'sendMemberWelcome'])->name('email.member.welcome');
    Route::post('/email/member/{member}/access', [Admin\EmailController::class, 'sendLoginAccess'])->name('email.member.access');
    Route::post('/email/member/{member}/reminder', [Admin\EmailController::class, 'sendPaymentReminder'])->name('email.member.reminder');
    Route::post('/email/receipt/{receipt}/resend', [Admin\EmailController::class, 'resendReceipt'])->name('email.receipt.resend');

    // Users
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/role', [Admin\UserController::class, 'updateRole'])->name('users.role');
    Route::patch('/users/{user}/toggle-status', [Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [Admin\UserController::class, 'resetPassword'])->name('users.reset-password');

    // Additional Info & Family
    Route::get('/members/{member}/additional-info', [Admin\MemberAdditionalInfoController::class, 'show'])->name('members.additional-info.show');
    Route::get('/members/{member}/additional-info/edit', [Admin\MemberAdditionalInfoController::class, 'edit'])->name('members.additional-info.edit');
    Route::patch('/members/{member}/additional-info', [Admin\MemberAdditionalInfoController::class, 'update'])->name('members.additional-info.update');
    Route::get('/members/{member}/family/create', [Admin\MemberAdditionalInfoController::class, 'createFamily'])->name('members.family.create');
    Route::post('/members/{member}/family', [Admin\MemberAdditionalInfoController::class, 'storeFamily'])->name('members.family.store');
    Route::get('/members/{member}/family/{family}/edit', [Admin\MemberAdditionalInfoController::class, 'editFamily'])->name('members.family.edit');
    Route::patch('/members/{member}/family/{family}', [Admin\MemberAdditionalInfoController::class, 'updateFamily'])->name('members.family.update');
    Route::delete('/members/{member}/family/{family}', [Admin\MemberAdditionalInfoController::class, 'destroyFamily'])->name('members.family.destroy');

    // Reports
    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/members', [Admin\ReportController::class, 'members'])->name('reports.members');
    Route::get('/reports/collections', [Admin\ReportController::class, 'collections'])->name('reports.collections');
    Route::get('/reports/dues', [Admin\ReportController::class, 'dues'])->name('reports.dues');
    Route::get('/reports/expenses', [Admin\ReportController::class, 'expenses'])->name('reports.expenses');
    Route::get('/reports/annual', [Admin\ReportController::class, 'annual'])->name('reports.annual');
    Route::get('/reports/occasions', [Admin\ReportController::class, 'occasions'])->name('reports.occasions');
});

require __DIR__ . '/auth.php';
