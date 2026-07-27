# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Netley is a Laravel 12 + Filament 4 admin panel for a law firm's case-management workflow: intake
(`Consulta`) → scheduling (`Cita`) → client (`Cliente`) → legal case (`Caso`) and its many
sub-records (workflow stages, hearings, filings, documents, etc.). There is no separate frontend
app — the UI is entirely the Filament panel at `/admin`, server-rendered via Livewire. The `resources/`
and `vite.config.js` setup is Laravel's default Tailwind/Vite scaffold, not a custom SPA.

Everything lives under a single Filament panel registered in
`app/Providers/Filament/AdminPanelProvider.php` (id `admin`, path `/admin`).

## Commands

```bash
# Install
composer install && npm install

# Run app (server + queue worker + log tail + vite, concurrently)
composer dev

# Tests (plain PHPUnit, not Pest — test classes extend Tests\TestCase, methods are test_snake_case)
composer test               # clears config cache, then `php artisan test`
php artisan test                                   # all tests
php artisan test --filter=test_method_name          # single test
php artisan test tests/Feature/SomeTest.php          # single file

# Lint / format (Laravel Pint, PSR-12-based)
vendor/bin/pint             # fix
vendor/bin/pint --test      # check only

# Migrations (SQLite by default, see .env)
php artisan migrate
php artisan migrate:fresh --seed

# Filament Shield (roles/permissions) — regenerate after adding/changing a Resource
php artisan shield:generate --all     # (re)generates Policies + permissions for all Resources
php artisan shield:super-admin        # grant super_admin to a user
```

Tests run against an in-memory SQLite DB (`phpunit.xml`), independent of the `.env` DB.

## Architecture

### Domain flow

The business process the schema encodes, roughly in order:

1. **`Consulta`** (inquiry/lead intake). Creating one auto-creates a **`Ticket`** (`Consulta::booted()`,
   RN-001: exactly one ticket per consulta, defaults to priority "Normal" / state "Pendiente").
2. **`Cita`** (appointment) is scheduled off a consulta; `CreateConsulta` uses a `Wizard` with two
   steps (`ConsultaForm` + `CitaForm`) and creates both records in one DB transaction.
3. **`Cliente`** is the converted contact. A `Pago` (payment) is what promotes a Cliente to
   "preferente" (`Pago::booted()` sets `es_preferente = true`) — business rule: "sin pago no existe
   Cliente Preferente". A Cliente can also be given a `rol_empresa` (Abogado/Psicólogo/Procurador/…),
   which marks them as internal staff; `Cliente::generarAccesoStaff()` creates a `User` + Shield role
   and a one-time temporary password (see `GeneratesStaffAccess` trait, used from Filament pages).
   Note: staff `User` accounts get full panel access (`canAccessPanel()` is unconditionally `true`) —
   there is intentionally no separate client-facing portal/guard yet (see TODO in `Pago` model).
4. **`Caso`** (legal case) belongs to a Cliente, has an `abogado` and `procurador` (both `User`), plus
   an `equipo` (many-to-many `User` via `caso_equipo` pivot, with a `rol` column, for
   pasante/psicólogo/conciliador/etc.). On creation it auto-generates a `codigo`
   (`CASO-{year}-{padded id}`). `Caso::generarWorkflow()` seeds `WorkflowEtapa` rows from a
   per-`materia` template (`PLANTILLAS_WORKFLOW` — Divorcio/Penal/Laboral/Genérico); the first-generated
   stages are flagged `es_original = true` and can't be deleted. `Caso::motivosQueImpidenCierre()`
   / `cerrar()` implement the case-closing checks (open hearings, incomplete original workflow stages,
   pending document requests).
5. A `Caso` fans out into many child records, each its own model + migration + RelationManager under
   `CasoResource`: `Actuacion`, `Audiencia`, `Conciliacion`, `Diligencia`, `Documento`,
   `SolicitudDocumento`, `Hito`, `MedidaCautelar`, `Observacion`, `ResolucionJudicial`.

Business-rule comments in the code use two tags worth grepping for when working in this area:
`CU-0xx` (use case number) and `RN-0xx` (regla de negocio / business rule) — they explain *why*
a piece of logic exists, including intentionally unimplemented parts (search for `TODO`).

### Filament Resources (v4 split-file structure)

Every resource under `app/Filament/Resources/<Plural>/` follows this layout — mirror it exactly
when adding a new one:

```
<Plural>Resource.php              # model binding, navigation icon, getPages(), getRelations()
Pages/Create<Singular>.php        # extends CreateRecord; override getFormSchema()/handleRecordCreation() for wizards or cross-model creates
Pages/Edit<Singular>.php
Pages/List<Singular>s.php
Schemas/<Singular>Form.php        # static components(): array + static configure(Schema): Schema — form fields live here, not in the Resource
Tables/<Plural>Table.php          # static configure(Table): Table — columns/filters/actions live here
RelationManagers/<Thing>RelationManager.php   # only on resources with child records (currently only Casos/)
Concerns/<Trait>.php              # optional, shared page behavior (e.g. Clientes/Concerns/GeneratesStaffAccess.php)
```

`Resource::form()`/`table()` just delegate to `Schemas\*Form::configure()` / `Tables\*Table::configure()`
— don't inline field/column definitions back into the Resource class.

Resources whose model uses `SoftDeletes` override `getRecordRouteBindingEloquentQuery()` to drop the
soft-delete global scope (see `ConsultaResource`), otherwise `/edit` breaks for trashed records.

### Authorization

`spatie/laravel-permission` + `bezhansalleh/filament-shield`. `AppServiceProvider::boot()` calls
`FilamentShield::enforcePolicies()`. Policies in `app/Policies/` are Shield-generated boilerplate that
delegate every ability to a permission string of the form `Action:Model` (e.g. `authUser->can('Update:Caso')`)
— don't hand-write custom authorization logic into these policies; change permissions/roles instead, and
re-run `shield:generate` if a new Resource or ability is added.

### Known gaps (don't be surprised by these; they're deliberate, see the TODOs in the code)

- `rmsramos/activitylog`'s Filament plugin is not compatible with Filament 4 yet (uses the old
  `Filament\Forms\Form` API) — it's commented out in `AdminPanelProvider`. The activity_log DB table
  and package still exist; only the Filament UI for it is missing.
- No client-facing portal/guard exists yet ("Dominio 5" in the business docs) — `Cliente` staff and
  `Cliente` end-customers currently share the same `User`/panel-access model.
- `Cliente::$fillable` uses `nombres` (plural) and `correo` (not `email`) — matches the migration
  column names exactly; don't "fix" these to singular/`email` without checking the migration.
