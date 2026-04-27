<?php

// Simulated config/config.php

// ruleid: ci-empty-encryption-key
$config['encryption_key'] = '';

// ok: ci-empty-encryption-key
$config['encryption_key'] = 'aB3$2KdLm9pQzRtUvWxYzA1b2C3d4E5f';

// ok: ci-empty-encryption-key
$config['encryption_key'] = getenv('ENCRYPTION_KEY');
