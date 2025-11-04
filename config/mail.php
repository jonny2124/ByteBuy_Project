<?php
// config/mail.php
// Copy this file to config/mail.local.php and fill in real SMTP credentials.

return [
    'driver' => 'smtp',
    'host' => 'smtp.example.com',
    'port' => 587,
    'username' => 'your-username@example.com',
    'password' => 'your-smtp-password',
    'from_email' => 'no-reply@example.com',
    'from_name' => 'ByteBuy Store',
    'encryption' => 'tls', // tls, ssl, or null
];
