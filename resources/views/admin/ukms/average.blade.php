@extends('layouts.admin')

@section('title', 'Rata-rata Anggota per UKM Bulan Ini')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('ukmChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Jumlah Anggota Bulan Ini',
                data: @json($data),
                backgroundColor: 'rgba(99, 102, 241, 0.7)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Jumlah Anggota per UKM (Bulan Ini)'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
});
</script>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Rata-rata Anggota per UKM Bulan Ini</h1>
    <div class="mb-3">
        <strong>Rata-rata:</strong> {{ $average }} anggota per UKM
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <canvas id="ukmChart" height="100"></canvas>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>UKM</th>
                            <th>Jumlah Anggota Bulan Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ukms as $ukm)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.ukms.activity', $ukm->id) }}" style="text-decoration:underline; color:#6366f1; font-weight:bold;">
                                        {{ $ukm->name }}
                                    </a>
                                </td>
                                <td>{{ $ukm->users_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
