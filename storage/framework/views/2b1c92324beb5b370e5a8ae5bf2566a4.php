<!DOCTYPE html>
<html>
<head>
    <title>Groups Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Groups Management</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Referral Code</th>
                                    <th>Description</th>
                                    <th>UKM</th>
                                    <th>Active</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($group->id); ?></td>
                                        <td><?php echo e($group->name); ?></td>
                                        <td><?php echo e($group->referral_code); ?></td>
                                        <td><?php echo e(Str::limit($group->description, 50)); ?></td>
                                        <td><?php echo e($group->ukm ? $group->ukm->name : 'No UKM'); ?></td>
                                        <td><?php echo e($group->is_active ? 'Yes' : 'No'); ?></td>
                                        <td><?php echo e($group->created_at->format('Y-m-d')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No groups found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if(method_exists($groups, 'links')): ?>
                        <?php echo e($groups->links()); ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\admin\groups\index.blade.php ENDPATH**/ ?>