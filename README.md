# Cold Storage Operations

Web application for managing cold storage, farmer relationships, seed and potato programs, logistics, and related reporting. It gives staff a browser-based administrator console and exposes a versioned HTTP API for mobile or integrated clients.

## What it does

The system centers on **farmers** and tracks their journey through commercial agreements, seed bookings, distributions, packaging, payments, and physical storage events. Supporting **master data** includes seed companies, seed varieties, transporters, vehicles, cold storage locations, and unloading companies. **Challans** and **storage loading / unloading** records tie movements to farmers and facilities. **User and role** management (via Laravel Permission) controls who can access which areas.

**Reporting** covers processing-style views, per-farmer summaries, storage and financial angles, with exports to PDF and Excel. Some modules support **financial-year filtering** and **creator attribution** on records for audit-style visibility.

## SMS integration

The application integrates with **SMSCountry** (HTTP REST API). Outbound messages use **approved templates** registered on the provider side; the app resolves template text by ID, substitutes placeholders, and posts the final text with the configured sender ID.

**Credentials and defaults** are read from environment variables exposed through Laravel’s `services` config: account key, auth token, default sender ID, and **per–business-event template IDs** (farmer registration, seed booking, seed distribution, potato booking, packaging distribution, storage loading, and advance payment). The service can also attach optional **delivery-receipt callback** URL and HTTP method when those values are defined.

**Sending behavior:** A dedicated `SmsService` authenticates with Basic auth, lists sender IDs and templates from the provider, sends a **single** SMS by expanding a template with variables (placeholders support sequential asterisk replacement or named `{key}` style replacement), and can send **bulk** template SMS to multiple numbers. Requests and outcomes are logged; empty or HTML error responses from the API are handled as failures.

**Where SMS is used in the product:** The same service is injected into domain services so that notifications fire on relevant **create/update** flows—for example farmer registration, potato agreements, seeds booking, seed and packaging distribution, advance payments, and storage loading—using the template ID mapped to each workflow in configuration.

**Operator testing (admin):** Authenticated staff can open an **SMS test** screen that loads available templates, and call JSON endpoints to fetch sender IDs and templates, send a **single** test SMS (validated as a ten-digit mobile number), or run a **bulk** template test. These routes exist for verification and troubleshooting, separate from the live business triggers above.

## Architecture at a glance

- **Backend:** Laravel (PHP 8.2+), service classes per domain area, form requests for validation, API resources for JSON shaping.
- **Admin UI:** Livewire components for interactive tables and forms; Blade layouts; Select2-style AJAX search on several modules for farmers and related entities.
- **Authentication:** Laravel Breeze-style session auth for the web admin; routes live under an **administrator** path prefix (login, register, password flows, email verification).
- **API:** REST-style **v1** routes secured with **Laravel Sanctum** token authentication; registration and login are public; profile and password endpoints are protected. Domain resources are exposed as API resources with dedicated PDF download endpoints where applicable.
- **Documents:** PDF generation (DomPDF) and spreadsheets (Maatwebsite Excel); OpenAPI description available through **L5 Swagger** for API exploration.
- **Frontend assets:** Vite is referenced in the Composer dev workflow; public assets include vendor UI pieces (e.g. Select2) as needed.

## Request flow (high level)

1. **Public site** — Visitors hit the default welcome page. Static-style pages include privacy policy and contact. A quote form can submit to the application for follow-up (email via the mail stack).
2. **Staff sign-in** — Unauthenticated users use the administrator login (and related auth screens). After verification, they reach the **dashboard** and module menus.
3. **Admin modules** — Each business area is a named section (farmers, companies, seed varieties, seeds booking, agreements / potato bookings, seed and packaging distributions, advance payments, transporters, vehicles, cold storages, storage loadings and unloadings, challans, unloading companies, users, roles). List screens use Livewire tables; create/update flows use forms wired to services and validation. Search endpoints back AJAX dropdowns where configured.
4. **Reports** — A reports hub links to processing, farmer, storage, and financial report views, plus farmer drill-downs (agreements, payments, challans, loadings). Exports post back to the server for PDF or Excel generation.
5. **API clients** — Clients obtain a token via login, then call versioned endpoints for CRUD and lookups (companies, varieties, vehicles, cold storages, transporters, unloading companies, etc.). Several entities offer **download PDF** actions as separate GET routes.

## Main domain areas (modules)

| Area | Purpose (conceptual) |
|------|----------------------|
| Farmers | Core party; linked to bookings, agreements, payments, and logistics. |
| Companies & seed varieties | Catalog for seed supply side. |
| Seeds booking & agreements | Bookings and potato/agreement style commitments; financial-year aware in places. |
| Seed & packaging distribution | Outbound seed and packaging allocation to farmers. |
| Advance payments | Financial prepayments tied to the program. |
| Transporters, vehicles, cold storages | Logistics and facility master data. |
| Storage loading / unloading | In/out of storage with optional vehicle linkage; unloading companies as a dimension. |
| Challans | Transport or dispatch documentation style records. |
| Users & roles | Access control for the admin application. |

## Dependencies (runtime)

Key Composer packages include Laravel Framework 12, Livewire 3, Sanctum, Spatie Laravel Permission, Maatwebsite Excel, Laravel DomPDF, and L5 Swagger. Development tools include Breeze, Pint, PHPUnit, Debugbar, and optional Sail/Pail per the project’s Composer configuration.

## Local setup (outline)

- PHP 8.2 or newer and Composer; a web server (e.g. WAMP) or `php artisan serve`.
- Copy environment configuration from the project’s example env file, set `APP_KEY`, database credentials, and mail/SMS-related values as required.
- Run Composer install, run database migrations (and seeders if the team provides them).
- For the full dev script defined in Composer, Node is used alongside Artisan for queues, logs, and the Vite dev server.

## Project layout (where things live)

- **Application code:** under `app/` — HTTP controllers (admin, API, auth, front pages), Livewire admin components, Eloquent models, domain services, exports, mail, and shared traits.
- **Routes:** `routes/web.php` (public + authenticated admin), `routes/api.php` (v1 API), `routes/auth.php` (Breeze-style auth under the administrator prefix).
- **Database:** migrations and seeders under `database/`; SQLite is present for local/default use in the tree.
- **UI:** Blade views and Livewire views under `resources/views`; front-end entry points follow Laravel’s usual `resources` conventions.
- **Tests:** PHPUnit tests under `tests/`.

## License

The Laravel framework is open source under the MIT license. This application’s overall licensing follows the same unless your organization has specified otherwise.
