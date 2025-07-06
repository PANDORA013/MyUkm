<?php

// Set proper artisan environment
$_SERVER['argv'] = ['artisan', 'tinker'];
$_SERVER['argc'] = 2;

require __DIR__ . '/../../artisan';
