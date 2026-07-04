@extends ('layouts.app')
@section ('title','Tambah Produk')
@section ('content')
    <div class="mx-auto max-w-3xl space-y-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('pos.inventory.index') }}" class="rounded-lg p-2 hover:bg-gray-100"
                ><i class="fas fa-arrow-left text-gray-600"></i
            ></a>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Produk</h1>
        </div>
        @include ('pos.inventory._form', ['mode' => 'create'])
    </div>
@endsection
