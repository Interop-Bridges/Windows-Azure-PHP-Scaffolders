<?php
/**
 * Copyright (c) 2009 - 2011, RealDolmen
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of RealDolmen nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY RealDolmen ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL RealDolmen BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * @version    $Id: SharedKeyCredentials.php 14561 2009-05-07 08:05:12Z unknown $
 */


/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure_CommandLine
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * 
 * @command-handler MemcachedScaffolder
 * 
 * @command-handler-description Windows Azure SDK for PHP default scaffolder.
 * @command-handler-header Windows Azure SDK for PHP
 * @command-handler-header Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @command-handler-footer 
 * @command-handler-footer The MemcachedScaffolder automatically installs PHP
 * @command-handler-footer to the Windows Azure virtual machine. If a customized
 * @command-handler-footer php.ini is required, add it in the /php folder after
 * @command-handler-footer running the scaffolder.
 * @command-handler-footer An added bonus is the availability of a preconfigured
 * @command-handler-footer Memcached instance and client available by
 * @command-handler-footer including 'memcache.inc.php' and using the $memcache
 * @command-handler-footer variable.
 */ 
class MemcachedScaffolder
	extends Microsoft_WindowsAzure_CommandLine_PackageScaffolder_PackageScaffolderAbstract
{
	/**
	 * Runs a scaffolder and creates a Windows Azure project structure which can be customized before packaging.
	 * 
	 * @command-name Run
	 * @command-description Runs the scaffolder.
	 * 
	 * @command-parameter-for $scaffolderFile Argv --Phar Required. The scaffolder Phar file path. This is injected automatically.
	 * @command-parameter-for $rootPath Argv|ConfigFile --OutputPath|-out Required. The path to create the Windows Azure project structure. This is injected automatically. 
	 * @command-parameter-for $diagnosticsConnectionString Argv|ConfigFile|Env --DiagnosticsConnectionString|-d Optional. The diagnostics connection string. This defaults to development storage.
	 * @command-parameter-for $webRoleNames Argv|ConfigFile|Env --WebRoles|-web Optional. A comma-separated list of names for web roles to create when scaffolding. Set this value to an empty parameter to skip creating a web role.
	 * @command-parameter-for $workerRoleNames Argv|ConfigFile|Env --WorkerRoles|-workers Optional. A comma-separated list of names for worker roles to create when scaffolding.
	 */
	public function runCommand($scaffolderFile, $rootPath, $webRoleNames = 'PhpOnAzure.Web', $workerRoleNames = '', $diagnosticsConnectionString = 'UseDevelopmentStorage=true')
	{
		// Load Phar
		$phar = new Phar($scaffolderFile);
		
		// Extract to disk
		$this->log('Extracting resources...');
		$this->createDirectory($rootPath);
		$this->extractResources($phar, $rootPath);
		$this->log('Extracted resources.');
		
		// Configuration files
		$serviceDefinitionIncludes = array();
		$serviceConfigurationIncludes = array();
		
		// Follow instructions listed in $webRoleNames and $workerRoleNames
		$httpPort = 80;
		$webRoles = explode(',', $webRoleNames);
		foreach ($webRoles as $webRole) {
			if ($webRole == '') continue;
			
			$this->log('Creating web role "' . $webRole . '"...');
			
			// Copy all files
			$this->createDirectory($rootPath . '/' . $webRole);
			$this->copyDirectory($rootPath . '/Common', $rootPath . '/' . $webRole, false);
			$this->copyDirectory($rootPath . '/CommonWeb', $rootPath . '/' . $webRole, false);
			
			// Configure role
			$this->log('  Configuring web role "' . $webRole . '"...');
			
			$serviceDefinitionFile = $rootPath . '/' . $webRole . '/ServiceDefinition.' . $webRole . '.csdef';
			$serviceConfigurationFile = $rootPath . '/' . $webRole . '/ServiceConfiguration.' . $webRole . '.cscfg';
			copy($rootPath . '/ServiceDefinition.Web.csdef', $serviceDefinitionFile);
			copy($rootPath . '/ServiceConfiguration.Web.cscfg', $serviceConfigurationFile);

			$this->applyTransforms($rootPath . '/' . $webRole, array(
				'DiagnosticsConnectionString' => $diagnosticsConnectionString,
				'RoleName' => $webRole,
				'HttpPort' => $httpPort++
			));		
			$serviceDefinitionIncludes[$webRole] = file_get_contents($serviceDefinitionFile);
			$serviceConfigurationIncludes[$webRole] = file_get_contents($serviceConfigurationFile);
			@unlink($serviceDefinitionFile);
			@unlink($serviceConfigurationFile);
			
			$this->log('  Configured web role "' . $webRole . '"...');

			$this->log('Created web role "' . $webRole . '"...');
		}
		
		$workerRoles = explode(',', $workerRoleNames);
		foreach ($workerRoles as $workerRole) {
			if ($workerRole == '') continue;
			
			$this->log('Creating worker role "' . $workerRole . '"...');
			
			// Copy all files
			$this->createDirectory($rootPath . '/' . $workerRole);
			$this->copyDirectory($rootPath . '/Common', $rootPath . '/' . $workerRole, false);
			$this->copyDirectory($rootPath . '/CommonWorker', $rootPath . '/' . $workerRole, false);
			
			// Configure role
			$this->log('  Configuring worker role "' . $workerRole . '"...');
			
			$serviceDefinitionFile = $rootPath . '/' . $workerRole . '/ServiceDefinition.' . $workerRole . '.csdef';
			$serviceConfigurationFile = $rootPath . '/' . $workerRole . '/ServiceConfiguration.' . $workerRole . '.cscfg';
			copy($rootPath . '/ServiceDefinition.Worker.csdef', $serviceDefinitionFile);
			copy($rootPath . '/ServiceConfiguration.Worker.cscfg', $serviceConfigurationFile);

			$this->applyTransforms($rootPath . '/' . $workerRole, array(
				'DiagnosticsConnectionString' => $diagnosticsConnectionString,
				'RoleName' => $workerRole
			));		
			$serviceDefinitionIncludes[$workerRole] = file_get_contents($serviceDefinitionFile);
			$serviceConfigurationIncludes[$workerRole] = file_get_contents($serviceConfigurationFile);
			@unlink($serviceDefinitionFile);
			@unlink($serviceConfigurationFile);
			
			$this->log('  Configured worker role "' . $workerRole . '"...');			
			
			$this->log('Created worker role "' . $workerRole . '"...');
		}

		// Apply transforms
		$this->log('Applying transforms...');
		$this->applyTransforms($rootPath, array(
			'DiagnosticsConnectionString' => $diagnosticsConnectionString,
			'ServiceDefinition' => implode("\r\n", $serviceDefinitionIncludes),
			'ServiceConfiguration' => implode("\r\n", $serviceConfigurationIncludes)
		));
		$this->log('Applied transforms.');
		
		// Delete unnecessary files and folders
		$this->log('Cleanup starting...');
		$this->deleteDirectory($rootPath . '/Common');
		$this->deleteDirectory($rootPath . '/CommonWeb');
		$this->deleteDirectory($rootPath . '/CommonWorker');
		@unlink($rootPath . '/ServiceConfiguration.Web.cscfg');
		@unlink($rootPath . '/ServiceConfiguration.Worker.cscfg');
		@unlink($rootPath . '/ServiceDefinition.Web.csdef');
		@unlink($rootPath . '/ServiceDefinition.Worker.csdef');
		$this->log('Cleanup finished.');
		
		// Show "to do" message
		$this->log('');
		$this->log('Your Windows Azure project has been scaffolded.');
		$this->log('In order to use Memcached in your application, do the following:');
		$this->log(' - Add the memcache.inc.php as an include');
		$this->log(' - The $memcache global variable will have a ready-to-use Memcache');
		$this->log('   client for you to use. E.g. use $memcache->getVersion() in your code.');
		$this->log('');
	}
}
