<?php
$_SERVER['CI_ENV'] = 'testing';
chdir(__DIR__);

// Serve real static files (css/js/images/fonts) directly, let php -S's built-in
// handler return false here; only route to CI3 for actual app requests.
$path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$full = __DIR__ . $path;
if ($path !== '/' && file_exists($full) && is_file($full)) {
    return false;
}

require __DIR__ . '/index.php';
