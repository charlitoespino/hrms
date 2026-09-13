# Philippine HR Management System (HRMS)

A functional, secure, OOP PHP + MySQL + Bootstrap 5.3 HRMS foundation tailored to a
Philippine-based company. Government contribution and tax rules are **configurable
via database tables** so HR/Payroll can update them when official rates change.

> **Disclaimer:** The sample SSS, PhilHealth, Pag-IBIG, and BIR values in
> `database/seed.sql` are **illustrative only**. This system is **not** claimed to be
> officially compliant with current Philippine government regulations. Verify and
> update all rates against current official sources before production use.

---

## Requirements

- PHP 8.2+
- MySQL 8+
- Apache with `mod_rewrite` (or Nginx equivalent)
- PHP extensions: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`

---

## Installation

1. Copy the project into your web root (e.g. `htdocs/hrms`).
2. Create a MySQL database named `hrms` (utf8mb4).
3. Import the schema:
   ```bash
   mysql -u root -p hrms < database/schema.sql
   ```
4. Import the seed data:
   ```bash
   mysql -u root -p hrms < database/seed.sql
   ```
5. Copy `.env.example` to `.env` and set DB credentials, `APP_URL`, and `APP_KEY`.
6. Make sure `public/uploads` and `storage/logs` are writable by the web server.
7. Point your browser to `http://localhost/hrms/public/` (or configure a vhost so the
   document root is the `public/` directory).

---

## Sample Logins

All demo accounts use password: `Password123!`

| Email                    | Role           |
|--------------------------|----------------|
| admin@example.com        | Super Admin    |
| hr@example.com           | HR Admin       |
| payroll@example.com      | Payroll Admin  |
| manager@example.com      | Manager        |
| employee@example.com     | Employee       |

**Do NOT use these credentials in production.**

---

## Architecture

```
public/index.php  →  routes/web.php  →  Controller
                                          ↓
                                       Service
                                          ↓
                                       Model (BaseModel)
                                          ↓
                                       PDO / MySQL
```

- **Controllers** – request handling, validation, authorization.
- **Services** – business logic (payroll, contributions, tax, leave).
- **Models** – data access via PDO prepared statements.
- **Middleware** – auth + permission checks.
- **Helpers** – Security, CSRF, Input, Validator, Response, Logger, Flash, Audit, Notification.

---

## Security

- `password_hash()` / `password_verify()` — never plain text.
- PDO prepared statements throughout — no string concatenation of user input.
- CSRF tokens on all POST forms and AJAX (`_csrf` field or `X-CSRF-Token` header).
- XSS output escaping via `e()` (`htmlspecialchars`) in every view.
- Secure session cookies: `HttpOnly`, `SameSite=Lax`, optional `Secure` for HTTPS.
- Session regeneration on login; idle timeout enforcement.
- Login throttling: configurable max failed attempts + lockout window.
- Role-based access control at the **server** (middleware) — never trust the UI.
- Soft delete on employees, departments, positions, branches.
- Audit logging for sensitive actions.
- File-upload helpers (`Security::safeFilename`) and MIME/size limits.
- Production error handling hides SQL details; details are written to `storage/logs/app.log`.

---

## Payroll

- Contribution rates come from `government_contribution_tables`.
- BIR withholding brackets come from `tax_tables`.
- Employees vs. employer shares are separated; employer contributions are never
  deducted from net pay.
- Locked payroll periods cannot be reprocessed.
- Payslips are auto-generated when a period is locked.

To update rates:
1. Insert a new row with the new `effective_from` date (leave previous rows in place).
2. Old payroll runs continue using their effective dates.
3. **Always verify official values before production.**

---

## Roles

- **Super Admin** – full access
- **HR Admin** – employees, departments, positions, attendance, leave, recruitment, training, performance, reports
- **Payroll Admin** – payroll, contributions, tax, payslips, payroll reports
- **Manager** – team employees, attendance, leave approval, overtime, performance
- **Employee** – own profile, attendance, leave, payslips, documents, training, performance, requests

---

## Folder Structure

```
/hrms
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   ├── Middleware/
│   ├── Helpers/
│   └── Views/
├── config/
├── database/ (schema.sql, seed.sql)
├── public/   (index.php, .htaccess, assets/, uploads/)
├── routes/
├── storage/logs/
└── README.md
```

---

## Development Notes

- No PHP frameworks used — plain OOP PHP with a tiny PSR-4-ish autoloader.
- Extension points: add new modules by creating a Model + Service + Controller and
  registering routes in `routes/web.php`.
- `Audit::log()` and `Notification::send()` can be dropped into any action.

---

## License

Provided as a starting foundation. Review and adapt per your organization’s
compliance requirements before production use.