# create test database

CREATE DATABASE IF NOT EXISTS `database_test`;
GRANT ALL PRIVILEGES ON database_test.* TO 'username'@'%';
FLUSH PRIVILEGES;