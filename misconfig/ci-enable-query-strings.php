<?php

// CI3 application/config/config.php — typical context.

// ruleid: ci-enable-query-strings
$config['enable_query_strings'] = TRUE;

// ruleid: ci-enable-query-strings
$config['enable_query_strings'] = true;

// ok: ci-enable-query-strings
$config['enable_query_strings'] = FALSE;

// ok: ci-enable-query-strings
$config['enable_query_strings'] = false;

// ok: ci-enable-query-strings
$config['enable_hooks'] = TRUE;  // different config key

// Constant-propagation TP: Semgrep follows the literal `true`
// through a single intra-scope assignment.
$some_flag = true;
// ruleid: ci-enable-query-strings
$config['enable_query_strings'] = $some_flag;

// True dynamic value from a function call — Semgrep cannot
// constant-propagate this and the rule does not fire. Acceptable
// recall gap; rare in real config files.
function compute_setting() { return true; }
// ok: ci-enable-query-strings
$config['enable_query_strings'] = compute_setting();

// Method-local $config — defensive against the same FP shape that
// affects ci-migration-enabled-prod. Suppressed.
class SomeController extends CI_Controller {
    protected function build_config() {
        // ok: ci-enable-query-strings
        $config['enable_query_strings'] = true;
        return $config;
    }
}
