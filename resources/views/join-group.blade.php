<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join UKM - MyUKM</title>
    @vite('resources/js/app.js')
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-600 to-indigo-700 flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-purple-600">Gabung UKM</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 mb-4 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('join.group') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kode Referal UKM
                </label>
                <select 
                    name="group_code" 
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    required
                >
                    <option value="" disabled selected>-- Pilih UKM --</option>
                    @foreach($groupCodes as $code => $name)
                        <option value="{{ $code }}" {{ in_array($code, $alreadyJoined) ? 'disabled' : '' }}>
                            {{ $name }} ({{ $code }}) {{ in_array($code, $alreadyJoined) ? '- Sudah Tergabung' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('group_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button 
                type="submit" 
                class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors"
            >
                Gabung UKM
            </button>
        </form>

        @if(!empty($alreadyJoined))
            <div class="mt-6">
                <h3 class="font-medium text-gray-700 mb-2">UKM Yang Sudah Diikuti:</h3>
                <div class="bg-gray-50 rounded-lg p-3">
                    @foreach($alreadyJoined as $code)
                        <div class="text-sm text-gray-600">
                            {{ $groupCodes[$code] }} ({{ $code }})
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('ukm.index') }}" class="text-purple-600 hover:underline text-sm">
                ← Kembali ke Daftar UKM
            </a>
        </div>
    </div>
</body>
</html>