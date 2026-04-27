<?php

// CI2/CI3 config file
// ruleid: ci-cookie-no-secure
$config['cookie_secure'] = false;

// ok: ci-cookie-no-secure
$config['cookie_secure'] = true;

// CI4 Config\App class
namespace Config;

class App {
    // ruleid: ci-cookie-no-secure
    public bool $cookieSecure = false;
}

class CookieSafe {
    // ok: ci-cookie-no-secure
    public bool $cookieSecure = true;
}

// Non-Config namespace — must not match
namespace App\Models;

class UserPrefs {
    // ok: ci-cookie-no-secure
    public bool $cookieSecure = false;
}
