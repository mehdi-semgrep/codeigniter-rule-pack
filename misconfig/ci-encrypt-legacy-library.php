<?php

class CryptoController extends CI_Controller {
    public function encode_data($data) {
        // ruleid: ci-encrypt-legacy-library
        return $this->encrypt->encode($data);
    }

    public function decode_data($cipher) {
        // ruleid: ci-encrypt-legacy-library
        return $this->encrypt->decode($cipher);
    }

    public function legacy_sha1($x) {
        // ruleid: ci-encrypt-legacy-library
        return $this->encrypt->sha1($x);
    }

    public function configure() {
        // ruleid: ci-encrypt-legacy-library
        $this->encrypt->set_cipher(MCRYPT_RIJNDAEL_128);
        // ruleid: ci-encrypt-legacy-library
        $this->encrypt->set_mode(MCRYPT_MODE_CBC);
        // ruleid: ci-encrypt-legacy-library
        $this->encrypt->set_key('some-key');
    }

    // CI3 modern Encryption library — different name (-ion). MUST NOT fire.
    public function modern_encrypt($data) {
        // ok: ci-encrypt-legacy-library
        return $this->encryption->encrypt($data);
    }

    public function modern_decrypt($cipher) {
        // ok: ci-encrypt-legacy-library
        return $this->encryption->decrypt($cipher);
    }

    // Migration helper: CI3 added encode_from_legacy() specifically for
    // reading CI2 ciphertext to re-encrypt with the modern library.
    // Legitimate one-time migration use — MUST NOT fire.
    public function migrate_legacy_data($old_cipher) {
        // ok: ci-encrypt-legacy-library
        $plain = $this->encrypt->encode_from_legacy($old_cipher);
        return $this->encryption->encrypt($plain);
    }
}

// Helper-function context: `$ci = get_instance()` then $ci->encrypt->...
// Metavariable `$OBJ->encrypt->...` must catch this.
function helper_encode($data) {
    $ci =& get_instance();
    // ruleid: ci-encrypt-legacy-library
    return $ci->encrypt->encode($data);
}

// Framework class definition itself — must NOT fire (the `Encrypt`
// class IS the implementation we're warning about). The `CI_*` prefix
// pattern-not-inside excludes this.
class CI_Encrypt {
    public function encode($data) {
        // ok: ci-encrypt-legacy-library
        return base64_encode(self::_xor_encode($data, $this->encryption_key));
    }

    public function decode($data) {
        // ok: ci-encrypt-legacy-library
        return self::_xor_decode(base64_decode($data), $this->encryption_key);
    }

    public function sha1($str) {
        // ok: ci-encrypt-legacy-library
        return sha1($str);
    }

    private function _xor_encode($string, $key) {
        return $string;
    }

    private function _xor_decode($string, $key) {
        return $string;
    }
}
