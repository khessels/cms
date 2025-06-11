<?php

return [
    'token' => env('KCS_CONTENT_MANAGER_TOKEN', ''),
    'app' => env('KCS_CONTENT_MANAGER_APP', ''),
    'dev' => env('KCS_CONTENT_MANAGER_DEV', ''),
    'domain' => env('KCS_CONTENT_MANAGER_DOMAIN', 'http://kcs-content-manager.local'),
    'expire' => (int) env('KCS_CONTENT_MANAGER_EXPIRE', 3600),
    'post' => (int) env('KCS_CONTENT_MANAGER_POST', 'YES'),
    'backup' => (int) env('KCS_CONTENT_MANAGER_BACKUP', 0),
    'backup_count' => (int) env('KCS_CONTENT_MANAGER_BACKUP_COUNT', 10),
    'image_management' => (int) env('KCS_CONTENT_MANAGER_IMAGE_MANAGEMENT', 0),
    'image_disks' => explode(',', env('KCS_CONTENT_MANAGER_IMAGE_DISKS', 'public')),
];
