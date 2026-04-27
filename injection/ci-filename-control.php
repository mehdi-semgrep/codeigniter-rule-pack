<?php

use CodeIgniter\HTTP\IncomingRequest;

class UploadController_CI4 extends \App\Controllers\BaseController {
    public function vuln_move(IncomingRequest $request) {
        $name = $request->getPost('name');
        $file = $request->getFile('upload');
        // ruleid: ci-filename-control
        $file->move(WRITEPATH . 'uploads', $name);
    }

    public function safe_random_name(IncomingRequest $request) {
        $file = $request->getFile('upload');
        $name = $file->getRandomName();
        // ok: ci-filename-control
        $file->move(WRITEPATH . 'uploads', $name);
    }

    public function safe_uniqid(IncomingRequest $request) {
        $file = $request->getFile('upload');
        $name = uniqid();
        // ok: ci-filename-control
        $file->move(WRITEPATH . 'uploads', $name);
    }
}

class UploadController_CI3 extends CI_Controller {
    public function vuln_initialize() {
        $n = $this->input->post('name');
        // ruleid: ci-filename-control
        $this->upload->initialize(['file_name' => $n, 'allowed_types' => 'gif|jpg']);
    }

    public function vuln_config_array() {
        $n = $this->input->post('name');
        $config = array();
        // ruleid: ci-filename-control
        $config['file_name'] = $n;
    }

    public function safe_initialize_literal() {
        // ok: ci-filename-control
        $this->upload->initialize(['file_name' => 'static.jpg', 'allowed_types' => 'jpg']);
    }
}

// New sink coverage — raw PHP filesystem sinks
class RawFileWriteController extends CI_Controller {
    public function vuln_move_uploaded_file() {
        $name = $this->input->post('name');
        // ruleid: ci-filename-control
        move_uploaded_file($_FILES['upload']['tmp_name'], '/uploads/' . $name);
    }

    public function vuln_file_put_contents() {
        $name = $this->input->post('name');
        // ruleid: ci-filename-control
        file_put_contents('/uploads/' . $name, "content");
    }

    public function vuln_rename() {
        $name = $this->input->post('name');
        // ruleid: ci-filename-control
        rename('/tmp/upload', '/uploads/' . $name);
    }

    public function safe_literal_move() {
        // ok: ci-filename-control
        move_uploaded_file($_FILES['upload']['tmp_name'], '/uploads/static.txt');
    }
}

class PlainFileClass {
    public function notCi($n, $f) {
        // ok: ci-filename-control
        $f->move("/tmp", $n);
    }
}

// Tier-1: CI3 framework sanitize_filename
class Ci3SanitizeController extends CI_Controller {
    public function safe_sanitize_filename() {
        $n = $this->input->post('name');
        $safe = $this->security->sanitize_filename($n);
        // ok: ci-filename-control
        $this->upload->initialize(['file_name' => $safe, 'allowed_types' => 'gif|jpg']);
    }

    // clean_file_name does NOT fully sanitize — user still controls the
    // basename and extension. Flag remains active.
    public function vuln_clean_file_name_insufficient() {
        $n = $this->input->post('name');
        $safe = $this->upload->clean_file_name($n);
        // ruleid: ci-filename-control
        move_uploaded_file($_FILES['upload']['tmp_name'], '/uploads/' . $safe);
    }
}

// Tier-1: CI4 static sanitizer
class Ci4SanitizeFilenameController extends \App\Controllers\BaseController {
    public function safe_ci4_static(\CodeIgniter\HTTP\IncomingRequest $request) {
        $n = $request->getPost('name');
        $safe = \CodeIgniter\Security\Security::sanitizeFilename($n);
        $file = $request->getFile('upload');
        // ok: ci-filename-control
        $file->move(WRITEPATH . 'uploads', $safe);
    }
}
