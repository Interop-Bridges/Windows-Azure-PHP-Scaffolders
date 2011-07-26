REM Copyright © Microsoft Corporation.  All Rights Reserved.
REM This code released under the terms of the 
REM Apache License, Version 2.0 (http://opensource.org/licenses/Apache-2.0)

REM Install Microsoft Sync Framework 2.1
%windir%\System32\msiexec.exe /i "%~dp0\syncfx\Synchronization-v2.1-x64-ENU.msi" /quiet
%windir%\System32\msiexec.exe /i "%~dp0\syncfx\ProviderServices-v2.1-x64-ENU.msi" /quiet