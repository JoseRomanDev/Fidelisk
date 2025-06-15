<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo e(config('app.name', 'FIDELISK')); ?></title>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    </head>
    <body class="font-sans antialiased">
        <main>
            
            <?php echo e($slot); ?>

            <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

        </main>
    </body>
</html><?php /**PATH C:\Users\josem\OneDrive\Escritorio\LARAVEL\Fidelisk\resources\views/components/layouts/auth.blade.php ENDPATH**/ ?>