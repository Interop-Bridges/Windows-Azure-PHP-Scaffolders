@echo on

REM This script will only execute on production Windows Azure. The PS script prohibits running on devfabric.

ECHO Installing PHP runtime... 

powershell.exe Set-ExecutionPolicy Unrestricted
powershell.exe .\install-php.ps1

ECHO Installed PHP runtime. 

%WINDIR%\system32\inetsrv\appcmd.exe set config -section:system.webServer/fastCgi /+"[fullPath='%ProgramFiles(x86)%\PHP\v5.3\php-cgi.exe'].environmentVariables.[name='PATH',value='%PATH%;%RoleRoot%\base\x86']" /commit:apphost 

REM Make folder writable for Drupal installation
CALL icacls ..\sites /grant "NETWORK SERVICE":F /T

REM Graceful exit
exit /b 0
