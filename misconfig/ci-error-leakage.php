<?php

// ruleid: ci-error-leakage
$db['default']['db_debug'] = TRUE;

// ruleid: ci-error-leakage
define('ENVIRONMENT', 'development');

// ok: ci-error-leakage
$db['default']['db_debug'] = FALSE;

// ok: ci-error-leakage
define('ENVIRONMENT', 'production');
