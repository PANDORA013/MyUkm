@echo off
echo === Checking Database Structure ===
echo.

REM Check MySQL version
mysql -V
echo.

REM List all tables
echo === Database Tables ===
mysql -u root -e "USE myukm_test; SHOW TABLES;"
echo.

REM Check groups table structure
echo === Groups Table Structure ===
mysql -u root -e "USE myukm_test; DESCRIBE `groups`;"
echo.

REM Show CREATE TABLE statement
echo === CREATE TABLE Statement ===
mysql -u root -e "USE myukm_test; SHOW CREATE TABLE `groups`;"
echo.

echo === Check Complete ===
