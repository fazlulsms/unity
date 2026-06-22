@extends('layouts.app')
@section('title', 'Membership Applications')
@section('page-title', 'Membership Applications')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5">

    {{-- Status tab strip --}}
    <div class="flex flex-wrap gap-2">
        @foreach([
            ''                   => ['label' => 'All',              'count' => $counts['all']],
            'pending'            => ['label' => 'Pending',          'count' => $counts['pending']],
            'under_review'       => ['label' => 'Under Review',     'count' => $counts['under_review']],
            'more_info_required' => ['label' => 'More Info Needed', 'count' => $counts['more_info_required']],
            'photo_required'     => ['label' => 'Photo Required',   'count' => $counts['photo_required']],
            'approved'           => ['label' => 'Approved',         'count' => $counts['approved']],
            'rejected'           => ['label' => 'Rejected',         'count' => $counts['rejected']],
        ] as $value => $tab)
        @php $active = request('status', '') === $value; @endphp
        <a href="{{ route('admin.applications.index', $value ? ['status' => $value] : []) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors
                  {{ $active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-600' }}">
            {{ $tab['label'] }}
            <span class="{{ $active ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600' }} px-1.5 py-0.5 rounded-full text-xs">{{ $tab['count'] }}</span>
        </a>
        @endforeach
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    <div class="table-wrap">
        <div class="card-header">
            <div>
                <p class="page-heading">Applications</p>
                <p class="page-subhead">{{ $applications->total() }} total</p>
            </div>
        </div>

        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="th">Applicant</th>
                    <th class="th hidden sm:table-cell">Phone</th>
                    <th class="th hidden md:table-cell">Email</th>
                    <th class="th hidden lg:table-cell">Applied</th>
                    <th class="th">Status</th>
                    <th class="th text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $application)
                <tr class="tr">
                    <td class="td">
                        <div class="flex items-center gap-3">
                            @if($application->photo_url)
                                <img src="{{ $application->photo_url }}" class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0" alt="">
                            @else
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                    <i class="fas fa-user text-gray-400 text-xs"></i>
                                </div>
                            @endif
                            <span class="font-medium text-gray-900">{{ $application->full_name }}</span>
                        </div>
                    </td>
                    <td class="td hidden sm:table-cell text-gray-600">{{ $application->phone }}</td>
                    <td class="td hidden md:table-cell text-gray-500 text-xs">{{ $application->email ?? '—' }}</td>
                    <td class="td hidden lg:table-cell text-gray-500 text-xs">{{ $application->created_at->format('d M Y') }}</td>
                    <td class="td">
                        <span class="{{ $application->statusClass() }}">{{ $application->statusLabel() }}</span>
                    </td>
                    <td class="td text-right">
                        <a href="{{ route('admin.applications.show', $application) }}" class="btn btn-sm btn-primary">
                            Review
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="table-empty">No applications found.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($applications->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $applications->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
