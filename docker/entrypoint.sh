#!/bin/bash
set -e

service mariadb start

until mariadb-admin ping --silent; do
    sleep 1
done

mariadb -e "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('');" || true
mariadb -e "CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '';" || true
mariadb -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;" || true
mariadb -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;" || true
mariadb -e "FLUSH PRIVILEGES;"

mariadb -e "CREATE DATABASE IF NOT EXISTS reservation_salles CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

cd /var/www/html
php youssou:migrate
php youssou:seed

exec apache2-foreground
