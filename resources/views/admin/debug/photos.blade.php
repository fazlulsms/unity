@extends('layouts.app')
@section('title', 'Photo Debug')
@section('page-title', 'Photo Debug — Admin Only')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-4">

    <div class="alert-error">
        <strong>Temporary debug page.</strong> Delete the route <code>admin.debug.photos</code> after confirming photos work.
    </div>

    <div class="card">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm">Uploads base path on this server</p>
        </div>
        <div class="card-body">
            <code class="text-xs bg-gray-100 px-3 py-2 rounded block">{{ $base }}</code>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm">Member photo resolution ({{ count($rows) }} members)</p>
        </div>
        <div class="card-body p-0 overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Member</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">DB photo path</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Absolute path on disk</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">file_exists()</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Resolved URL / avatar</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Preview</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                    <tr class="border-b border-gray-100 {{ $row['db_path'] && !$row['file_exists'] ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-2">
                            <p class="font-medium text-gray-900">{{ $row['name'] }}</p>
                            <p class="text-gray-400 font-mono">{{ $row['number'] }}</p>
                        </td>
                        <td class="px-4 py-2">
                            @if($row['db_path'])
                                <code class="text-gray-700">{{ $row['db_path'] }}</code>
                            @else
                                <span class="text-gray-400 italic">null</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if($row['abs_path'])
                                <code class="text-gray-500 break-all">{{ $row['abs_path'] }}</code>
                            @else
                                <span class="text-gray-400 italic">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center">
                            @if(!$row['db_path'])
                                <span class="text-gray-400">n/a</span>
                            @elseif($row['file_exists'])
                                <span class="text-green-600 font-bold">✓ yes</span>
                            @else
                                <span class="text-red-600 font-bold">✗ no</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 max-w-xs break-all">
                            <code class="text-gray-600">{{ $row['resolved_url'] }}</code>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <img src="{{ $row['resolved_url'] }}"
                                 class="w-10 h-10 rounded-full object-cover border border-gray-200 inline-block"
                                 alt="">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <p class="text-xs text-gray-400">
        Rows highlighted in red: DB has a photo path but the file was not found on disk.
        Run <code>php artisan photos:migrate --dest=/path/to/public_html/uploads</code> to copy old files.
    </p>

</div>
@endsection
