<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Daftar User</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-2xl mb-4">Daftar User dan Grup</h1>
    <table class="table-auto border-collapse border border-gray-400 w-full">
        <thead>
            <tr class="bg-gray-300">
                <th class="border border-gray-400 px-4 py-2">Nama</th>
                <th class="border border-gray-400 px-4 py-2">NIM</th>
                <th class="border border-gray-400 px-4 py-2">Grup</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="hover:bg-gray-100">
                <td class="border border-gray-400 px-4 py-2">{{ $user->name }}</td>
                <td class="border border-gray-400 px-4 py-2">{{ $user->nim }}</td>
                <td class="border border-gray-400 px-4 py-2">
                    {{ $groupCodes[$user->group_code] ?? '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('ukm.index') }}" class="inline-block mt-4 text-blue-600 underline">Kembali ke Chat</a>
</body>
</html>
