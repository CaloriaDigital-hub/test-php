# Determine Operating System
ifeq ($(OS),Windows_NT)
    # Windows Settings
    CP = copy
    RM = del /Q
    COMMENT = rem
else
    # Unix (Linux/macOS) Settings
    CP = cp
    RM = rm -f
    COMMENT = #
endif

.PHONY: install migrate seed admin run start-server

install:
	php -r "if(!file_exists('.env')) copy('.env.example', '.env');"

migrate:
	php bin/migrate.php

seed:
	php bin/seed.php

admin:
	php bin/create-admin.php admin secretpassword

run:
	php -S localhost:8000 -t public/

start-server: install migrate seed admin run
