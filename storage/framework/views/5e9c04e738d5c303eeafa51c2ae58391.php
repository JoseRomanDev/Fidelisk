<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('FIDELISK')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('FIDELISK'))]); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php if(auth()->user()->hasRole('agente')): ?>
                    <a href="<?php echo e(route('agente.panel')); ?>" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-blue-50 dark:hover:bg-blue-900">
                        <h3 class="text-lg font-bold text-blue-700 dark:text-blue-300">Panel de Agente</h3>
                        <p class="text-gray-600 dark:text-gray-300">Accede a la gestión de clientes y tickets como agente.</p>
                    </a>
                <?php endif; ?>
                <?php if(auth()->user()->hasRole('supervisor')): ?>
                    <a href="<?php echo e(route('supervisor.panel')); ?>" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-green-50 dark:hover:bg-green-900">
                        <h3 class="text-lg font-bold text-green-700 dark:text-green-300">Panel de Supervisor</h3>
                        <p class="text-gray-600 dark:text-gray-300">Accede a la supervisión y estadísticas globales.</p>
                    </a>
                    <a href="<?php echo e(route('agente.panel')); ?>" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-blue-50 dark:hover:bg-blue-900">
                        <h3 class="text-lg font-bold text-blue-700 dark:text-blue-300">Panel de Agente</h3>
                        <p class="text-gray-600 dark:text-gray-300">Accede a la gestión de clientes y tickets como agente.</p>
                    </a>
                <?php endif; ?>
                <?php if(auth()->user()->hasRole('admin')): ?>
                    <a href="<?php echo e(route('admin.panel')); ?>" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-yellow-50 dark:hover:bg-yellow-900">
                        <h3 class="text-lg font-bold text-yellow-700 dark:text-yellow-300">Panel de Administrador</h3>
                        <p class="text-gray-600 dark:text-gray-300">Accede a la gestión completa del sistema.</p>
                    </a>
                    <a href="<?php echo e(route('supervisor.panel')); ?>" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-green-50 dark:hover:bg-green-900">
                        <h3 class="text-lg font-bold text-green-700 dark:text-green-300">Panel de Supervisor</h3>
                        <p class="text-gray-600 dark:text-gray-300">Accede a la supervisión y estadísticas globales.</p>
                    </a>
                    <a href="<?php echo e(route('agente.panel')); ?>" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-blue-50 dark:hover:bg-blue-900">
                        <h3 class="text-lg font-bold text-blue-700 dark:text-blue-300">Panel de Agente</h3>
                        <p class="text-gray-600 dark:text-gray-300">Accede a la gestión de clientes y tickets como agente.</p>
                    </a>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?><?php /**PATH C:\Users\josem\OneDrive\Escritorio\LARAVEL\Fidelisk\resources\views/dashboard.blade.php ENDPATH**/ ?>