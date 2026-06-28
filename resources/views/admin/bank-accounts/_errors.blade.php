@if($errors->any())
<div class="alert-error mb-4">
    <i class="fas fa-circle-exclamation text-red-500 mt-0.5 shrink-0"></i>
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
