<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battle Mode — {{ $room->nama_room }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-900
             via-purple-900 to-indigo-800
             flex items-center justify-center p-6">

    <div class="w-full max-w-2xl">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="text-6xl mb-4">⚔️</div>
            <h1 class="text-4xl font-black text-white tracking-wide">
                BATTLE MODE
            </h1>
            <p class="text-purple-300 mt-2 text-lg">
                {{ $room->nama_room }}
            </p>
        </div>

        @if (session('error'))
            <div class="bg-red-500 text-white px-4 py-3
                        rounded-xl mb-6 text-center font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('battle.start', $room->id_room) }}"
              method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-6 mb-8">

                {{-- Player A --}}
                <div class="bg-blue-500 bg-opacity-20 border-2
                            border-blue-400 rounded-2xl p-6 text-center">
                    <div class="text-5xl mb-3">🧖‍♂️</div>
                    <h2 class="text-xl font-bold text-blue-300 mb-4">
                        PLAYER A
                    </h2>
                    <input
                        type="text"
                        name="nama_a"
                        placeholder="Nama Player A"
                        autocomplete="off"
                        value="{{ old('nama_a') }}"
                        class="w-full bg-white bg-opacity-10
                               border-2 border-blue-400 rounded-xl
                               px-4 py-3 text-white text-center
                               text-lg font-semibold placeholder-blue-300
                               focus:outline-none focus:border-white">
                    @error('nama_a')
                        <p class="text-red-400 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Player B --}}
                <div class="bg-pink-400 bg-opacity-20 border-2
                            border-pink-300 rounded-2xl p-6 text-center">
                    <div class="text-5xl mb-3">🧖</div>
                    <h2 class="text-xl font-bold text-red-300 mb-4">
                        PLAYER B
                    </h2>
                    <input
                        type="text"
                        name="nama_b"
                        placeholder="Nama Player B"
                        autocomplete="off"
                        value="{{ old('nama_b') }}"
                        class="w-full bg-white bg-opacity-10
                               border-2 border-red-400 rounded-xl
                               px-4 py-3 text-white text-center
                               text-lg font-semibold placeholder-red-300
                               focus:outline-none focus:border-white">
                    @error('nama_b')
                        <p class="text-red-400 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            <button type="submit"
                    class="w-full bg-yellow-400 hover:bg-yellow-300
                           text-gray-900 font-black text-2xl py-5
                           rounded-2xl transition shadow-lg
                           shadow-yellow-500/30 tracking-wide">
                MULAI BATTLE!
            </button>

        </form>

        <a href="{{ route('game.index') }}"
           class="block text-center text-purple-400
                  hover:text-white mt-6 text-sm transition">
            ← Kembali ke daftar room
        </a>

    </div>

</body>
</html>