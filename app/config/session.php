<?php
require_once __DIR__ . '/DbSessionHandler.php';

if (session_status() === PHP_SESSION_NONE) {

   
    if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
        error_log("[SESSION] \$koneksi belum tersedia, membuka koneksi sendiri untuk session handler.");
        require_once dirname(__DIR__, 2) . '/koneksi.php';
    }

    if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
        error_log("[SESSION] FATAL: gagal membuat koneksi database untuk session handler.");
        die("Konfigurasi session error: koneksi database belum siap.");
    }

    $handler = new DbSessionHandler($koneksi);
    session_set_save_handler($handler, true);

    session_set_cookie_params([
        'lifetime' => 86400,
        'path'     => '/',
        'domain'   => '',
        'secure'   => true,   
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();

    error_log("[SESSION] Session started via DbSessionHandler. ID: " . session_id());
    error_log("[SESSION] Session data: " . print_r($_SESSION, true));
}