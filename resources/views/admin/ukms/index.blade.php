@extends('layouts.admin')

@section('title', 'Kelola UKM')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-users-cog"></i> Kelola UKM
                    </h4>
                    <div class="d-flex gap-2">
                        <!-- Search Form -->
                        <form method="GET" action="{{ route('admin.ukms') }}" class="d-flex">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari UKM..." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit" aria-label="Cari UKM" title="Cari UKM berdasarkan nama">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('admin.ukms') }}" class="btn btn-outline-danger" aria-label="Hapus pencarian" title="Hapus filter pencarian UKM">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahUkmModal">
                            <i class="fas fa-plus"></i> Tambah UKM
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($ukms->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama UKM</th>
                                        <th>Kode/Referral</th>
                                        <th>Deskripsi</th>
                                        <th>Jumlah Anggota</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ukms as $index => $ukm)
                                        <tr>
                                            <td>{{ $ukms->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $ukm->name }}</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <code class="me-2 fs-6 fw-bold text-primary">{{ $ukm->code }}</code>
                                                    <button class="btn btn-sm btn-outline-success copy-code-btn" 
                                                            data-code="{{ $ukm->code }}"
                                                            title="Copy kode referral">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted d-block">Kode untuk bergabung</small>
                                            </td>
                                            <td>
                                                @if($ukm->description)
                                                    <span title="{{ $ukm->description }}">
                                                        {{ \Illuminate\Support\Str::limit($ukm->description, 50) }}
                                                    </span>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $ukm->members_count ?? 0 }} anggota
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $ukm->created_at ? $ukm->created_at->format('d/m/Y H:i') : '-' }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ url('admin/ukm/' . $ukm->id . '/anggota') }}" 
                                                       class="btn btn-outline-info" 
                                                       title="Lihat Anggota">
                                                        <i class="fas fa-users"></i>
                                                    </a>
                                                    <a href="{{ url('admin/ukm/edit/' . $ukm->id) }}" 
                                                       class="btn btn-outline-warning" 
                                                       title="Edit UKM">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger" 
                                                            title="Hapus UKM"
                                                            onclick="deleteUKM({{ $ukm->id }}, '{{ addslashes($ukm->nama ?? $ukm->name) }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Menampilkan {{ $ukms->firstItem() }} - {{ $ukms->lastItem() }} 
                                dari {{ $ukms->total() }} UKM
                            </div>
                            {{ $ukms->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">
                                @if(request('search'))
                                    Tidak ada UKM yang ditemukan untuk pencarian "{{ request('search') }}"
                                @else
                                    Belum ada UKM yang terdaftar
                                @endif
                            </h5>
                            <p class="text-muted">
                                @if(request('search'))
                                    Coba ubah kata kunci pencarian Anda
                                @else
                                    Klik tombol "Tambah UKM" untuk menambahkan UKM pertama
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah UKM -->
<div class="modal fade" id="tambahUkmModal" tabindex="-1" aria-labelledby="tambahUkmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.tambah-ukm') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahUkmModalLabel">
                        <i class="fas fa-plus"></i> Tambah UKM Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama UKM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label">Kode UKM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" maxlength="4" required>
                        <div class="form-text">Kode unik untuk UKM (maksimal 4 karakter, akan digunakan sebagai referral code)</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi UKM</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Masukkan deskripsi UKM..."></textarea>
                        <div class="form-text">Deskripsi singkat tentang UKM (opsional)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Batal" title="Batal membuat UKM baru">Batal</button>
                    <button type="submit" class="btn btn-primary" aria-label="Simpan UKM baru" title="Simpan UKM baru dengan data yang telah diisi">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-close alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Function to copy referral code
    document.addEventListener('DOMContentLoaded', function() {
        const copyButtons = document.querySelectorAll('.copy-code-btn');
        
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const code = this.getAttribute('data-code');
                
                // Create temporary textarea to copy text
                const textarea = document.createElement('textarea');
                textarea.value = code;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                
                // Show feedback
                const originalIcon = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check text-success"></i>';
                this.classList.remove('btn-outline-success');
                this.classList.add('btn-success');
                
                // Show toast notification
                if (typeof showToast === 'function') {
                    showToast('Kode referral "' + code + '" berhasil disalin!', 'success');
                } else {
                    alert('Kode referral "' + code + '" berhasil disalin!');
                }
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    this.innerHTML = originalIcon;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-success');
                }, 2000);
            });
        });
    });
    
    // Function to confirm UKM deletion
    function confirmDeleteUKM(ukmId, ukmName) {
        if (confirm('Apakah Anda yakin ingin menghapus UKM "' + ukmName + '"?\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait UKM ini.')) {
            // Create form and submit
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/ukm/' + ukmId;
            
            // Add CSRF token
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add method spoofing for DELETE
            let methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
