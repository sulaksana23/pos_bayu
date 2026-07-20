@extends('layouts.app')
@section('title', 'Edit Pelanggan')
@section('content')
<div class="mx-auto max-w-xl space-y-4">
    <div>
        <h1 class="text-base font-bold text-gray-900">Edit Pelanggan</h1>
        <p class="mt-0.5 text-xs text-gray-400">{{ $customer->name }}</p>
    </div>
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('pos.customers.update', $customer) }}">
            @csrf @method('PUT')
            @include('pos.customers._form')
        </form>
    </div>
</div>
@endsection
