<?php

// Track A test file for ci-mass-assignment.

use CodeIgniter\HTTP\IncomingRequest;

// === MUST FIRE: Case 1 — $this->db direct receiver in CI class ===
class UsersController extends CI_Controller {
    public function vuln_insert_post() {
        // ruleid: ci-mass-assignment
        $this->db->insert('users', $_POST);
    }

    public function vuln_insert_request() {
        // ruleid: ci-mass-assignment
        $this->db->insert('users', $_REQUEST);
    }

    public function vuln_insert_input_post_no_args() {
        // ruleid: ci-mass-assignment
        $this->db->insert('users', $this->input->post());
    }

    public function vuln_insert_input_post_null() {
        // ruleid: ci-mass-assignment
        $this->db->insert('users', $this->input->post(NULL));
    }

    public function vuln_update_post($id) {
        // ruleid: ci-mass-assignment
        $this->db->update('users', $_POST, array('id' => $id));
    }

    public function vuln_update_input_post($id) {
        // ruleid: ci-mass-assignment
        $this->db->update('users', $this->input->post(), ['id' => $id]);
    }

    public function vuln_replace_post() {
        // ruleid: ci-mass-assignment
        $this->db->replace('users', $_POST);
    }
}

class UserModelMy extends MY_Model {
    public function vuln_save_post() {
        // ruleid: ci-mass-assignment
        $this->db->insert($this->table, $_POST);
    }
}

class UserModel extends CI_Model {
    public function vuln_update_assoc_input($id) {
        // ruleid: ci-mass-assignment
        $this->db->update('users', $this->input->post(), array('id' => $id));
    }
}

// CI4 BaseController and Model contexts
class CI4UsersController extends \App\Controllers\BaseController {
    public function vuln_builder_insert(IncomingRequest $request) {
        // ruleid: ci-mass-assignment
        $this->db->table('users')->insert($this->request->getPost());
    }

    public function vuln_builder_update_post() {
        // ruleid: ci-mass-assignment
        $this->db->table('users')->update($_POST, ['id' => 1]);
    }

    public function vuln_builder_get_json() {
        // ruleid: ci-mass-assignment
        $this->db->table('users')->insert($this->request->getJSON(true));
    }
}

class CI4UserModel extends \CodeIgniter\Model {
    public function vuln_legacy_insert() {
        // ruleid: ci-mass-assignment
        $this->db->insert('users', $_POST);
    }
}

// REST_Controller context
class RestUserController extends REST_Controller {
    public function users_post() {
        // ruleid: ci-mass-assignment
        $this->db->insert('users', $this->input->post());
    }
}

// === MUST FIRE: Case 2 — $this->$P->db library-helper receiver ===
// Pattern: scaffolding/admin libraries that hold $CI = get_instance()
// as $this->CI and call $this->CI->db->...
class ScaffoldingLib {
    private $CI;

    public function __construct() {
        $this->CI =& get_instance();
    }

    public function vuln_lib_insert($table) {
        // ruleid: ci-mass-assignment
        $this->CI->db->insert($table, $_POST);
    }

    public function vuln_lib_update($table, $where) {
        // ruleid: ci-mass-assignment
        $this->CI->db->update($table, $_POST, $where);
    }

    public function vuln_lib_input_post($table) {
        // ruleid: ci-mass-assignment
        $this->CI->db->insert($table, $this->input->post());
    }

    public function vuln_lib_replace($table) {
        // ruleid: ci-mass-assignment
        $this->CI->db->replace($table, $_POST);
    }

    // CI4 builder via library-helper receiver
    public function vuln_lib_builder($table) {
        // ruleid: ci-mass-assignment
        $this->CI->db->table($table)->insert($this->request->getPost());
    }
}

// Underscore-property-name variant
class UnderscoreLib {
    private $_ci;

    public function vuln($table) {
        // ruleid: ci-mass-assignment
        $this->_ci->db->insert($table, $_POST);
    }
}

// === MUST FIRE: Case 3 — $DB from db_connect() / service / Services ===
class CI4ServiceController extends \App\Controllers\BaseController {
    public function vuln_db_connect_insert() {
        $db = db_connect();
        // ruleid: ci-mass-assignment
        $db->insert('users', $_POST);
    }

    public function vuln_service_insert() {
        $db = service('db');
        // ruleid: ci-mass-assignment
        $db->insert('users', $this->request->getPost());
    }

    public function vuln_services_database_builder() {
        $db = \Config\Services::database();
        // ruleid: ci-mass-assignment
        $db->table('users')->insert($_POST);
    }
}


// === MUST NOT FIRE: safe shapes ===
class SafeUsersController extends CI_Controller {
    // S1 — explicit array literal with hardcoded keys
    public function safe_literal_array() {
        // ok: ci-mass-assignment
        $this->db->insert('users', ['name' => 'fixed', 'created_at' => time()]);
    }

    // S2 — CI3 array-keys filter (returns only listed keys)
    public function safe_array_filter_form() {
        // ok: ci-mass-assignment
        $this->db->insert('users', $this->input->post(['name', 'email']));
    }

    public function safe_update_array_filter($id) {
        // ok: ci-mass-assignment
        $this->db->update('users', $this->input->post(['name', 'email']), ['id' => $id]);
    }

    // S3 — single-key form returns scalar (not an array)
    public function safe_single_key() {
        // ok: ci-mass-assignment
        $this->db->insert('users', $this->input->post('name'));
    }

    // S4 — CI4 array filter
    public function safe_ci4_array_filter() {
        // ok: ci-mass-assignment
        $this->db->table('users')->insert($this->request->getPost(['name', 'email']));
    }

    // S5 — locally constructed filtered array (named keys only)
    public function safe_locally_constructed() {
        $data = [
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
        ];
        // ok: ci-mass-assignment
        $this->db->insert('users', $data);
    }

    // S5 variant — CI4 with locally constructed array
    public function safe_locally_ci4() {
        $data = [
            'name' => $this->request->getPost('name'),
        ];
        // ok: ci-mass-assignment
        $this->db->table('users')->insert($data);
    }
}

// === Aliased input — caught by taint mode ===
class AliasedUsersController extends CI_Controller {
    public function vuln_aliased() {
        $d = $_POST;
        // ruleid: ci-mass-assignment
        $this->db->insert('users', $d);
    }

    // Custom Model method (not the CI Active Record API) — out of scope.
    public function fn_aliased_via_input_post() {
        $post = $this->input->post();
        // ok: ci-mass-assignment
        $this->ui_model->update($id, $post);
    }
}

// === Scope guard — non-CI class must NOT fire ===
class PlainPhpClass {
    public function notCi($table) {
        $pdo = new PDO('...');
        // ok: ci-mass-assignment
        $pdo->insert($table, $_POST);
    }
}
