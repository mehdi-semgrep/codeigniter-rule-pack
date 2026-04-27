<?php

// Wildcard.
class WildcardConfigController extends CI_Controller {
    public function vuln_wildcard_inline() {
        // ruleid: ci-unrestricted-upload
        $this->upload->initialize(['allowed_types' => '*', 'upload_path' => '/tmp']);
    }

    public function vuln_wildcard_array() {
        // ruleid: ci-unrestricted-upload
        $config['allowed_types'] = '*';
    }

    public function vuln_wildcard_double_quoted() {
        // ruleid: ci-unrestricted-upload
        $this->upload->initialize(['allowed_types' => "*", 'upload_path' => '/tmp']);
    }
}

// Empty.
class EmptyConfigController extends CI_Controller {
    public function vuln_empty_string() {
        // ruleid: ci-unrestricted-upload
        $config['allowed_types'] = '';
    }

    public function vuln_empty_inline() {
        // ruleid: ci-unrestricted-upload
        $this->upload->initialize(['allowed_types' => '', 'upload_path' => '/tmp']);
    }
}

// Dangerous extensions.
class DangerousExtController extends CI_Controller {
    public function vuln_php() {
        // ruleid: ci-unrestricted-upload
        $config['allowed_types'] = 'jpg|php';
    }

    public function vuln_phtml() {
        // ruleid: ci-unrestricted-upload
        $this->upload->initialize(['allowed_types' => 'phtml|jpg', 'upload_path' => '/tmp']);
    }

    public function vuln_html() {
        // ruleid: ci-unrestricted-upload
        $config['allowed_types'] = 'html|jpg|png';
    }

    public function vuln_svg() {
        // ruleid: ci-unrestricted-upload
        $config['allowed_types'] = 'png|svg';
    }

    public function vuln_phar() {
        // ruleid: ci-unrestricted-upload
        $config['allowed_types'] = 'phar';
    }

    public function vuln_phps() {
        // ruleid: ci-unrestricted-upload
        $config['allowed_types'] = 'phps';
    }

    public function safe_pdf() {
        // ok: ci-unrestricted-upload
        $config['allowed_types'] = 'pdf|docx';
    }

    public function safe_image_only() {
        // ok: ci-unrestricted-upload
        $this->upload->initialize(['allowed_types' => 'gif|jpg|png|jpeg', 'upload_path' => '/tmp']);
    }

    public function safe_csv() {
        // ok: ci-unrestricted-upload
        $config['allowed_types'] = 'csv';
    }

    public function safe_zip() {
        // ok: ci-unrestricted-upload
        $config['allowed_types'] = 'zip';
    }
}

// Negative: bare do_upload() without any config — Layer 2 stripped, MUST NOT fire.
class BareDoUploadController extends CI_Controller {
    public function bare_do_upload($field) {
        // ok: ci-unrestricted-upload
        $this->upload->do_upload($field);
    }

    public function initialize_without_allowed_then_upload() {
        $this->upload->initialize(['upload_path' => '/tmp']);
        // ok: ci-unrestricted-upload
        $this->upload->do_upload('userfile');
    }
}
