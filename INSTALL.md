INSTALL ON LOCALHOST
====================

Install composer
----------------

> curl -sS https://getcomposer.org/installer | php

> cp composer.phar /usr/local/bin/composer

Clone the localmat repository
-----------------------------

> git clone https://github.com/detrito/localmat

Install laravel framework and other dependencies
------------------------------------------------

> cd localmat

> composer install

> chmod -R a+w app/storage/

Configure MySQL (or another database)
-------------------------------------

> mysql -u root -h localhost -p

> CREATE DATABASE mydbname;

> GRANT ALL ON mydbname.* TO 'myusername'@'localhost' IDENTIFIED BY 'mypassword';

> edit app/config/database.php

Populate database
-----------------

> php artisan migrate

> php artisan db:seed

Enjoy
-----

> php artisan serve

> firefox localhost:8000

INSTALL ON REMOTE SERVER
========================

It is easier to first install and test LocalMat on a localhost: you can then simply upload the files and the database backup on the remote server.

Install on webserver with virtual host
======================================
Laravel works better when configured to run on a virtual host, with the document root pointed to the /public folder. [Here] (https://github.com/daylerees/laravel-website-configs) some server configuration files to get Laravel running on a number of web servers.  The [Laravel official documentation] (http://laravel.com/docs/installation) may also be a very useful resource.

Install on shared webserver
===========================
If the hosting environnement does not allow allows you to change your document root path to your `/public` folder, you can try [one of these 3 solutions] (http://forumsarchive.laravel.io/viewtopic.php?id=1258).

For an intallation on an Apache webserver with a subdomain I used solution 2 with the following `.htaccess` file placed in the upload directory (in this case `localmat`).

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews
    </IfModule>

    RewriteEngine On

    # Redirect to static content
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{DOCUMENT_ROOT}/localmat/public/%{REQUEST_URI} -f
    RewriteRule ^(.+)$ /public/$1 [L]

    # Redirect Trailing Slashes...
    RewriteRule ^(.*)/$ /$1 [L,R=301]

    # Handle Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ /public/index.php/$1 [L]

</IfModule>
```

MCRYPT
===========================
mcrypt is depcrecated since php 7.1 and doesn't work since php 7.2
http://www.php.net/manual/en/migration71.deprecated.php

Laravel 4.2 has a hardcoded check for the mcrypt extension, so that installation with php > 7.1 is complicated
http://medium.com/@tomgrohl/making-laravel-4-2-work-with-php-7-2-e9149a9428c3
http://medium.com/@tomgrohl/an-php-7-1-encrypter-for-laravel-4-2-932112405a45
