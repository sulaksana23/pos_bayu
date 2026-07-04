@php
    $map = [
        'success' => ['bg-emerald-50','text-emerald-800','border-emerald-200','fa-circle-check','text-emerald-500'],
        'warning' => ['bg-amber-50','text-amber-800','border-amber-200','fa-triangle-exclamation','text-amber-500'],
        'info'    => ['bg-blue-50','text-blue-800','border-blue-200','fa-circle-info','text-blue-500'],
        'error'   => ['bg-red-50','text-red-800','border-red-200','fa-circle-xmark','text-red-500'],
    ];
    [$bg,$tx,$bd,$icon,$ic] = $map[$type] ?? $map['info'];
@endphp
<div
    class="mb-4 rounded-xl border {{ $bg }} {{ $bd }} {{ $tx }} p-3 flex items-start gap-3"
    role="alert"
>
    <i class="fas {{ $icon }} {{ $ic }} mt-0.5"></i>
    <p class="flex-1 text-sm">{{ $message }}</p>
    <button onclick="this.parentElement.remove()" class="text-current/60 hover:opacity-80">
        <i class="fas fa-times text-xs"></i>
    </button>
</div>
