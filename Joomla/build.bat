echo off

set PWD=%CD%

REM Folder containing the scaffold
set JVer=1.7.3



echo Cleaning up previous Joomla scaffolder files
 rmdir /S /Q %PWD%\build
 mkdir %PWD%\build

echo Building scaffold .phar file 
 call scaffolder build -in="%PWD%\Joomla" -out="%PWD%\build\Joomla.phar"

echo Creating project directories
call scaffolder run -out="%PWD%\build\Joomla" -s="%PWD%\build\Joomla.phar" -db jbase -user "demosql@xXXXXXX" -password "SamHoustonXXX" -host "XXXX.database.windows.net"  


REM -out="%PWD%\build\Joomla"

echo Packaging project
package.bat create --InputPath=.\build\Joomla --RunDevFabric=false --OutputPath=.\package
