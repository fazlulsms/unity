{{-- Email Log partial — pass $emailLogs (collection) --}}
<div class="card overflow-hidden">
    <div class="card-header">
        <p class="font-semibold text-gray-800 text-sm"><i class="fas fa-envelope text-gray-400 mr-1.5"></i> Email History</p>
        <span class="text-xs text-gray-400">{{ $emailLogs->count() }} sent</span>
    </div>
    @if($emailLogs->isEmpty())
    <div class="px-5 py-8 text-center text-gray-400 text-sm">
        <i class="fas fa-envelope-open text-2xl mb-2 block"></i>
        No emails sent yet.
    </div>
    @else
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="th">Recipient</th>
                <th class="th hidden sm:table-cell">Subject</th>
                <th class="th">Status</th>
                <th class="th hidden md:table-cell">Sent By</th>
                <th class="th text-right">When</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($emailLogs as $log)
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="td">
                    <p class="text-gray-800 font-medium text-xs">{{ $log->to_name ?? $log->to_email }}</p>
                    <p class="text-gray-400 text-xs">{{ $log->to_email }}</p>
                </td>
                <td class="td hidden sm:table-cell text-gray-600 text-xs max-w-xs truncate">{{ $log->subject }}</td>
                <td class="td">
                    @if($log->status === 'sent')
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 ring-1 ring-emerald-200 px-2 py-0.5 rounded-full">
                            <i class="fas fa-check text-[9px]"></i> Sent
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-red-700 bg-red-50 ring-1 ring-red-200 px-2 py-0.5 rounded-full"
                              title="{{ $log->error_message }}">
                            <i class="fas fa-times text-[9px]"></i> Failed
                        </span>
                    @endif
                </td>
                <td class="td hidden md:table-cell text-gray-500 text-xs">
                    {{ $log->sender?->name ?? 'System' }}
                </td>
                <td class="td text-right text-gray-400 text-xs whitespace-nowrap">
                    {{ $log->created_at->format('d M Y H:i') }}
                </td>
            </tr>
            @if($log->status === 'failed' && $log->error_message)
            <tr class="bg-red-50/50">
                <td colspan="5" class="px-5 py-2">
                    <p class="text-xs text-red-600 font-mono">Error: {{ Str::limit($log->error_message, 120) }}</p>
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif
</div>
