<?php $__env->startSection('title', 'Rata-rata Anggota per UKM Bulan Ini'); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('ukmChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels, 15, 512) ?>,
            datasets: [{
                label: 'Jumlah Anggota Bulan Ini',
                data: <?php echo json_encode($data, 15, 512) ?>,
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Rata-rata Anggota per UKM Bulan Ini</h1>
    <div class="mb-3">
        <strong>Rata-rata:</strong> <?php echo e($average); ?> anggota per UKM
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
                        <?php $__currentLoopData = $ukms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ukm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('admin.ukms.activity', $ukm->id)); ?>" style="text-decoration:underline; color:#6366f1; font-weight:bold;">
                                        <?php echo e($ukm->name); ?>

                                    </a>
                                </td>
                                <td><?php echo e($ukm->users_count); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NganTeen\MyUkm\resources\views/admin/ukms/average.blade.php ENDPATH**/ ?>