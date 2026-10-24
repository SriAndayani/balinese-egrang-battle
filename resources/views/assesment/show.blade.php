<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masukkan Nama - {{ $room->nama_room }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-6">

    <div class="bg-white rounded-xl shadow p-8 w-full max-w-md">

        {{-- Nama room sebagai judul halaman --}}
        <h1 class="text-2xl font-bold text-gray-800 mb-1">
            {{ $room->nama_room }}
        </h1>
        <p class="text-gray-500 text-sm mb-6">
            Masukkan namamu untuk memulai kuis
        </p>

        <form action="{{ route('assesment.start', $room->id_room) }}"
              method="POST">
            @csrf

            <div class="mb-4">
                <label for="nama"
                       class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Siswa
                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="Tulis namamu di sini..."
                    autocomplete="off"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2
                           text-gray-800 focus:outline-none focus:ring-2
                           focus:ring-green-400
                           @error('nama') border-red-400 @enderror"
                    value="{{ old('nama') }}"
                >
                @error('nama')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="kode_room"
                       class="block text-sm font-medium text-gray-700 mb-1">
                    Kode Room
                </label>

                <input
                    type="text"
                    id="kode_room"
                    name="kode_room"
                    placeholder="Masukkan kode dari guru"
                    autocomplete="off"
                    maxlength="10"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2
                           text-gray-800 focus:outline-none focus:ring-2 
                           focus:ring-green-400
                           @error('kode_room') border-red-400 @enderror"
                >

                @error('kode_room')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-green-500 hover:bg-green-600 text-white
                           font-semibold py-2 rounded-lg transition">
                Mulai Kuis
            </button>

        {{-- Link kembali ke daftar room --}}
        <a href="{{ route('game.index') }}"
           class="block text-center text-sm text-gray-400
                  hover:text-gray-600 mt-4">
            ← Kembali ke daftar room
        </a>

    </div>

</body>
</html>