@extends('layouts.app')
@section('title', 'Tambah Pelanggan')
@section('content')
<div class="mx-auto max-w-xl space-y-4">
    <div>
        <h1 class="text-base font-bold text-gray-900">Tambah Pelanggan</h1>
        <p class="mt-0.5 text-xs text-gray-400">Isi data pelanggan baru</p>
    </div>
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('pos.customers.store') }}">
            @csrf
            @include('pos.customers._form')
        </form>
    </div>
</div>
@endsection
