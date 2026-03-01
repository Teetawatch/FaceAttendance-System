<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$icons = ['academic-cap', 'arrow-down-tray', 'arrow-left', 'arrow-path', 'arrow-right-on-rectangle', 'arrow-trending-down', 'arrow-trending-up', 'arrow-up-tray', 'arrow-uturn-left', 'bars-3', 'bell', 'book-open', 'building-office', 'calendar', 'calendar-days', 'camera', 'chart-bar', 'chart-pie', 'check', 'check-circle', 'chevron-down', 'chevron-left', 'chevron-right', 'circle-stack', 'clipboard-document-list', 'clock', 'cog-6-tooth', 'cog-8-tooth', 'computer-desktop', 'device-tablet', 'document-arrow-down', 'document-arrow-up', 'document-check', 'document-text', 'envelope', 'exclamation-circle', 'exclamation-triangle', 'eye', 'face-smile', 'funnel', 'globe-alt', 'identification', 'information-circle', 'light-bulb', 'magnifying-glass', 'map-pin', 'pencil-square', 'photo', 'plus', 'printer', 'qr-code', 'server', 'star', 'table-cells', 'trash', 'user', 'user-circle', 'user-plus', 'users', 'viewfinder-circle', 'x-circle', 'x-mark'];

foreach ($icons as $icon) {
    try {
        Illuminate\Support\Facades\Blade::render("<x-heroicon-o-{$icon} />");
        // echo "OK: {$icon}\n";
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'Unable to locate a class or view for component') !== false) {
            echo "FAIL: {$icon}\n";
        }
    } catch (\Throwable $e) {
        if (strpos($e->getMessage(), 'Unable to locate a class or view for component') !== false) {
            echo "FAIL: {$icon}\n";
        }
    }
}
echo "DONE\n";
