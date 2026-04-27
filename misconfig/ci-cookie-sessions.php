<?php

// Simulated config/config.php

// ruleid: ci-cookie-sessions
$config['sess_use_database'] = FALSE;

// ruleid: ci-cookie-sessions
$config['sess_driver'] = 'cookie';

// ok: ci-cookie-sessions
$config['sess_use_database'] = TRUE;

// ok: ci-cookie-sessions
$config['sess_driver'] = 'files';

// ok: ci-cookie-sessions
$config['sess_driver'] = 'redis';
