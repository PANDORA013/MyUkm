<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyUkm - Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-sm p-4">
        @if(session('success'))
        <div class="p-3 bg-green-300 mb-3 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-blue-500 h-48 rounded-b-full relative flex items-center justify-center">
                <h1 class="text-3xl font-bold text-white">MyUkm</h1>
            </div>

            <form method="POST" action="{{ route('register') }}" class="px-6 py-6">
                @csrf
                <div class="mb-4">
                    <input type="text" name="name" placeholder="Nama" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')<div class="text-red-600 text-sm mt-1 ml-4">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <input type="text" name="nim" placeholder="NIM" value="{{ old('nim') }}" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nim') border-red-500 @enderror">
                    @error('nim')<div class="text-red-600 text-sm mt-1 ml-4">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <input type="password" name="password" placeholder="Password" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')<div class="text-red-600 text-sm mt-1 ml-4">{{ $message }}</div>@enderror
                </div>
                <div class="mb-6">
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit"
                    class="w-full py-3 bg-blue-500 text-white rounded-full font-semibold hover:bg-blue-600 transition"
                    aria-label="Daftar akun baru"
                    title="Daftar akun baru dengan data yang telah diisi">
                    Daftar
                </button>

                <p class="text-center text-sm text-gray-600 mt-4">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-blue-500 font-semibold underline">Login</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
