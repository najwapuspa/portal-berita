<?php

/**
 * NewsXPaper — Root Entry Point Bridge
 * 
 * File ini menjamin bahwa jika server web (Apache pada InfinityFree/shared hosting)
 * langsung mengakses root direktori proyek, request akan diteruskan dengan aman
 * ke front-controller Laravel di public/index.php tanpa memicu error 403 Forbidden.
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/'
);

// Jika file statis yang diminta ada di dalam folder public, biarkan server melayaninya
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

require_once __DIR__ . '/public/index.php';
