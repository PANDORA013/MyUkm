<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-center text-blue-500 mb-4">Join Group</h1>
        <form method="POST" action="{{ route('join.group') }}" class="w-80">
            @csrf
            <select name="group_code" id="group_code" required 
                class="w-full px-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
                <option value="">-- Pilih Grup --</option>
                @foreach($groupCodes as $code => $name)
                    <option value="{{ $code }}">{{ $name }} ({{ $code }})</option>
                @endforeach
            </select>
            @error('group_code')
                <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
            @enderror
            <button type="submit"
                class="w-full py-3 bg-blue-500 text-white rounded-full font-semibold hover:bg-blue-600 transition">
                Join
            </button>
        </form>
    </div>
</body>
</html>
