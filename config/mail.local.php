<?php
// config/mail.local.php
// Local SMTP configuration - using log-only mode for development
return [
    'driver' => 'log',  // 'log' for development, 'smtp' for production
    'host' => '',
    'port' => 587,
    'username' => '',
    'password' => '',
    'from_email' => 'noreply@bytebuy.test',
    'from_name' => 'ByteBuy Store (Dev)',
    'encryption' => null,
];