<?php
// config/paths.php

return [
    'default_hosting' => env('DEFAULT_HOSTING', 'AWS'),

    'hosting' => [
        'Local' => [
            'url' => '/storage/',
            'disk' => 'public',
            'is_default' => false,
            'is_active' => false,
        ],
        'Digital Ocean' => [
            'url' => env('BUCKET_PATH_FILE_FOLDER_READ', 'https://frangettediskspace.fra1.digitaloceanspaces.com'),
            'disk' => 'do',
            'is_default' => false,
            'is_active' => false,
        ],
        'AWS' => [
            'url' => env('AWS_URL', 'https://media.florenceegi.com'),
            'disk' => 's3',
            'is_default' => true,
            'is_active' => true,
        ],
        'IPFS' => [
            'url' => 'https://ipfs.io/ipfs/',
            'disk' => 'ipfs',
            'is_default' => false,
            'is_active' => false,
        ],
    ],


    'paths' => [
        'collections' => 'users_files/collections_{collectionId}/',
        'head' => [
            'root' => 'users_files/collections_{collectionId}/head/',
            'banner' => 'users_files/collections_{collectionId}/head/banner/',
            'card' => 'users_files/collections_{collectionId}/head/card/',
            'avatar' => 'users_files/collections_{collectionId}/head/avatar/',
            'EGI_asset' => 'users_files/collections_{collectionId}/head/EGI_asset/',
        ],
        'EGIs' => 'users_files/collections_{collectionId}/EGIs/',
        'user_data' => [
            'root' => 'users_files/users-data/',
            'documents' => 'users_files/users-data/documents/',
        ],
        'icons' => env('PATH_ICONS', 'https://frangettediskspace.fra1.digitaloceanspaces.com/assets/images/icons/'),
        'temp' => env('BUCKET_TMP_FILE_FOLDER', 'temp'),
    ],
];
