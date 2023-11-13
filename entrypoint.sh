#!/bin/bash

# Start MySQL
service mysql start

# Wait a bit for MySQL to start (optional, to ensure MySQL is ready)
sleep 10

# Run MySQL setup commands
mysql -e "CREATE DATABASE IF NOT EXISTS website;"
mysql -e "CREATE USER 'website_user'@'localhost' IDENTIFIED BY 'test';"
mysql -e "GRANT ALL PRIVILEGES ON website.* TO 'website_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Start Apache in the foreground
exec /usr/sbin/apache2ctl -D FOREGROUND
