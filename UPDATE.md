UPDATE v.0.1 to v.0.2
=====================

Download last version
---------------------

> git pull origin

Update dependencies
-------------------

Install new dependencies (maatwebsite/excel and schickling/backup)

> composer update

Update database
---------------

This migration adds a 'main' column to the table 'lm_fields'. Stored data will not be modified.

> php artisan migrate

Alternatively run the following MySQL query:

> ALTER TABLE lm_fields ADD main TINYINT(1);

