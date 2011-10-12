echo off

set PWD=%CD%

REM Folder containing the scaffold
set WPVer=3.2.1



echo Cleaning up previous WordPress scaffolder files
 rmdir /S /Q %PWD%\build
 mkdir %PWD%\build

echo Building scaffold .phar file 
 call scaffolder build -in="%PWD%\WordPress" -out="%PWD%\build\WordPress.phar"

echo Creating project directories
call scaffolder run -out="%PWD%\build\WordPress" -s="%PWD%\build\WordPress.phar" -DB_NAME database_name -DB_USER "user@lhost" -DB_PASSWORD "*******" -DB_HOST "******.database.windows.net"  -sync_account "account enpoint" -sync_key "account key" 

REM -out="%PWD%\build\WordPress"

echo Packaging project
call package create -in="%PWD%\build\WordPress" -out="%PWD%\build" -dev=false
