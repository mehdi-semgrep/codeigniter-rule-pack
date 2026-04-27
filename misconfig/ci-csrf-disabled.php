<?php

// CI2/CI3 config file
// ruleid: ci-csrf-disabled
$config['csrf_protection'] = false;

// ok: ci-csrf-disabled
$config['csrf_protection'] = true;

// CI4 Config\Security class
namespace Config;

class Security {
    // ruleid: ci-csrf-disabled
    public $csrfProtection = false;
}

class SecuritySafe {
    // ok: ci-csrf-disabled
    public $csrfProtection = 'cookie';
}

// Non-Config namespace — must not match
namespace App\Models;

class UserPrefs {
    // ok: ci-csrf-disabled
    public $csrfProtection = false;
}
