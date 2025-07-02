<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title',
    'value',
    'icon',
    'description' => null,
    'color' => 'blue',
    'action' => null
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'title',
    'value',
    'icon',
    'description' => null,
    'color' => 'blue',
    'action' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$colors = [
    'blue' => [
        'bg' => 'bg-blue-50',
        'text' => 'text-blue-500',
        'button' => 'bg-blue-600 hover:bg-blue-700'
    ],
    'green' => [
        'bg' => 'bg-green-50',
        'text' => 'text-green-500',
        'button' => 'bg-green-600 hover:bg-green-700'
    ],
    'yellow' => [
        'bg' => 'bg-yellow-50',
        'text' => 'text-yellow-500',
        'button' => 'bg-yellow-600 hover:bg-yellow-700'
    ],
    'red' => [
        'bg' => 'bg-red-50',
        'text' => 'text-red-500',
        'button' => 'bg-red-600 hover:bg-red-700'
    ],
];

$colorClasses = $colors[$color] ?? $colors['blue'];
?>

<div class="bg-white border border-gray-200 shadow rounded-lg p-4 hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e($title); ?></p>
            <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($value); ?></p>
            <?php if($description): ?>
                <p class="text-xs text-gray-500 mt-1"><?php echo e($description); ?></p>
            <?php endif; ?>
        </div>
        <div class="p-3 <?php echo e($colorClasses['bg']); ?> rounded-lg">
            <?php if(str_starts_with($icon, 'fa-')): ?>
                <i class="fas <?php echo e($icon); ?> <?php echo e($colorClasses['text']); ?> text-2xl"></i>
            <?php else: ?>
                <?php echo $icon; ?>

            <?php endif; ?>
        </div>
    </div>
    
    <?php if($action): ?>
        <div class="mt-auto">
            <a href="<?php echo e($action['url']); ?>" 
               class="w-full text-center px-4 py-2 <?php echo e($colorClasses['button']); ?> text-white rounded-lg transition-colors duration-200 flex items-center justify-center">
                <?php if(isset($action['icon'])): ?>
                    <i class="fas <?php echo e($action['icon']); ?> mr-2"></i>
                <?php endif; ?>
                <?php echo e($action['label']); ?>

            </a>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/components/stat-card.blade.php ENDPATH**/ ?>