<?php

namespace Config;

// CI4 Routes config — typical canonical pattern.
$routes = Services::routes();

// ruleid: ci-auto-routing-enabled
$routes->setAutoRoute(true);

// ruleid: ci-auto-routing-enabled
$routes->setAutoRoute(TRUE);

// Renamed receiver — metavariable `$ANY->...` should still match.
$r = Services::routes();
// ruleid: ci-auto-routing-enabled
$r->setAutoRoute(true);

// Dev-gated — fires intentionally per design decision (rule is
// structural; dev systems are commonly exposed).
if (ENVIRONMENT === 'development') {
    // ruleid: ci-auto-routing-enabled
    $routes->setAutoRoute(true);
}

// ok: ci-auto-routing-enabled
$routes->setAutoRoute(false);

// ok: ci-auto-routing-enabled
$routes->setAutoRoute(FALSE);

// ok: ci-auto-routing-enabled
// Different setter; should not fire.
$routes->setDefaultNamespace('App\Controllers');

// ok: ci-auto-routing-enabled
// Defined route — the safe pattern.
$routes->get('users', 'UserController::index');

// ok: ci-auto-routing-enabled
// Defined route POST.
$routes->post('users', 'UserController::create');
