@extends('layouts.app')
@section('title', 'Edit Bank Account')
@section('page-title', 'Edit Bank Account')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        @include('admin.bank-accounts._errors')
        <form action="{{ route('admin.bank-accounts.update', $bankAccount) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            @include('admin.bank-accounts._fields', ['account' => $bankAccount])
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm">Update Account</button>
                <a href="{{ route('admin.bank-accounts.show', $bankAccount) }}" class="flex-1 text-center border border-gray-300 text-gray-700 py-2.5 rounded-lg text-sm hover:bg-gray-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
