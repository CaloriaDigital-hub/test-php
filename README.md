# User Administration Panel

A robust, dependency-free PHP user management system featuring CRUD operations, secure authentication, AJAX pagination, and sorting.

## Requirements
- Docker & Docker Compose (Recommended)
- OR PHP 8.2+ and MySQL 8.0+

---

## 🐳 Quick Start (Docker) - Recommended

The easiest way to run the project without worrying about PHP versions or Apache configurations is using Docker.

1. **Start the containers**
   ```bash
   docker compose up -d --build
   ```

2. **Initialize Database & Create Admin**
   Run the following commands inside the container to set up the database and test data:
   ```bash
   docker compose exec web php bin/migrate.php
   docker compose exec web php bin/seed.php
   docker compose exec web php bin/create-admin.php admin secretpassword
   ```

3. **Open the application**
   Visit `http://localhost:8080/login` in your browser.
   *(Login with Username: `admin`, Password: `secretpassword`)*

---

## 💻 Manual Deployment (Without Docker)

If you prefer to set up the project manually or deploy it to a production server like Apache/Nginx:

1. **Configure Database Connection**
   - Copy `.env.example` to `.env`.
   - Update `DB_NAME`, `DB_USER`, and `DB_PASS` in `.env` to match your local MySQL credentials.

2. **One-liner setup (requires `make`)**
   This single command runs all steps below in order:
   ```bash
   make start-server
   ```
   Then open `http://localhost:8000` in your browser.

   **Or run each step manually:**

   | Command | What it does |
   |---|---|
   | `make install` | Copies `.env.example` → `.env` if `.env` doesn't exist yet |
   | `make migrate` | Runs `bin/migrate.php` — creates DB tables from `database/schema.sql` |
   | `make seed` | Runs `bin/seed.php` — inserts 16 test users |
   | `make admin` | Runs `bin/create-admin.php admin secretpassword` — creates the admin account |
   | `make run` | Starts the PHP built-in server at `localhost:8000` |

   ```bash
   make install
   make migrate
   make seed
   make admin
   make run
   ```

4. **Web Server Setup (Apache/Nginx)**
   - Point your web server's **document root** to the `public/` directory.
   - For **Apache**, ensure `mod_rewrite` is enabled so the `.htaccess` routing works properly.
   - For **Nginx**, `.htaccess` is not supported. Add the following `location` block to your server config:
     ```nginx
     server {
         listen 80;
         root /path/to/project/public;
         index index.php;

         location / {
             try_files $uri $uri/ /index.php?$query_string;
         }

         location ~ \.php$ {
             include fastcgi_params;
             fastcgi_pass unix:/run/php/php8.2-fpm.sock;
             fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
         }
     }
     ```

---

## ✨ Features
- **MVC Architecture**: Clean separation of logic, templates, and database repositories.
- **Dependency-free**: Written in raw PHP 8.2+ without frameworks.
- **Security**: Prepared SQL statements, CSRF protection, secure sessions.
- **AJAX Interactions**: Dynamic pagination and sorting without full page reloads.
- **Robust Error Handling**: Centralized logging and fallback mechanisms.
