INSTALL ON LOCALHOST
====================

Clone the repository
--------------------

> git clone https://github.com/detrito/localmat

Install composer
----------------

> curl -sS https://getcomposer.org/installer | php

> cp composer.phar /usr/local/bin/composer

Install laravel framework
-------------------------

> cd localmat

> composer install

> chmod -R a+w app/storage/

Configure MySQL (or another database)
-------------------------------------

> mysql -u root -h localhost -p

> CREATE DATABASE mydbnbame;

> GRANT ALL ON mydbname.* TO 'myusername'@'localhost' IDENTIFIED BY 'mypassword';

> edit app/config/database.php

> php artisan migrate

Enjoy
-----

> php artisan serve

> firefox localhost:8000
