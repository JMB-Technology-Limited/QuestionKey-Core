#!/usr/bin/env bash

debconf-set-selections <<< 'mysql-server mysql-server/root_password password password'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password password'
sudo apt-get update
sudo apt-get install -y apache2 php mysql-server php-mysql phpunit php-curl zip unzip libapache2-mod-php php-mbstring

mysql -u root --password=password -e "CREATE DATABASE app"
mysql -u root --password=password -e "CREATE DATABASE testing"

echo "xdebug.max_nesting_level=1000" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini

mkdir /home/ubuntu/bin
cd /home/ubuntu/bin
wget https://getcomposer.org/composer.phar

cd /vagrant
php /home/ubuntu/bin/composer.phar  install

cp /vagrant/vagrant/normal/parameters_test.yml /vagrant/app/config/parameters_test.yml
cp /vagrant/vagrant/parameters.yml /vagrant/app/config/parameters.yml
cp /vagrant/vagrant/apache.conf /etc/apache2/sites-enabled/000-default.conf
cp /vagrant/vagrant/app_dev.php /vagrant/web/app_dev.php

mkdir /vagrant/app/cache/dev/
mkdir /vagrant/app/cache/prod/

touch /vagrant/app/logs/prod.log
touch /vagrant/app/logs/dev.log
chown -R www-data:www-data /vagrant/app/logs/prod.log
chown -R www-data:www-data /vagrant/app/logs/dev.log

rm -r /vagrant/app/cache/prod/*
rm -r /vagrant/app/cache/dev/*

a2enmod rewrite
/etc/init.d/apache2 restart

php app/console doctrine:migrations:migrate --no-interaction

chown -R www-data:www-data /vagrant/app/cache/prod/
chown -R www-data:www-data /vagrant/app/cache/dev/

cp /vagrant/vagrant/normal/test /home/ubuntu/test
chmod a+x /home/ubuntu/test
