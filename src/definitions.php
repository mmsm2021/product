<?php

use function DI\env;

return [
    'database.connection.url' => env('DB_URI'),
    'database.entity.paths' => [
        __DIR__ . '/app/Database/Entities/',
    ],
    'database.proxies.dir' => __DIR__ . '/cache/Database/Proxies',
    'database.proxies.namespace' => 'Database\Proxies',
    'database.migrations.config' => [
        'table_storage' => [
            'table_name' => 'doctrine_migration_versions',
            'version_column_name' => 'version',
            'version_column_length' => 1024,
            'executed_at_column_name' => 'executed_at',
            'execution_time_column_name' => 'execution_time',
        ],

        'migrations_paths' => [
            'App\Database\Migrations' => __DIR__ . '/app/Database/Migrations',
        ],

        'all_or_nothing' => true,
        'check_database_platform' => true,
        'organize_migrations' => 'none',
    ],
    //add definitions here.
];