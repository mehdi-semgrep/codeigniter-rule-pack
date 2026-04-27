<?php

use CodeIgniter\HTTP\IncomingRequest;

class DeserialController_CI3 extends CI_Controller {
    public function vuln_unserialize_cookie() {
        $c = $_COOKIE['data'];
        // ruleid: ci-deserialisation
        $data = unserialize($c);
    }

    public function vuln_unserialize_input() {
        $x = $this->input->post('data');
        // ruleid: ci-deserialisation
        unserialize($x);
    }

    public function vuln_yaml_parse() {
        $y = $this->input->post('yaml');
        // ruleid: ci-deserialisation
        yaml_parse($y);
    }

    public function safe_literal() {
        // ok: ci-deserialisation
        unserialize('N;');
    }
}

class DeserialController_CI4 extends \App\Controllers\BaseController {
    public function vuln(IncomingRequest $request) {
        $c = $request->getCookie('d');
        // ruleid: ci-deserialisation
        unserialize($c);
    }
}

class PlainDeserClass {
    public function notCi($x) {
        // ok: ci-deserialisation
        unserialize($x);
    }
}
