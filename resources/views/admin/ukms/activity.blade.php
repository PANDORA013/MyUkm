@extends('layouts.admin')

@section('title', 'Detail Aktivitas UKM')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('activityChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($activityLabels),
            datasets: [{
                label: 'Jumlah Pesan Bulanan',
                data: @json($activityData),
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Aktivitas UKM (Pesan per Bulan)'
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
    <h1 class="h3 mb-4 text-gray-800">Detail Aktivitas UKM: {{ $ukm->name }}</h1>
    <div class="mb-3">
        <strong>Periode:</strong> {{ $period }}
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <canvas id="activityChart" height="100"></canvas>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Jumlah Pesan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activityTable as $row)
                            <tr>
                                <td>{{ $row['month'] }}</td>
                                <td>{{ $row['count'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
