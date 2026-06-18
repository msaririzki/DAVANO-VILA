<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Sebelum melanjutkan, verifikasi alamat email dengan membuka tautan yang telah kami kirim. Jika email belum diterima, Anda dapat mengirim ulang tautannya.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            Tautan verifikasi baru telah dikirim ke alamat email Anda.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Kirim Ulang Email Verifikasi
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
