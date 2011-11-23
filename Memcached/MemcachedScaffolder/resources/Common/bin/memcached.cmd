@echo off
cd "%~dp0"

icacls %RoleRoot%\approot /grant "Everyone":F /T

powershell.exe Set-ExecutionPolicy Unrestricted
powershell.exe .\memcached.ps1
