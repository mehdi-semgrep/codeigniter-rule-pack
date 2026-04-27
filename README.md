# CodeIgniter security pack — production rules

18 high-precision Semgrep rules for CodeIgniter (CI2/CI3/CI4) PHP applications. Validated against a 30-repo benchmark with 3-model adversarial triage under the Semgrep Pro engine. Overall pack precision: **94.3%** (300 TP / 18 FP, n=318); per-rule precision range 85.7% – 100%.

## Pack contents

### `injection/` — Track A blocking injection rules (5)

| Rule | CWE | Precision | Sample | Notes |
|---|---|---:|---:|---|
| `ci-deserialisation` | CWE-502 | 100% | n=1 | CI input → `unserialize`. |
| `ci-filename-control` | CWE-73 | 97.4% | n=39 | CI3 + CI4 file uploads. |
| `ci-mass-assignment` | CWE-915 | 100% | n=6 | CI Active Record array-key shape (`$this->db->insert($T, $_POST)`). No generic PHP rule covers it. |
| `ci-path-traversal` | CWE-22 | 85.7% | n=7 | CI view loaders, raw PHP file ops. Manual review recommended per finding. |
| `ci-xss-output` | CWE-79 | 93.3% | n=179 | CI3 output library + CI4 response/view helpers + raw echo/print. ~64% location overlap with `p/php` rules `taint-unsafe-echo-tag` and `echoed-request` — clients running both packs may want to dedupe. |

### `misconfig/` — Track C/D config and behavior rules (13)

| Rule | CWE | Precision | Sample | Notes |
|---|---|---:|---:|---|
| `ci-auto-routing-enabled` | CWE-1188 | 100% | n=2 | CI4 `setAutoRoute(true)` exposes every public controller method as a routable URL. |
| `ci-cookie-no-httponly` | CWE-1004 | 100% | n=9 | `cookie_httponly=FALSE` (CI3) / `$cookieHTTPOnly=false` (CI4). |
| `ci-cookie-no-secure` | CWE-614 | 100% | n=14 | `cookie_secure=FALSE` (CI3) / `$cookieSecure=false` (CI4). |
| `ci-cookie-sessions` | CWE-565 | 100% | n=5 | `sess_driver='cookie'` / `sess_use_database=FALSE`. |
| `ci-csrf-disabled` | CWE-352 | 100% | n=11 | `csrf_protection=FALSE` (CI3) / `$CSRFProtection=false` (CI4). |
| `ci-empty-encryption-key` | CWE-321 | 100% | n=7 | Empty / default `encryption_key`. |
| `ci-enable-query-strings` | CWE-913 | n=0 | — | Defensive: CI3 `enable_query_strings=TRUE` bypasses segment routing. |
| `ci-encrypt-legacy-library` | CWE-327 | 100% | n=10 | CI2 `Encrypt` library — XOR + base64. Migrate to CI3+ `Encryption` or modern PHP crypto. Disjoint scope from `ci-insecure-encryption-config`. |
| `ci-error-leakage` | CWE-209 | 86.7% | n=15 | `db_debug=TRUE` outside ENVIRONMENT branches. |
| `ci-hardcoded-db-creds` | CWE-798 | 100% | n=2 | Non-empty `password` in CI db config. |
| `ci-insecure-encryption-config` | CWE-327 | n=0 | — | Defensive: weak ciphers (DES/3DES/RC2/RC4), ECB, MD5/SHA1 HMAC. Disjoint scope from `ci-encrypt-legacy-library`. |
| `ci-migration-enabled-prod` | CWE-16 | 100% | n=1 | `migration_enabled=TRUE` in `migration.php`. |
| `ci-unrestricted-upload` | CWE-434 | 100% | n=2 | CI3 Upload library `allowed_types` misconfig — wildcard, empty, or dangerous-extension whitelist (php/phtml/phar/html/svg/exe/sh/htaccess). |

## Coverage by OWASP Top 10 (2021)

| OWASP | CWEs covered |
|---|---|
| A01 Broken Access Control | CWE-22, CWE-73, CWE-352, CWE-1188 |
| A02 Cryptographic Failures | CWE-321, CWE-327 |
| A03 Injection | CWE-79, CWE-502 |
| A04 Insecure Design | CWE-434, CWE-915 |
| A05 Security Misconfiguration | CWE-16, CWE-209, CWE-565, CWE-614, CWE-798, CWE-913, CWE-1004 |

## Running the pack

```bash
semgrep scan --pro --config php/codeigniter_production <target>
```

The `--pro` flag is recommended: precision figures in this README are measured under Pro. Without `--pro`, taint-mode rules under-fire on intra-file cross-function flows.

## Publishing to your Semgrep org

```bash
gh repo clone mehdi-semgrep/codeigniter-rule-pack
cd codeigniter-rule-pack
semgrep login
semgrep publish --visibility unlisted .
```

After publishing, the rules are reachable in any scan as `--config <your-org-slug>.ci-<rule-name>` or as a pack via `--config p/<your-org-slug>`.
