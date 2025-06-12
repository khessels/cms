<?php

return [
    'token' => env('CMS_TOKEN', ''),
    'app' => env('CMS_APP', ''),
    'dev' => env('CMS_DEV', ''),
    'domain' => env('CMS_DOMAIN', 'https://content.khessels.com'),
    'expire' => (int) env('CMS_EXPIRE', 3600),
    'post' => (int) env('CMS_POST', 'YES'),
    'backup' => (int) env('CMS_BACKUP', 0),
    'backup_count' => (int) env('CMS_BACKUP_COUNT', 10),
    'image_management' => (int) env('CMS_IMAGE_MANAGEMENT', 0),
    'image_disks' => explode(',', env('CMS_IMAGE_DISKS', 'public')),
    'available_locales' => explode(',', env('CMS_LOCALES_ENABLED', 'nl,en,es')),
];
