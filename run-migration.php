<?php
// public/run-migration.php
require __DIR__.'/../../face-core/vendor/autoload.php';
$app = require_once __DIR__.'/../../face-core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('migrate', ['--force' => true]);
echo "Migration completed!";