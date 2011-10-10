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
call scaffolder run -out="%PWD%\build\WordPress" -s="%PWD%\build\WordPress.phar" -DB_NAME=wordpress_ben -DB_USER="satish@ltkw9g5pq3" -DB_PASSWORD="p7HmRW9Tp6FK" -DB_HOST="ltkw9g5pq3.database.windows.net" -AUTH_KEY="ASDF" --SECURE_AUTH_KEY="ASDF" --LOGGED_IN_SALT="ASDF" --NONCE_KEY="ASDF" --AUTH_SALT="ASDF" --SECURE_AUTH_SALT="ASDF" --LOGGED_IN_SALT="ASDF" --NONCE_SALT="ASDF" -sync_account="belobastor" -sync_key="x9dwLT6jaNgLvqVDhkQ24ZQtPeedoJqKobxkaOAjPc5/+jpI8IFUsaCiowyhKu69UWl70IdHsv2vCfRqiCPkiA==" 

REM -out="%PWD%\build\WordPress"

echo Packaging project
call package create -in="%PWD%\build\WordPress" -out="%PWD%\build" -dev=false
