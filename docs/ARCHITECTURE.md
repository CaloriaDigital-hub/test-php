# Architecture Overview

## Project Structure

```
test-php/
├── public/             # Web root — only this dir is exposed to the web server
│   ├── index.php       # Front controller: DI wiring, router dispatch
│   ├── .htaccess       # Rewrite all requests to index.php (Apache)
│   └── assets/
│       ├── css/style.css
│       └── js/users.js # AJAX pagination and sorting
│
├── src/
│   ├── Contracts/      # Interfaces only — no implementation details
│   │   ├── UserRepositoryInterface.php
│   │   └── AdminRepositoryInterface.php
│   │
│   ├── Core/           # Framework-like plumbing written from scratch
│   │   ├── Database.php        # PDO singleton (init once, get anywhere)
│   │   ├── Router.php          # Regex-based router with middleware groups
│   │   ├── Session.php         # Session helpers + flash messages
│   │   ├── Csrf.php            # CSRF token generation and validation
│   │   ├── Logger.php          # File-based PSR-like logger
│   │   ├── Validator.php       # Chainable validation rules engine
│   │   ├── EnvLoader.php       # .env parser (no external deps)
│   │   ├── helpers.php         # Global: render(), e(), sortLink(), getPaginationRange()
│   │   ├── MiddlewareInterface.php
│   │   └── Middleware/
│   │       └── AuthMiddleware.php  # Redirects to /login if not authenticated
│   │
│   ├── Models/         # Plain readonly data objects, no DB logic
│   │   ├── User.php            # Full entity — includes password_hash (write operations)
│   │   ├── UserListItem.php    # Read-only projection — no password_hash (display only)
│   │   └── AuthUser.php        # Admin session model
│   │
│   ├── Repositories/   # All SQL lives here
│   │   ├── UserRepository.php
│   │   └── AdminRepository.php
│   │
│   ├── UseCases/       # Business logic — one class per operation
│   │   ├── GetPaginatedUsers.php   # Pagination + sort whitelist + page clamping
│   │   ├── PaginatedUsersResult.php
│   │   ├── CreateUser.php
│   │   ├── UpdateUser.php
│   │   └── DeleteUser.php
│   │
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginFormController.php
│   │   │   ├── ProcessLoginController.php
│   │   │   └── LogoutController.php
│   │   ├── Users/
│   │   │   ├── ListUsersController.php
│   │   │   ├── ShowUserController.php
│   │   │   ├── CreateUserFormController.php
│   │   │   ├── StoreUserController.php
│   │   │   ├── EditUserFormController.php
│   │   │   ├── UpdateUserController.php
│   │   │   └── DeleteUserController.php
│   │   └── Api/
│   │       └── ListUsersController.php  # JSON endpoint for AJAX pagination
│   │
│   ├── Validators/
│   │   ├── CreateUserValidator.php
│   │   └── UpdateUserValidator.php
│   │
│   ├── Enums/
│   │   └── Gender.php
│   │
│   └── Exceptions/
│       ├── ValidationException.php
│       └── NotFoundException.php
│
├── templates/          # PHP templates (presentation layer)
│   ├── layout.php
│   ├── login.php
│   ├── users/
│   │   ├── list.php
│   │   ├── show.php
│   │   └── form.php    # Shared for create and edit
│   └── errors/
│       └── 404.php
│
├── database/
│   ├── schema.sql      # CREATE TABLE statements (idempotent — IF NOT EXISTS)
│   └── dump.sql        # Full dump: schema + 10 seed users + 1 admin
│
├── bin/
│   ├── migrate.php     # Runs schema.sql
│   ├── seed.php        # Inserts test users
│   └── create-admin.php  # Creates admin: php bin/create-admin.php <user> <pass>
│
├── config/
│   └── config.php      # Returns array with DB and app settings from .env
│
├── Dockerfile          # php:8.2-apache with pdo_mysql and mod_rewrite
├── docker-compose.yml  # web + db services
├── Makefile            # Shortcuts: make start-server, make migrate, etc.
├── .env.example        # Template — copy to .env and fill credentials
└── README.md           # Installation guide (Docker and manual)
```

## Request Lifecycle

```
HTTP Request
    └─▶ public/index.php
            ├─ EnvLoader → loads .env
            ├─ Database::init() → PDO connection
            ├─ DI wiring → instantiates controllers with their dependencies
            └─ Router::dispatch()
                    ├─ AuthMiddleware::handle() → redirects to /login if needed
                    └─ Controller::__invoke()
                            ├─ (validates CSRF for POST requests)
                            ├─ calls UseCase::execute()
                            │       ├─ validates input
                            │       └─ calls Repository methods
                            └─ render(template) or header(Location) or json_encode()
```

## Key Design Decisions

| Decision | Reason |
|---|---|
| Separate `User` and `UserListItem` models | `UserListItem` never exposes `password_hash` — safe to pass to any template |
| Separate `admins` table | Admins and regular users are different entities with different auth flows |
| UseCase layer between Controller and Repository | Controllers stay thin (HTTP adapter only); business rules are testable in isolation |
| `GetPaginatedUsers` UseCase shared by HTML and API controllers | DRY — pagination logic, page clamping, sort whitelist in one place |
| Sort column whitelist in both UseCase and Repository | Defence-in-depth — ORDER BY column names can't be parameterized by PDO |
| Flash messages via Session instead of query string | `?msg=deleted` persists on page reload — session flash disappears after first read |
| `wasClamped` flag in `PaginatedUsersResult` | Explicit signal from UseCase; HTML controller redirects, API controller sends `pageWasAdjusted` in JSON |
