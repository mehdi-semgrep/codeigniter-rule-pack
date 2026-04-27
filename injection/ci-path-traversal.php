<?php

// Track A test file for ci-path-traversal

use CodeIgniter\HTTP\IncomingRequest;

class PathController_CI3 extends CI_Controller {
    public function vuln_load_view() {
        $page = $this->input->get('page');
        // ruleid: ci-path-traversal
        $this->load->view($page);
    }

    public function vuln_load_file() {
        $f = $this->input->post('f');
        // ruleid: ci-path-traversal
        $this->load->file($f);
    }

    public function vuln_concat() {
        $t = $this->input->get('t');
        // ruleid: ci-path-traversal
        $this->load->view("templates/" . $t);
    }

    public function safe_literal() {
        // ok: ci-path-traversal
        $this->load->view("home");
    }

    public function safe_basename() {
        $page = $this->input->get('page');
        $safe = basename($page);
        // ok: ci-path-traversal
        $this->load->view($safe);
    }

    public function safe_sanitize_filename() {
        $page = $this->input->get('page');
        $safe = $this->security->sanitize_filename($page);
        // ok: ci-path-traversal
        $this->load->view($safe);
    }
}

class PathController_CI4 extends \App\Controllers\BaseController {
    public function show(IncomingRequest $request) {
        $t = $request->getGet('t');
        // ruleid: ci-path-traversal
        $this->load->view($t);
    }
}

class PathRest extends REST_Controller {
    public function view_get() {
        $p = $this->get('p');
        // ruleid: ci-path-traversal
        $this->load->view($p);
    }
}

class UntypedPathController extends \App\Controllers\BaseController {
    public function show($page) {
        // ruleid: ci-path-traversal
        $this->load->view($page);
    }
}

// New sink coverage — CI4 view() helper
class ViewHelperPath extends \App\Controllers\BaseController {
    public function vuln_view(IncomingRequest $request) {
        $t = $request->getGet('t');
        // ruleid: ci-path-traversal
        return view($t);
    }
}

// New sink coverage — raw filesystem reads
class RawFilesController extends CI_Controller {
    public function vuln_fgc() {
        $p = $this->input->get('p');
        // ruleid: ci-path-traversal
        $contents = file_get_contents($p);
    }

    public function vuln_fopen() {
        $p = $this->input->get('p');
        // ruleid: ci-path-traversal
        $fh = fopen($p, 'r');
    }

    public function vuln_readfile() {
        $p = $this->input->get('p');
        // ruleid: ci-path-traversal
        readfile($p);
    }

    public function vuln_include() {
        $p = $this->input->get('p');
        // ruleid: ci-path-traversal
        include $p;
    }

    public function vuln_require_once() {
        $p = $this->input->get('p');
        // ruleid: ci-path-traversal
        require_once $p;
    }

    public function safe_literal_include() {
        // ok: ci-path-traversal
        include "/etc/config.php";
    }
}

class PlainPathClass {
    public function notCi($x) {
        // ok: ci-path-traversal
        $this->load->view($x);
    }
}

// Tier-1: md5/sha1/hash neutralize path separators
class HashSanitizerController extends CI_Controller {
    public function safe_md5_cache_path() {
        $key = $this->input->get('cache_key');
        $hashed = md5($key);
        // ok: ci-path-traversal
        $contents = file_get_contents('/var/cache/' . $hashed);
    }

    public function safe_sha1_cache_path() {
        $key = $this->input->get('cache_key');
        $hashed = sha1($key);
        // ok: ci-path-traversal
        $contents = file_get_contents('/var/cache/' . $hashed);
    }

    public function safe_hash_sha256_cache_path() {
        $key = $this->input->get('cache_key');
        $hashed = hash('sha256', $key);
        // ok: ci-path-traversal
        $contents = file_get_contents('/var/cache/' . $hashed);
    }
}

// Tier-1: PHP-managed temp paths are not attacker controlled
class TmpNameController extends CI_Controller {
    public function safe_tmp_name() {
        // ok: ci-path-traversal
        $contents = file_get_contents($_FILES['upload']['tmp_name']);
    }

    public function safe_tmp_name_via_move() {
        // ok: ci-path-traversal
        move_uploaded_file($_FILES['upload']['tmp_name'], '/var/uploads/static.bin');
    }
}

// Tier-1: CI4 namespaced static sanitizer
class Ci4SanitizerController extends \App\Controllers\BaseController {
    public function safe_ci4_sanitize(\CodeIgniter\HTTP\IncomingRequest $request) {
        $name = $request->getGet('name');
        $safe = \CodeIgniter\Security\Security::sanitizeFilename($name);
        // ok: ci-path-traversal
        $this->load->view($safe);
    }
}

// v2.1: CI4 security helper — bare `sanitize_filename(...)` function
// form loaded via `helper('security')`. Same sanitization, different
// invocation shape.
class Ci4SecurityHelperController extends \App\Controllers\BaseController {
    public function safe_ci4_helper(\CodeIgniter\HTTP\IncomingRequest $request) {
        helper('security');
        $name = $request->getGet('name');
        $safe = sanitize_filename('prefix_' . $name);
        // ok: ci-path-traversal
        $contents = file_get_contents(WRITEPATH . 'cache/' . $safe);
    }
}

// v2.1: directory-scan results are server-filesystem data, not
// attacker-controlled strings.
class DirectoryScanController extends CI_Controller {
    public function safe_glob() {
        foreach (glob('/var/app/configs/*.json') as $f) {
            // ok: ci-path-traversal
            $content = file_get_contents($f);
        }
    }

    public function safe_scandir() {
        foreach (scandir('/var/app/uploads') as $f) {
            if ($f === '.' || $f === '..') continue;
            // ok: ci-path-traversal
            $content = file_get_contents('/var/app/uploads/' . $f);
        }
    }

    // FN-risk check: if user input flows DIRECTLY into file_get_contents
    // without passing through a directory scan, the rule must still fire.
    public function vuln_direct_still_flags() {
        $p = $this->input->get('path');
        // ruleid: ci-path-traversal
        file_get_contents($p);
    }
}

// v2.1: foreach over $_FILES — the iterated $file variable's
// 'tmp_name' key is still the PHP-managed temp path, not an
// attacker-controlled string.
class FilesIterationController extends CI_Controller {
    public function safe_iterated_tmp_name() {
        foreach ($_FILES as $field => $file) {
            // ok: ci-path-traversal
            $contents = file_get_contents($file['tmp_name']);
        }
    }

    public function safe_indexed_tmp_name() {
        $file = $_FILES['upload'];
        // ok: ci-path-traversal
        $contents = file_get_contents($file['tmp_name']);
    }

    // FN-risk check: the 'tmp_name'-key sanitizer must not suppress
    // the case where the array itself is obviously attacker-controlled
    // (e.g., the user crafts a POST body parsed into an array with a
    // 'tmp_name' key). This is rare in practice but we exercise it:
    public function vuln_crafted_array_tmp_name() {
        $attackerShape = $this->input->post('fake_file');
        // ruleid: ci-path-traversal
        $contents = file_get_contents($attackerShape);
    }

    // FN-risk check: hash functions still neutralize path separators
    // even if we accidentally shadowed the wrong sanitizer.
    public function safe_hash_still_works() {
        $key = $this->input->get('key');
        // ok: ci-path-traversal
        $contents = file_get_contents('/var/cache/' . sha1($key));
    }
}
