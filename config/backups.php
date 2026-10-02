<?php

return [
    'root' => env('BACKUP_ROOT', '/home/claude/services/backups/sqls'),
    'fallback_root' => base_path(env('BACKUP_FALLBACK_ROOT', 'storage/app/backups')),
    'preview_max_kb' => (int) env('BACKUP_PREVIEW_MAX_KB', 1024),
];
