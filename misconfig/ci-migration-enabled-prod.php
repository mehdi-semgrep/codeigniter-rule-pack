<?php

// CI3 application/config/migration.php — typical context.

// ruleid: ci-migration-enabled-prod
$config['migration_enabled'] = TRUE;

// ruleid: ci-migration-enabled-prod
$config['migration_enabled'] = true;

// Conditional gating still fires (structural rule, no ENVIRONMENT
// awareness — reviewer judgment expected per rule message).
if (ENVIRONMENT !== 'production') {
    // ruleid: ci-migration-enabled-prod
    $config['migration_enabled'] = TRUE;
}

// ok: ci-migration-enabled-prod
$config['migration_enabled'] = FALSE;

// ok: ci-migration-enabled-prod
$config['migration_enabled'] = false;

// ok: ci-migration-enabled-prod
// Different config key.
$config['migration_type'] = 'sequential';

// ok: ci-migration-enabled-prod
// Different config key.
$config['migration_path'] = APPPATH . 'migrations/';

// Constant-propagation TP: Semgrep follows the literal `true`
// through a single intra-scope assignment.
$some_flag = true;
// ruleid: ci-migration-enabled-prod
$config['migration_enabled'] = $some_flag;

// True dynamic value from a function call — Semgrep cannot
// constant-propagate this and the rule does not fire. Acceptable
// recall gap; rare in real config files.
function should_enable_migrations() { return true; }
// ok: ci-migration-enabled-prod
$config['migration_enabled'] = should_enable_migrations();

// Method-local $config — typical FuelCMS-style migration runner that
// builds a config array to pass to $this->load->library('migration', $config).
// Suppressed by pattern-not-inside `function $F(...) { ... }`.
class MigrationRunner extends CI_Controller {
    protected function _init_migrate($module) {
        // ok: ci-migration-enabled-prod
        $config['migration_enabled'] = true;
        $config['module'] = $module;
        $this->load->library('migration', $config);
    }
}

// Standalone function with a local $config — same case, also suppressed.
function _migrate_locally($version) {
    // ok: ci-migration-enabled-prod
    $config['migration_enabled'] = true;
    $config['migration_version'] = $version;
    return $config;
}
