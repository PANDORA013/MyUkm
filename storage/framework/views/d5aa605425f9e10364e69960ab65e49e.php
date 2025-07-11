<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>MyUkm - Login</title>
    
    <!-- Favicons -->
    <link rel="icon" href="<?php echo e(asset('favicon.ico')); ?>" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
        .input-group-text {
            border-radius: 50px 0 0 50px;
            border: 2px solid #e3e6f0;
            border-right: none;
        }
        .input-group .form-control {
            border-radius: 0 50px 50px 0;
            border-left: none;
        }
        .input-group:focus-within .input-group-text {
            border-color: #4e73df;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo e($errors->first()); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="auth-card">
                    <div class="auth-header">
                        <h1 class="text-white mb-0 fs-2 fw-bold">
                            <i class="fas fa-users me-2"></i>MyUkm
                        </h1>
                        <p class="text-white-50 mb-0 mt-2">Masuk ke akun Anda</p>
                    </div>

                    <form method="POST" action="<?php echo e(route('login')); ?>" class="px-4 pb-4">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-id-card text-muted"></i>
                                </span>
                                <input type="text" 
                                       name="nim" 
                                       class="form-control border-start-0" 
                                       placeholder="NIM" 
                                       value="<?php echo e(old('nim')); ?>" 
                                       required
                                       aria-label="Nomor Induk Mahasiswa"
                                       title="Masukkan NIM Anda">
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" 
                                       name="password" 
                                       class="form-control border-start-0" 
                                       placeholder="Password" 
                                       required
                                       aria-label="Password"
                                       title="Masukkan password Anda">
                            </div>
                        </div>

                        <button type="submit"
                                class="btn btn-primary btn-auth w-100 mb-3"
                                aria-label="Masuk ke akun"
                                title="Masuk dengan NIM dan password yang diisi">
                            <i class="fas fa-sign-in-alt me-2"></i>Log In
                        </button>

                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Belum punya akun?
                                <a href="<?php echo e(route('register')); ?>" class="text-decoration-none fw-semibold">
                                    <i class="fas fa-user-plus me-1"></i>Daftar
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

    <?php if(session('success')): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registrasi Berhasil!',
                text: "<?php echo e(session('success')); ?>",
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        </script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\MyUkm-master\resources\views/auth/login.blade.php ENDPATH**/ ?>