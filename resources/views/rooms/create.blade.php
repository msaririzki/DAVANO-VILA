<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('rooms.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-950">Master kamar</a>
            <h2 class="mt-1 text-2xl font-semibold text-gray-950">Tambah kamar</h2>
        </div>
    </x-slot>

    <div class="bg-[#f6f4ef] py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('rooms.store') }}" enctype="multipart/form-data">
                @csrf
                @include('rooms._form', ['submitLabel' => 'Simpan kamar'])
            </form>
        </div>
    </div>
</x-app-layout>
