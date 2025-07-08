<?php

return [
    'token'                         => env('CMS_TOKEN', ''),
    'app'                           => env('CMS_APP', ''),
    'dev'                           => env('CMS_DEV', ''),
    'domain'                        => env('CMS_DOMAIN', 'https://content.khessels.com'),
    'expire'                        => (int) env('CMS_EXPIRE', 3600),
    'post'                          => (int) env('CMS_POST', 'YES'),
    'backup'                        => (int) env('CMS_BACKUP', 0),
    'backup_count'                  => (int) env('CMS_BACKUP_COUNT', 10),

    'image_management'              => (int) env('CMS_IMAGE_MANAGEMENT', 0),
    'document_management'           => (int) env('CMS_DOCUMENTS_MANAGEMENT', 0),
    'content_management'            => (int) env('CMS_CONTENT_MANAGEMENT', 0),

    'images_disks'                  => explode(',', env('CMS_IMAGES_DISKS', 'public')),
    'content_disks'                 => explode(',', env('CMS_CONTENT_DISKS', 'public')),
    'documents_disks'               => explode(',', env('CMS_DOCUMENTS_DISKS', 'public')),
    'available_locales'             => explode(',', env('CMS_LOCALES_ENABLED', 'nl,en,es')),

    'accepted_image_extensions'    => explode( ',', env('CMS_IMAGES_ACCEPTED_EXTENSIONS', 'jpeg,jpg,png,gif,webp,pdf,svg,ico')),
];
