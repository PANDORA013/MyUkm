@extends('layouts.admin_grup')

@section('title', 'Riwayat Anggota Keluar')

@section('content')
<div class="container-fluid p-0">
    <h4 class="page-header mb-4">
        <i class="fas fa-history me-2"></i>Riwayat Anggota Keluar
        @if($group)
            <span class="text-muted">- {{ $group->name }}</span>
        @endif
    </h4>
    <div class="card">
        <div class="card-header bg-light">
            <span><i class="fas fa-user-slash me-1"></i> Anggota yang pernah keluar ({{ $ex_members->count() }})</span>
        </div>
        <div class="card-body">
            @if($ex_members->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Belum ada riwayat anggota keluar</h6>
                </div>
            @else
                <div class="row g-3">
                    @foreach($ex_members as $ex)
                        <div class="col-lg-6 col-xl-4">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <div class="member-avatar me-3 bg-danger">
                                            {{ substr($ex->name, 0, 1) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-danger">{{ $ex->name }}</h6>
                                            <p class="mb-1 text-muted small">
                                                <i class="fas fa-id-card me-1"></i>{{ $ex->nim }}
                                            </p>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-plus me-1"></i>
                                                    Bergabung: {{ \Carbon\Carbon::parse($ex->pivot->created_at)->format('d M Y') }}<br>
                                                    <i class="fas fa-calendar-minus me-1"></i>
                                                    Keluar: {{ \Carbon\Carbon::parse($ex->pivot->deleted_at)->format('d M Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
