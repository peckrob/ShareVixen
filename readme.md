# ShareVixen

ShareVixen is a web-based graphical frontend to a Microsoft Azure blob storage
container. It allows a group of users to use Azure blob storage as a virtual
fileshare.

This is a work in progress.

## Installation

### Prerequisites

ShareVixen is built on Laravel, a PHP framework. Therefore, you will need to
have PHP 7 installed. It *may* work on 5.6, but this is not tested. You will
also need the `mcrypt` and `json` packages installed, as well as packages for
what database you use. ShareVixen is written against MySQL, but should work
without any modification on any database Laravel supports.

You will also need `composer`, `npm` and `gulp` installed.

### Installation

```
git clone https://github.com/peckrob/ShareVixen.git /path/to/your/checkout
cd /path/to/your/checkout
cp .env.example .env
```

Edit the `.env` file, paying particular attention to the AZURE settings.

```
composer install
php artisan key:generate
php artisan migrate
npm install
gulp
```

Run `php artisan db:seed` to create your admin user.

### Configure Apache

Configure an apache vhost to point at the public directory of the checkout. As
an example:

```
<VirtualHost *:80>
    DocumentRoot /var/www/example.com/sharevixen/htdocs
    ServerName sharevixen.example.com
    CustomLog /var/log/apache2/vhosts/example.com/access.log combined
    ErrorLog /var/log/apache2/vhosts/example.com/error.log
    LogLevel warn

    <Directory /var/www/example.com/sharevixen/htdocs>
        AllowOverride all
    </Directory>
</VirtualHost>
```

In this case, `htdocs` is a symlink to `/path/to/your/checkout/public`.

### Install Logrotate Job

```
cd /etc/logrotate.d/
```

Create a `sharefixen` file.

```
/path/to/your/checkout/storage/logs/*.log {
    daily
    missingok
    rotate 7
    dateext
    notifempty
    create 775 username www-data
}
```

## Update Instructions

Generally, the procedure is:

1. App goes into maintenance mode.
1. Pull most recent code.
1. Update composer and npm.
1. Perform any migrations.
1. Run gulp for JS and CSS changes.
1. Bring the app back up.

```
cd /path/to/your/checkout
php artisan down
git pull
composer install
npm install
php artisan migrate
gulp
php artisan up
```
