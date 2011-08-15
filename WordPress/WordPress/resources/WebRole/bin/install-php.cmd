@echo on

REM This script will only execute on production Windows Azure. The PS script prohibits running on devfabric.

ECHO Installing PHP runtime... 

powershell.exe Set-ExecutionPolicy Unrestricted
powershell.exe .\install-php.ps1

ECHO Installed PHP runtime. 


