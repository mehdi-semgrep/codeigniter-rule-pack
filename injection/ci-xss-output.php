<?php

// Track A test file for ci-xss-output

use CodeIgniter\HTTP\IncomingRequest;

// CI3: $this->input->get -> $this->output->set_output
class DisplayController_CI3 extends CI_Controller {
    public function vuln_set_output() {
        $name = $this->input->get('name');
        // ruleid: ci-xss-output
        $this->output->set_output("Hello " . $name);
    }

    public function vuln_with_header() {
        $n = $this->input->post('name');
        // ruleid: ci-xss-output
        $this->output->set_content_type('text/html')->set_output($n);
    }

    public function safe_escaped() {
        $name = $this->input->get('name');
        $safe = htmlspecialchars($name);
        // ok: ci-xss-output
        $this->output->set_output("Hello " . $safe);
    }

    public function safe_esc_helper() {
        $name = $this->input->get('name');
        // ok: ci-xss-output
        $this->output->set_output(esc($name));
    }

    public function safe_literal() {
        // ok: ci-xss-output
        $this->output->set_output("Static page content");
    }
}

// CI4: $response->setBody
class DisplayController_CI4 extends \App\Controllers\BaseController {
    public function vuln_setbody(IncomingRequest $request) {
        $q = $request->getGet('q');
        // ruleid: ci-xss-output
        $this->response->setBody("Query was " . $q);
    }

    public function safe_setbody_escaped(IncomingRequest $request) {
        $q = $request->getGet('q');
        // ok: ci-xss-output
        $this->response->setBody(htmlspecialchars($q));
    }

    public function safe_setbody_literal() {
        // ok: ci-xss-output
        $this->response->setBody("static content");
    }
}

// Twig: render with tainted data
class TwigController extends CI_Controller {
    public function vuln_twig() {
        $name = $this->input->post('name');
        $twig = new \Twig\Environment(null);
        // ruleid: ci-xss-output
        $twig->render("page.twig", ["name" => $name]);
    }
}

// REST_Controller source
class ApiXssController extends REST_Controller {
    public function users_get() {
        $q = $this->get('q');
        // ruleid: ci-xss-output
        $this->output->set_output("Results: " . $q);
    }
}

// Untyped route param
class UntypedXssController extends \App\Controllers\BaseController {
    public function show($name) {
        // ruleid: ci-xss-output
        $this->response->setBody("Hello " . $name);
    }
}

// New sink coverage — CI4 view() helper
class ViewHelperController extends \App\Controllers\BaseController {
    public function vuln_view(IncomingRequest $request) {
        $name = $request->getGet('name');
        // ruleid: ci-xss-output
        return view('welcome', ['name' => $name]);
    }

    public function safe_view_literal() {
        // ok: ci-xss-output
        return view('welcome', ['name' => 'Static']);
    }
}

// Raw echo / print
class EchoController extends CI_Controller {
    public function vuln_echo() {
        $n = $this->input->post('name');
        // ruleid: ci-xss-output
        echo $n;
    }

    public function vuln_print() {
        $n = $this->input->post('name');
        // ruleid: ci-xss-output
        print $n;
    }

    public function safe_echo_literal() {
        // ok: ci-xss-output
        echo "Hello world";
    }

    public function safe_echo_escaped() {
        $n = $this->input->post('name');
        // ok: ci-xss-output
        echo esc($n);
    }
}

// Scope guard
class PlainXssClass {
    public function notCi($x) {
        // ok: ci-xss-output
        $this->output->set_output($x);
    }
}

// Tier-1: JSON/URL encoding sanitizers
class JsonEncodeController extends CI_Controller {
    public function safe_json_encode() {
        $data = $this->input->post('data');
        // ok: ci-xss-output
        $this->output->set_output(json_encode($data));
    }

    public function safe_http_build_query() {
        $params = $this->input->post('params');
        // ok: ci-xss-output
        $this->output->set_output(http_build_query($params));
    }

    public function safe_urlencode() {
        $name = $this->input->get('name');
        // ok: ci-xss-output
        echo urlencode($name);
    }
}

// Tier-1: date() transformation produces harmless output
class DateOutputController extends CI_Controller {
    public function safe_date_format() {
        $ts = $this->input->get('ts');
        // ok: ci-xss-output
        $this->output->set_output(date('Y-m-d', $ts));
    }
}

// FN-risk coverage: plain echo of tainted data still fires even with new sanitizers
class FnRiskEchoController extends CI_Controller {
    public function vuln_raw_echo_stays_vuln() {
        $n = $this->input->post('name');
        // ruleid: ci-xss-output
        echo "Hello " . $n;
    }
}
