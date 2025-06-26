@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm">
            <!-- Profile Header -->
            <div class="p-6 border-b">
                <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
            </div>

            <!-- Profile Content -->
            <div class="p-6 space-y-8">
                <!-- Photo Section -->
                <div class="flex items-start space-x-6">
                    <div class="relative">
                        <div class="h-24 w-24 rounded-full overflow-hidden bg-gray-100 ring-4 ring-white">
                            @if($user->photo)
                                <img src="{{ Storage::url($user->photo) }}" 
                                     alt="{{ $user->name }}" 
                                     class="h-full w-full object-cover"
                                     id="currentPhoto">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-blue-100 text-blue-600 text-2xl font-bold"
                                     id="photoPlaceholder">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <img id="photoPreview" class="h-full w-full object-cover hidden" alt="Preview">
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-900">Foto Profil</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            JPG atau PNG. Maksimal 2MB.
                        </p>
                        <form action="{{ route('profile.updatePhoto') }}" 
                              method="POST" 
                              enctype="multipart/form-data" 
                              class="space-y-4">
                            @csrf
                            <div>
                                <input type="file" 
                                       name="photo" 
                                       id="photo" 
                                       accept="image/jpeg,image/png"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100">
                                @error('photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent 
                                           rounded-md shadow-sm text-sm font-medium text-white 
                                           bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 
                                           focus:ring-offset-2 focus:ring-blue-500">
                                Upload Foto
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="border-t pt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama</dt>
                            <dd class="mt-1 text-gray-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">NIM</dt>
                            <dd class="mt-1 text-gray-900">{{ $user->nim }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Change Password -->
                <div class="border-t pt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Password</h3>
                    <form action="{{ route('profile.updatePassword') }}" 
                          method="POST" 
                          class="space-y-4 max-w-md"
                          id="passwordForm">
                        @csrf
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">
                                Password Saat Ini
                            </label>
                            <input type="password" 
                                   name="current_password" 
                                   id="current_password"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                          focus:border-blue-500 focus:ring-blue-500">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password Baru
                            </label>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   required
                                   minlength="8"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                          focus:border-blue-500 focus:ring-blue-500">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Konfirmasi Password Baru
                            </label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation"
                                   required
                                   minlength="8"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                          focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent 
                                           rounded-md shadow-sm text-sm font-medium text-white 
                                           bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 
                                           focus:ring-offset-2 focus:ring-blue-500">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabel Keanggotaan UKM -->
                <div class="border-t pt-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Keanggotaan UKM</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            Total: {{ count($memberships) }} UKM
                        </span>
                    </div>

                    @if(count($memberships) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama UKM</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bergabung</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terakhir Aktif</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($memberships as $membership)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $membership->ukm_name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($membership->joined_at)->format('d M Y H:i') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($membership->is_online)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Online
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Offline
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($membership->last_seen)
                                                    {{ \Carbon\Carbon::parse($membership->last_seen)->diffForHumans() }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <i class="fas fa-users-slash text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-500">Belum terdaftar di UKM manapun</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/profile.js'])
@endpush
