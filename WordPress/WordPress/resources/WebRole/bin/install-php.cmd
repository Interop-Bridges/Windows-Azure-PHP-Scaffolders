@echo off

REM This script will only execute on production Windows Azure. The PS script prohibits running on devfabric.

ECHO Installing PHP runtime... >> ..\startup-tasks-log.txt

powershell.exe Set-ExecutionPolicy Unrestricted
powershell.exe .\install-php.ps1

ECHO Installed PHP runtime. >> ..\startup-tasks-log.txt


icacls %RoleRoot%\approot /grant "Everyone":F /T
%WINDIR%\system32\inetsrv\appcmd.exe set config -section:system.webServer/fastCgi /+"[fullPath='%ProgramFiles(x86)%\PHP\v5.3\php-cgi.exe'].environmentVariables.[name='PATH',value='%PATH%;%RoleRoot%\base\x86']" /commit:apphost 
