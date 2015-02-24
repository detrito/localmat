localmat
========

LocalMat is a MVC (Model View Controller) web application created to manage the
equipement of caving organisations.

![Screenshot](/public/img/screenshot_articles.png)

First, enter the Categories (e.g. helmet, rope, ...) and the Fields (e.g. brand,
cord length, serial number, year of introduction, ...) that you wish. You can
then start to add some Articles to those Categories. These Articles can be
browsed and listed by their status (borrowed or available) and by their
FieldData. Users can borrow and return the Articles, and their History can also
be visualised. The database can be exported in sql or viewed in excel format.

A demonstration-website is available at http://localmat-demo.speleo-lausanne.ch

LocalMat is written in PHP and is based on the [Laravel](http://laravel.com)
framework. LocalMat requires PHP >= 5.3.7 and MySQL. More details in the
[install documentation](INSTALL.md). The [update documentation](UPDATE.md)
explains how to update to v.0.2.

The source code is available under the
[GPLv3](http://www.gnu.org/licenses/quick-guide-gplv3.html) license. Patches and
bug reports can be sent over GitHub or by e-mail at detrito (at} inventati (dot}
org
