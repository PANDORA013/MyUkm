<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyUkm - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-sm p-4">
        @if($errors->any())
        <div class="p-3 bg-red-300 mb-3 rounded">
            {{ $errors->first() }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-blue-500 h-48 rounded-b-full relative flex items-center justify-center">
                <h1 class="text-3xl font-bold text-white">MyUkm</h1>
            </div>

            <form method="POST" action="{{ route('login') }}" class="px-6 py-6">
                @csrf
                <div class="mb-4">
                    <input type="text" name="nim" value="{{ old('nim') }}" placeholder="NIM" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-6">
                    <input type="password" name="password" placeholder="Password" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit"
                    class="w-full py-3 bg-blue-500 text-white rounded-full font-semibold hover:bg-blue-600 transition"
                    aria-label="Masuk ke akun"
                    title="Masuk dengan email dan password yang diisi">
                    Log In
                </button>

                <p class="text-center text-sm text-gray-600 mt-4">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-blue-500 font-semibold underline">Registration</a>
                </p>
            </form>
        </div>
    </div>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registrasi Berhasil!',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif
</body>
</html>
