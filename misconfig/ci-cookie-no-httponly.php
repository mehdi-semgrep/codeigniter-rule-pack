<?php

// CI2/CI3 config file (config/config.php) — legacy array form
// ruleid: ci-cookie-no-httponly
$config['cookie_httponly'] = false;

// ok: ci-cookie-no-httponly
$config['cookie_httponly'] = true;

// CI4 Config\App class (app/Config/App.php)
namespace Config;

class App {
    // ruleid: ci-cookie-no-httponly
    public bool $cookieHTTPOnly = false;
}

class CookieSafe {
    // ok: ci-cookie-no-httponly
    public bool $cookieHTTPOnly = true;
}

// Non-Config namespace class — must not match
namespace App\Models;

class SomeUserClass {
    // ok: ci-cookie-no-httponly
    public bool $cookieHTTPOnly = false;
}
