<?php

// Simulated config/database.php

// ruleid: ci-hardcoded-db-creds
$db['default']['password'] = 'supersecret123';

// ruleid: ci-hardcoded-db-creds
$db['default']['password'] = 'hunter2';

// ok: ci-hardcoded-db-creds
$db['default']['password'] = '';

// ok: ci-hardcoded-db-creds
$db['default']['password'] = getenv('DB_PASSWORD');

// ok: ci-hardcoded-db-creds
$db['default']['password'] = $_ENV['DB_PASSWORD'];

// Tier-1: deploy-time placeholder substitution — not real secrets
// ok: ci-hardcoded-db-creds
$db['default']['password'] = '@DB_PASSWORD@';

// ok: ci-hardcoded-db-creds
$db['default']['password'] = '{{password}}';

// ok: ci-hardcoded-db-creds
$db['default']['password'] = '%DB_PASS%';

// ok: ci-hardcoded-db-creds
$db['default']['password'] = '<PASSWORD>';

// ok: ci-hardcoded-db-creds
$db['default']['password'] = '${DB_PASS}';

// CI4 app/Config/Database.php class property form
namespace Config;

class Database {
    // ruleid: ci-hardcoded-db-creds
    public string $password = 'supersecret';

    // ok: ci-hardcoded-db-creds
    public string $username = 'root';  // different field, not password
}
