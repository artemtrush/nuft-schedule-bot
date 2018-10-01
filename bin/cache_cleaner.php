<?php
date_default_timezone_set('Europe/Kiev');
$appConf = include_once __DIR__ . '/../etc/app-conf.php';

$cacheDir = $appConf['cache']['dir'];
$files = scandir($cacheDir);
if ($files) {
    foreach ($files as $filename) {
        if ($filename[0] != '.') {
            $file = $cacheDir . $filename;
            $data = unserialize(file_get_contents($file));
            if (!$data || empty($data['time']) || $data['time'] < time() - (7 * 24 * 60 * 60)) {
                unlink($file);
            }
        }
    }
}
