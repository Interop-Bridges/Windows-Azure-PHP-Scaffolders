@echo off
cd "%~dp0"

REM This script will only execute on production Windows Azure.
if "%EMULATED%"=="true" goto :EOF

ECHO Installing PHP runtime... >> ..\startup-tasks-log.txt

powershell.exe Set-ExecutionPolicy Unrestricted
powershell.exe .\install-php.ps1

ECHO Installed PHP runtime. >> ..\startup-tasks-log.txt