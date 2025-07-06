<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type' => 'info']));

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

foreach (array_filter((['type' => 'info']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$colors = [
    'success' => 'bg-green-100 border-green-200 text-green-700',
    'error' => 'bg-red-100 border-red-200 text-red-700',
    'info' => 'bg-blue-100 border-blue-200 text-blue-700',
    'warning' => 'bg-yellow-100 border-yellow-200 text-yellow-700'
];

$icon = [
    'success' => 'check-circle',
    'error' => 'exclamation-circle',
    'info' => 'information-circle',
    'warning' => 'exclamation'
];

$colorClasses = $colors[$type] ?? $colors['info'];
$iconName = $icon[$type] ?? 'information-circle';
?>

<div <?php echo e($attributes->merge(['class' => "px-4 py-3 rounded-lg border $colorClasses flex items-start"])); ?>>
    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <div>
        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\components\alert.blade.php ENDPATH**/ ?>