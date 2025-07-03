<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyUkm - Registration</title>
    
    <!-- Favicons -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            margin: 0 auto;
        }
        .auth-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            padding: 3rem 2rem;
            text-align: center;
            border-radius: 0 0 50% 50%;
            margin-bottom: 2rem;
        }
        .form-control {
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            border: 2px solid #e3e6f0;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        .btn-auth {
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="auth-card">
                    <div class="auth-header">
                        <h1 class="text-white mb-0 fs-2 fw-bold">
                            <i class="fas fa-users me-2"></i>MyUkm
                        </h1>
                        <p class="text-white-50 mb-0 mt-2">Bergabung dengan komunitas UKM</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="px-4 pb-4">
                        @csrf
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                                <input type="text" 
                                       name="name" 
                                       class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                       placeholder="Nama Lengkap" 
                                       value="{{ old('name') }}" 
                                       required
                                       aria-label="Nama Lengkap"
                                       title="Masukkan nama lengkap Anda">
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-id-card text-muted"></i>
                                </span>
                                <input type="text" 
                                       name="nim" 
                                       class="form-control border-start-0 @error('nim') is-invalid @enderror" 
                                       placeholder="NIM" 
                                       value="{{ old('nim') }}" 
                                       required
                                       aria-label="Nomor Induk Mahasiswa"
                                       title="Masukkan NIM Anda">
                            </div>
                            @error('nim')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" 
                                       name="password" 
                                       class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                       placeholder="Password" 
                                       required
                                       aria-label="Password"
                                       title="Masukkan password yang aman">
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" 
                                       name="password_confirmation" 
                                       class="form-control border-start-0" 
                                       placeholder="Konfirmasi Password" 
                                       required
                                       aria-label="Konfirmasi Password"
                                       title="Konfirmasi password yang sama">
                            </div>
                        </div>

                        <button type="submit"
                                class="btn btn-primary btn-auth w-100 mb-3"
                                aria-label="Daftar akun baru"
                                title="Daftar akun baru dengan data yang telah diisi">
                            <i class="fas fa-user-plus me-2"></i>Daftar
                        </button>

                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Sudah punya akun?
                                <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
