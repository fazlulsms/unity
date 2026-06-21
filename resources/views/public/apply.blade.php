@extends('layouts.public')
@section('title', 'Apply for Membership — Unity Circle')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Membership Application</h1>
        <p class="text-gray-500 mt-2">Fill out the form below. Our admin team will review and contact you.</p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <ul class="text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $error)
            <li><i class="fas fa-exclamation-circle mr-1"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('apply.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 space-y-6">
        @csrf

        <h2 class="text-base font-semibold text-gray-900 border-b pb-2">Personal Information</h2>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('full_name') border-red-400 @enderror">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('phone') border-red-400 @enderror">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <p class="text-xs text-gray-400 mt-1">Used for login and receipt delivery</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                <textarea name="address" rows="2" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('address') border-red-400 @enderror">{{ old('address') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Profession</label>
                <input type="text" name="profession" value="{{ old('profession') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact</label>
                <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
        </div>

        <h2 class="text-base font-semibold text-gray-900 border-b pb-2 pt-2">Nominee Details (Optional)</h2>
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominee Name</label>
                <input type="text" name="nominee_name" value="{{ old('nominee_name') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominee Contact</label>
                <input type="text" name="nominee_contact" value="{{ old('nominee_contact') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
        </div>

        <h2 class="text-base font-semibold text-gray-900 border-b pb-2 pt-2">Membership Details</h2>
        <div class="grid sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_existing_member" value="1" {{ old('is_existing_member') ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 rounded border-gray-300">
                    <span class="text-sm font-medium text-gray-700">I am already an existing member</span>
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Membership / Joining Date</label>
                <input type="date" name="membership_date" value="{{ old('membership_date') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Fee Amount (৳) <span class="text-red-500">*</span></label>
                <input type="number" name="monthly_fee_amount" value="{{ old('monthly_fee_amount', 500) }}" required min="0"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('monthly_fee_amount') border-red-400 @enderror">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <p class="text-xs text-gray-400 mt-1">JPG or PNG, max 2MB</p>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100">
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                Submit Membership Application
            </button>
            <p class="text-xs text-gray-400 text-center mt-3">
                Your application will be reviewed by our admin team. You will be notified of the decision.
            </p>
        </div>
    </form>
</div>
@endsection
