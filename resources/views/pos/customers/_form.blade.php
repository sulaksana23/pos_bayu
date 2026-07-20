{{-- Shared form partial untuk create & edit pelanggan --}}
<div class="space-y-4">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="mb-1 block text-xs font-semibold text-gray-700">Nama Pelanggan <span class="text-red-500">*</span></label>
            <input name="name" type="text" required value="{{ old('name', $customer->name ?? '') }}"
                placeholder="Nama lengkap"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none @error('name') border-red-400 @enderror" />
            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-700">No. Telepon</label>
            <input name="phone" type="text" value="{{ old('phone', $customer->phone ?? '') }}"
                placeholder="08xxxxxxxxxx"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none @error('phone') border-red-400 @enderror" />
            @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-700">Email</label>
            <input name="email" type="email" value="{{ old('email', $customer->email ?? '') }}"
                placeholder="email@contoh.com"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none @error('email') border-red-400 @enderror" />
            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-xs font-semibold text-gray-700">Alamat</label>
            <textarea name="address" rows="2" placeholder="Alamat lengkap (opsional)"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none resize-none @error('address') border-red-400 @enderror">{{ old('address', $customer->address ?? '') }}</textarea>
            @error('address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-xs font-semibold text-gray-700">Catatan</label>
            <textarea name="notes" rows="2" placeholder="Catatan tambahan (opsional)"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none resize-none">{{ old('notes', $customer->notes ?? '') }}</textarea>
        </div>
    </div>
    <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
        <a href="{{ route('pos.customers.index') }}"
            class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm text-gray-600 hover:bg-gray-50 transition">Batal</a>
        <button type="submit"
            class="rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-orange-500/30 hover:shadow-lg transition">
            {{ isset($customer) ? 'Simpan Perubahan' : 'Tambah Pelanggan' }}
        </button>
    </div>
</div>
