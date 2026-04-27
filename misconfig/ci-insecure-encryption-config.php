<?php

// Track D test file for ci-insecure-encryption-config.
// Pure structural rule — no class context required for CI3 array
// configs (the $this->encryption->initialize call shape is the signal).

class WeakCipherController extends CI_Controller {
    public function vuln_des() {
        // ruleid: ci-insecure-encryption-config
        $this->encryption->initialize(['cipher' => 'des', 'mode' => 'cbc']);
    }

    public function vuln_3des() {
        // ruleid: ci-insecure-encryption-config
        $this->encryption->initialize(['cipher' => '3des', 'mode' => 'cbc']);
    }

    public function vuln_tripledes() {
        // ruleid: ci-insecure-encryption-config
        $this->encryption->initialize(['cipher' => 'tripledes']);
    }

    public function vuln_rc2() {
        // ruleid: ci-insecure-encryption-config
        $this->encryption->initialize(['cipher' => 'rc2']);
    }

    public function vuln_rc4() {
        // ruleid: ci-insecure-encryption-config
        $this->encryption->initialize(['cipher' => 'rc4']);
    }

    public function vuln_ecb_mode() {
        // ruleid: ci-insecure-encryption-config
        $this->encryption->initialize(['cipher' => 'aes-256', 'mode' => 'ecb']);
    }

    public function vuln_md5_hmac() {
        // ruleid: ci-insecure-encryption-config
        $this->encryption->initialize(['cipher' => 'aes-256', 'mode' => 'cbc', 'hmac_digest' => 'md5']);
    }

    public function vuln_sha1_hmac() {
        // ruleid: ci-insecure-encryption-config
        $this->encryption->initialize(['hmac_digest' => 'sha1']);
    }

    // FN-risk variant: weak cipher coexisting with strong mode — should still fire
    public function vuln_mixed_weak_cipher_strong_mode() {
        // ruleid: ci-insecure-encryption-config
        $this->encryption->initialize(['cipher' => 'des', 'mode' => 'gcm']);
    }
}

// === MUST FIRE: CI3 legacy MCrypt library usage ===
class LegacyMcryptController extends CI_Controller {
    public function vuln_legacy_encode($data) {
        // ruleid: ci-insecure-encryption-config
        $encrypted = $this->encrypt->encode($data);
    }

    public function vuln_legacy_decode($cipher) {
        // ruleid: ci-insecure-encryption-config
        $plain = $this->encrypt->decode($cipher);
    }

    public function vuln_legacy_sha1($data) {
        // ruleid: ci-insecure-encryption-config
        $hash = $this->encrypt->sha1($data);
    }

    public function vuln_legacy_encode_from_legacy($data) {
        // ruleid: ci-insecure-encryption-config
        $encrypted = $this->encrypt->encode_from_legacy($data);
    }

    public function vuln_legacy_set_cipher() {
        // ruleid: ci-insecure-encryption-config
        $this->encrypt->set_cipher(MCRYPT_DES);
    }

    public function vuln_legacy_set_mode() {
        // ruleid: ci-insecure-encryption-config
        $this->encrypt->set_mode(MCRYPT_MODE_ECB);
    }
}

// === MUST NOT FIRE: strong CI3 configs ===
class StrongConfigController extends CI_Controller {
    public function safe_aes256() {
        // ok: ci-insecure-encryption-config
        $this->encryption->initialize(['cipher' => 'aes-256']);
    }

    public function safe_aes128_cbc_sha512() {
        // ok: ci-insecure-encryption-config
        $this->encryption->initialize([
            'cipher' => 'aes-128',
            'mode' => 'cbc',
            'hmac_digest' => 'sha512',
        ]);
    }

    public function safe_aes256_gcm() {
        // ok: ci-insecure-encryption-config
        $this->encryption->initialize(['cipher' => 'aes-256', 'mode' => 'gcm']);
    }

    public function safe_sha384_hmac() {
        // ok: ci-insecure-encryption-config
        $this->encryption->initialize(['hmac_digest' => 'sha384']);
    }

    public function safe_default_no_args() {
        // Default config uses strong primitives (CI3 default: AES-128-CBC + SHA-512 HMAC)
        // ok: ci-insecure-encryption-config
        $this->encryption->initialize([]);
    }

    public function safe_using_modern_encryption_library($data) {
        // Operations on the modern library — not config — must NOT fire
        // ok: ci-insecure-encryption-config
        $cipher = $this->encryption->encrypt($data);
    }

    // Out-of-scope intentional FN: cipher comes from a variable;
    // can't statically resolve. Documented limitation.
    public function fn_variable_cipher($cfg) {
        // ok: ci-insecure-encryption-config
        $this->encryption->initialize(['cipher' => $cfg]);
    }
}

// === MUST FIRE: CI4 Config\Encryption with weak cipher ===
namespace Config;

use CodeIgniter\Config\BaseConfig;

// ruleid: ci-insecure-encryption-config
class Encryption extends BaseConfig {
    /**
     * Encryption Key Starter
     */
    public string $key = '';

    /**
     * Cipher to use — WEAK (ECB mode embedded in cipher string)
     */
    public string $cipher = 'AES-256-ECB';

    public string $driver = 'OpenSSL';
}

// === MUST NOT FIRE: CI4 Config\Encryption with strong cipher ===
namespace Config;

// ok: ci-insecure-encryption-config
class StrongEncryption extends BaseConfig {
    public string $key = '';

    public string $cipher = 'AES-256-CTR';

    public string $driver = 'OpenSSL';
}

// === Scope guard: a class outside Config namespace with $cipher property
// — the rule fires on any class extends BaseConfig with a weak cipher
// property. This is acceptable: if a non-CI codebase happens to have
// `class Foo extends BaseConfig { public string $cipher = 'DES-CBC'; }`,
// the rule fires (FP). But that pattern is essentially CI-shape and
// the FP cost is bounded. ===
namespace App\Models;

class ProductCipher {
    // The class doesn't extend BaseConfig, so it doesn't match the CI4
    // pattern — should be ok regardless of property value.
    // ok: ci-insecure-encryption-config
    public string $cipher = 'DES-CBC';
}

namespace App;

// Plain class with $this->encrypt — not a CI controller, not the
// MCrypt library. The rule's MCrypt detection IS broad here (any
// $this->encrypt->encode/decode call), so this is acceptable known-FP risk.
class CustomEncrypt {
    public function process($data) {
        // ruleid: ci-insecure-encryption-config
        return $this->encrypt->encode($data);
    }
}
