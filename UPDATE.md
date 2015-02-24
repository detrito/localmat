UPDATE v.0.1 to v.0.2
=====================

Download last version
---------------------

> git pull origin

Update dependencies
-------------------

Install new dependencies (maatwebsite/excel and schickling/backup)

> composer update

Migrate database
----------------

This migration adds a 'main' column to the table 'lm_fields'. Stored data will not be modified.

> php artisan migrate
