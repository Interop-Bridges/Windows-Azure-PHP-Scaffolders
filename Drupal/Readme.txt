1) Install Windows Azure SDK for PHP. 

2) Update PATH environment variable to include PHP runtime and bin folder of 
   the Windows Azure SDK for PHP. You can either modify system environemnt variable or set it for a specific 
   command windows session using following command. Make sure to replace correct path for PHP runtime and Windows Azure
   SDK for PHP.

   SET PATH=%PATH%;C:\Program Files (x86)\PHP\v5.3\;C:\phpazure4.0.2\bin

   Also add following line to your php.ini file. You need this to create .phar file using scaffolder.bat command.
   phar.readonly = Off

   In addition, you need to install FileSystemDurabilityPlugin.zip into Windows Azure SDK 1.4. Please extract the
   https://github.com/downloads/Interop-Bridges/Windows-Azure-File-System-Durability-Plugin/FileSystemDurabilityPlugin.zip
   file into "C:\Program Files\Windows Azure SDK\v1.4\bin\plugins" folder.
   Note: You may need administrative rights to this SDK folder.

   Note: Please make sure to install Windows Azure SDK 1.4 Refresh that is available in Web Platform Installer. The version 
   number for this SDK is 1.4.20407.2049. This is needed for FileSystemDurabilityPlugin. You can check the version number in
   software listed in Windows Control Panel -> Program and Features. If it says 1.4.20227.1419, then you need to uninstall your
   Windows Azure SDK and re-install it using Web Platform Installer.

3) run build_scaffolder.bat command. This will produce drupal.phar file in current directory.

   build_scaffolder.bat
   ====================   
   scaffolder.bat build --InputPath=.\source --OutputFile=.\drupal.phar 

4) Edit run_scaffolder.bat and replace ***** with correspnding values for your SQL Azure and Windows Azure storage credentials.

5) Execute run_scaffolder.bat. It will create .\build\drupal folder and put all files needed for packaging.

   run_scaffolder.bat
   ==================
   scaffolder.bat run --Scaffolder=drupal.phar
   	--OutputPath=.\build\drupal 
	--DiagnosticsConnectionString="DefaultEndpointsProtocol=https;AccountName=*****;AccountKey=*****"  
	--sql_azure_database=***** 
	--sql_azure_username=*****@***** 
	--sql_azure_password=***** 
	--sql_azure_host=*****.database.windows.net 
	--sync_account=***** 
	--sync_key=*****

6) If needed, customize Drupal available in build\WebRole foTypycally user will include their custom modules, themes and 
   installation profiles by modifying following folders.
   - .\build\drupal\WebRole\modules
   - .\build\drupal\WebRole\themes
   - .\build\drupal\WebRole\sites\all\modules
   - .\build\drupal\WebRole\sites\all\themes
   - .\build\drupal\WebRole\profiles

   Note: Please do not delete following modules. They are essentials for running Drupal on Windows Azure.
   - .\build\drupal\WebRole\modules\azure
   - .\build\drupal\WebRole\modules\ctools
   - .\build\drupal\WebRole\includes\database\sqlsrv

7) User can also edit .\build\drupal\ServiceConfiguration.cscfg if they decide to modify settings provided while executing 
   run_scaffolder.bat command.

   If user need to change the default VM size, he can update vmsize attribute in following line in .\build\drupal\ServiceDefinition.csdef file.
   <WebRole name="WebRole" enableNativeCodeExecution="true" vmsize="Small">

   Allowed values for vmsize attribute are "ExtraSmall", "Small", "Medium", "Large" and "ExtraLarge".

8) If you need to enable RDP acecss, you need to 
   - Uncomment following lines from .\build\drupal\ServiceDefinition.csdef file
      <Import moduleName="RemoteAccess"/>
      <Import moduleName="RemoteForwarder"/>
   - Uncomment following lines from .\build\drupal\ServiceConfiguration.cscfg file
      <Setting name="Microsoft.WindowsAzure.Plugins.RemoteAccess.Enabled" value="true" />
      <Setting name="Microsoft.WindowsAzure.Plugins.RemoteForwarder.Enabled" value="true" />
      <Setting name="Microsoft.WindowsAzure.Plugins.RemoteAccess.AccountUsername" value="****" />
      <Setting name="Microsoft.WindowsAzure.Plugins.RemoteAccess.AccountEncryptedPassword" value="****" />
      <Setting name="Microsoft.WindowsAzure.Plugins.RemoteAccess.AccountExpiration" value="2039-12-31T23:59:59.0000000-08:00" />
   - Refer MSDN documentation for settings above values.

9) Once you have finalized your build folder, execute package_scaffolder.bat command. It will create follwing two files inside package folder
   .\package\drupal.cspkg and .\package\ServiceConfiguration.cscfg. Please make sure that you have installed FileSystemDurabilityPlugin.
   
   package_scaffolder.bat
   ======================
   package.bat create --InputPath=.\build\drupal --RunDevFabric=false --OutputPath=.\package

10) If needed you can edit the .\package\ServiceConfiguration.cscfg 

11) Finally deploy .\package\drupal.cspkg and .\package\ServiceConfiguration.cscfg file to Windows Azure.

12) Once drupal deployment is ready, visit the install.php page of your drupal site and confiure your drupal.
    i.e. visit http://yourapp.cloudapp.net/install.php

13) Use of file synchronization is not needed for basic Drupal functionality. But if dynamic installation of modules/theme on running site without repackage/upgrade 
   is dealbreaker for Drupal sites to move to Windows Azure, file synchronization can be used to support this feature.

14) Once your drupal is configured as per your requirement, you can increase instance count using Windows Azure portal. 
