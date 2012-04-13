@echo off
cd "%~dp0"
ECHO Starting PHP installation... >> ..\startup-tasks-log.txt

md "%~dp0appdata"
cd "%~dp0appdata"
cd "%~dp0"

reg add "hku\.default\software\microsoft\windows\currentversion\explorer\user shell folders" /v "Local AppData" /t REG_EXPAND_SZ /d "%~dp0appdata" /f
"..\resources\WebPICmdLine\webpicmdline" /Products:PHP53,SQLDriverPHP53IIS,PHPManager /AcceptEula  >> ..\startup-tasks-log.txt 2>>..\startup-tasks-error-log.txt
reg add "hku\.default\software\microsoft\windows\currentversion\explorer\user shell folders" /v "Local AppData" /t REG_EXPAND_SZ /d %%USERPROFILE%%\AppData\Local /f

ECHO Completed PHP installation. >> ..\startup-tasks-log.txt

icacls %RoleRoot%\approot /grant "Everyone":F /T
%WINDIR%\system32\inetsrv\appcmd.exe set config -section:system.webServer/fastCgi /+"[fullPath='%ProgramFiles(x86)%\PHP\v5.3\php-cgi.exe'].environmentVariables.[name='PATH',value='%PATH%;%RoleRoot%\base\x86']" /commit:apphost 
