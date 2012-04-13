<?php
set_time_limit(600);
/*
Copyright 2011 Microsoft Corporation

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

/*
 * HOW TO USE THIS FILE
 * 
 * There are only two methods in this file you should need to update, parameters()
 * and doWork(). 
 * 
 * Follow the example in parameters() to add all the required and optional 
 * command line parameters your scaffold requires. 
 * 
 * This scaffold automagically extracts the content and updates your config file,
 * however if there are any extra steps you need to take you should do so in
 * the doWork() method. This could include work such as downloading archives
 * or configuring files.
 * 
 * **** HOW TO CHANGE THE NAME OF YOUR SCAFFOLD ****
 * There are several steps required to change the name of a scaffold.
 *  - Rename the scaffold folder to the name you desire
 *  - Change the @command-handler in this file to the name you desire
 *  - Change the class name in this file to the name you desire
 */





require_once('Params.class.php');
require_once('FileSystem.class.php');
require_once('Config.class.php');

/**
 * @command-handler Joomla
 */ 
class Joomla
	extends Microsoft_WindowsAzure_CommandLine_PackageScaffolder_PackageScaffolderAbstract {


        // this should be in parent
    protected $p;

    /**
     * Full path to Document Root
     * @var String
     */
    protected $mAppRoot;

    /**
     * Path to scaffolder file
     * @var String
     */
    protected $mScaffolder;

    protected $mRootPath;

    /**
     * This method controls all the command line parameters you need for 
     * the scaffold. Set them here to ensure their values are used in your
     * ServiceConfiguration.cscfg file, also all values can be accessed
     * via $this->p->get(param_name).
     * 
     * Adding a parameter is done with the following structure:
     * $this->p->add('cmd_param_name', required(true|false), default value, help message string);
     */
    public function parameters() {
            $this->p = new Params(); // Do not remove this line


            /*
             * Example of a command line parameter
             * 
             * $this->p->add('cmd_param_name', required(true|false), default value, help message string);
             */               
			 $this->p->add('diagnosticsConnectionString', false, 'UseDevelopmentStorage=true', 'Connections string to storage for diagnostics');
			 $this->p->add('offline', false, '0', 'Joomla Site is offline');
			 $this->p->add('offline_message', false, '0', 'This site is down for maintenance.<br /> Please check back again soon.');
			 $this->p->add('display_offline_message', false, '1', 'This site is down for maintenance.<br /> Please check back again soon.');
			 $this->p->add('sitename', false, '1', 'Joomla on Azure');
			 $this->p->add('editor', false, '1', 'tinymce');
			 $this->p->add('list_limit', false, '1', '20');
			 $this->p->add('access', false, '1', 'Access to the site');
			 $this->p->add('debug', false, '0', 'Non Debug mode.');
			 $this->p->add('debug_lang', false, '0', 'Debug Language is english (UK) by default');
			 $this->p->add('db', true, '', 'Name of database to store Joomla data in');
             $this->p->add('user', true, '', 'User account name with permissions to the Joomla database');
             $this->p->add('password', true, '', 'Password of account with permissions to the Joomla database');
             $this->p->add('host', true, '', 'URL to database host');
             $this->p->add('dbtype', false, 'sqlazure', 'Database driver to use');
			 $this->p->add('dbprefix', false, 'jos_', 'Joomla table prefix');
             $this->p->add('live_site', false, '', 'Live Site');
             $this->p->add('secret', false, uniqid(), 'Secret');
             $this->p->add('gzip', false, '0', 'Secure auth key');
			 $this->p->add('error_reporting', false, '-1', 'default');
             $this->p->add('helpurl', false, 'http://help.joomla.org/proxy/index.php?option=com_help&amp;keyref=Help{major}{minor}:{keyref}', 'Help URL');
             $this->p->add('ftp_host', false, '127.0.0.1', 'FTP Host');
             $this->p->add('ftp_port', false, '21', 'FTP Port');
             $this->p->add('ftp_user', false, '', 'FTP User');
             $this->p->add('ftp_pass', false,'', 'FTP Password');
             $this->p->add('offset', false, 'UTC', 'Offset');
             $this->p->add('mailer', false, 'mail', 'Joomla Mail');
             $this->p->add('mailfrom', false, 'info@microsoft.com', 'Mail From');
             $this->p->add('fromname', false, 'Joomla on SQLazure', 'From Name');
             $this->p->add('sendmail', false, '', 'No send mail for windows');
             $this->p->add('smtpauth', false, '', 'SMTP Auth');
             $this->p->add('smtpuser', false, '', 'SMTP User');
             $this->p->add('smtppass', false, '', 'SMTP Pass');
             $this->p->add('smtphost', false, 'localhost', 'SMTP Host');
             $this->p->add('smtpsecure', false, 'none', 'SMTP Secure');
             $this->p->add('smtpport', false, '25', 'Path of current site');
             $this->p->add('caching', false, '0', 'Caching');
             $this->p->add('cache_handler', false, 'file', 'cache handler');
             $this->p->add('cachetime', false, '15', 'Cache Time');
             $this->p->add('MetaDesc', false, '', 'Meta Description');
             $this->p->add('MetaKeys', false, '', 'Meta Keys');
			 $this->p->add('MetaTitle', false, '', 'Meta Title');
			 $this->p->add('MetaAuthor', false, '', 'Meta Author');
			 $this->p->add('sef', false, '1', 'SEF');
			 $this->p->add('sef_rewrite', false, '0', 'SEF Rewrite');
			 $this->p->add('sef_suffix', false, '0', 'SEF Suffix');
			 $this->p->add('unicodeslugs', false, '0', 'Unicode Slugs');
			 $this->p->add('feed_limit', false, '10', 'Feed Limit');
			 $this->p->add('log_path', false, '0', 'E:/approot/logs');
             $this->p->add('tmp_path', false, '0', 'E:/approot/tmp');
             $this->p->add('lifetime', false, '15', 'Lifetime');
			 $this->p->add('session_handler', false, 'database', 'SEF Suffix');
            /*$this->p->add('source', false, 'C:/abhibuild/Joomla/beta', 'If there is an existing Joomla code base you can use it via a path');*/
		     $this->p->add('source', false, '', 'If there is an existing Joomla code base you can use it via a path');
			 $this->p->add('sample_data', true, '', 'If set to 1, sample data is installed');
			 $this->p->add('admin_user', true, '', 'Default admin susername');
			 $this->p->add('admin_password', true, '', 'Default admin password');
             if(!$this->p->verify()) die($this->p->getError());

    }
		

    /**
     * This method allows you to do any additional work beyond unpacking 
     * the files that is required. This could include work such as downloading
     * and unpacking an archive.
     * 
     * The following are some of the methods available to you in this file:
     * $this->curlFile($url, $destFolder)
     * $this->move($src, $dest)
     * $this->unzip($file, $destFolder)
     */
    public function doWork() {
		$conf = new Config();
		
        $fs = new Filesystem();
        // Ensure tmp working dir exists
        $tmp = $this->mRootPath . "\\tmp";
        $this->log("Creating temporary build directory: " . $tmp);
        $fs->mkdir($tmp);

        if($this->p->get('source') != '' && $fs->exists($this->p->get('source'))) {
            // Use Joomla codebase from source parameter
            $this->log("Copying Joomla from " . $this->p->get('source'));
            $fs->copy($this->p->get('source'), $this->mAppRoot);
        } else {
            // Download and unpack Joomla
            $this->log('Downloading Joomla');
			//Packaged Joomla.zip on storage/svn or git
			$file = $this->curlFile($conf->joomla_link, $tmp);
            $this->log('Extracting Joomla');
			$fs->mkdir( $tmp.'/joomla/');
            $this->unzip($file, $tmp.'/joomla/');
			
            $this->log('Moving Joomla files to ' . $this->mAppRoot);
            $fs->move("$tmp\joomla", $this->mAppRoot);
			
			$this->log("Downloading the CDN");
			$file = $this->curlFile($conf->cdn_link, $tmp);
			$this->log('Extracting CDN');
			$fs->mkdir( $tmp.'/CDN');
			$this->unzip($file, $tmp.'/CDN');
			$this->log('Installing CDN...');
			
			$fs->copy("$tmp/CDN/azure_dependencies/standalone.php",$this->mAppRoot . "/standalone.php");
			$fs->copy("$tmp/CDN/azure_dependencies/preflight_config.php",$this->mAppRoot . "/preflight_config.php");
			$fs->copy("$tmp/CDN/azure_dependencies/index.php",$this->mAppRoot . "/index.php");
		    $fs->move("$tmp/CDN/azure_dependencies/windows_azure", $this->mAppRoot . "/windows_azure");
			$fs->move("$tmp/CDN/azure_dependencies/admin_media", $this->mAppRoot . "/administrator/components/com_media");
		    $fs->move("$tmp/CDN/azure_dependencies/site_media/controller.php", $this->mAppRoot . "/components/com_media/controller.php");
		    $fs->move("$tmp/CDN/azure_dependencies/admin_cdn", $this->mAppRoot . "/administrator/components/com_cdn");
		    $fs->copy("$tmp/CDN/azure_dependencies/factory.php",$this->mAppRoot . "/libraries/joomla/factory.php");
		    $fs->move("$tmp/CDN/azure_dependencies/plg_azure",$this->mAppRoot . "/plugins/system/plg_azure");
		    $fs->move("$tmp/CDN/libraries/microsoft", $this->mAppRoot . "/libraries/microsoft");
		    $fs->copy("$tmp/CDN/language/en-GB/en-GB.com_cdn.ini", $this->mAppRoot . "/administrator/language/en-GB/en-GB.com_cdn.ini");
		    $fs->copy("$tmp/CDN/language/en-GB/en-GB.com_cdn.sys.ini", $this->mAppRoot . "/administrator/language/en-GB/en-GB.com_cdn.sys.ini");
			
			$fs->copy($this->mAppRoot.'/installation/sql/sqlazure/joomla.sql', $this->mAppRoot.'/windows_azure/sql/joomla.sql' );
			$fs->copy($this->mAppRoot.'/installation/sql/sqlazure/sample_data.sql', $this->mAppRoot.'/windows_azure/sql/sample_data.sql' );
			$fs->move($this->mAppRoot.'/installation', $this->mAppRoot.'/installation_bak');
			
	
		}
		
		 $this->updateJoomlaConfig();
		 $this->updatePreflightConfig();
	    			
			
		//echo "\nNOTE: Do not forget to install the FileSystemDurabilityPlugin before packaging your application!";
    	echo "\n\nCongratulations! You now have a brand new Windows Azure Joomla project at " . $this->mRootPath . "\n";

    }







    /**
     * Runs a scaffolder and creates a Windows Azure project structure which can be customized before packaging.
     * 
     * @command-name Run
     * @command-description Runs the scaffolder.
     * 
     * @command-parameter-for $scaffolderFile Argv --Phar Required. The scaffolder Phar file path. This is injected automatically.
     * @command-parameter-for $rootPath Argv|ConfigFile --OutputPath|-out Required. The path to create the Windows Azure project structure. This is injected automatically. 

     *
     */
    public function runCommand($scaffolderFile, $rootPath)	{
            /**
             * DO NOT REMOVE BETWEEN BELOW COMMENT
             */
            $this->mAppRoot = ($rootPath) . "\WebRole";
            $this->mScaffolder = $scaffolderFile;
            $this->mRootPath = $rootPath;
            $this->parameters();
            
            $this->extractPhar();
            $this->updateServiceConfig();

            $this->doWork();
            /**
             * DO NOT REMOVE BETWEEN ABOVE COMMENT
             */
    }

    /**
     * Will update the ServiceConfiguration.cscfg file with any values 
     * specified from the command line paramters. Tags in the .cscfg file
     * will be found and replaced. Tags are of the form $tagName$
     */
    private function updateServiceConfig() {
        $this->log("Updating ServiceConfiguration.cscfg\n");
         $contents = file_get_contents($this->mRootPath . "/ServiceConfiguration.cscfg");
         $values = $this->p->valueArray();
        foreach ($values as $key => $value) {
                $contents = str_replace('$' . $key . '$', $value, $contents);
        }

        file_put_contents($this->mRootPath . "/ServiceConfiguration.cscfg", $contents);
    }
    
    /**
     * Will update the configuration file with any values 
     * specified from the command line paramters. Tags in the configuration file
     * will be found and replaced. Tags are of the form $tagName$
     */
    private function updateJoomlaConfig() {
        $this->log("Updating configuration.php\n");
         $contents = file_get_contents($this->mRootPath . "/WebRole/configuration.php");
         $values = $this->p->valueArray();
        foreach ($values as $key => $value) {
                $contents = str_replace('$' . $key . '$', $value, $contents);
        }

        file_put_contents($this->mRootPath . "/WebRole/configuration.php", $contents);
    }
	
	/**
     * Will update the preflight_config.php file with any values 
     * specified from the command line paramters. Tags in the php file
     * will be found and replaced. Tags are of the form $tagName$
     */
    private function updatePreflightConfig() {
        $this->log("Updating preflight_config.php\n");
         $contents = file_get_contents($this->mRootPath . "/WebRole/preflight_config.php");
         $values = $this->p->valueArray();
        foreach ($values as $key => $value) {
                $contents = str_replace('$' . $key . '$', $value, $contents);
        }

        file_put_contents($this->mRootPath . "/WebRole/preflight_config.php", $contents);
    }

    /**
     * Extracts the scaffold files and sets up the project structure
     */
    private function extractPhar() {
            // Load Phar
            $phar = new Phar($this->mScaffolder);

            // Extract to disk
            $this->log("Extracting resources...\n");
            $this->createDirectory($this->mRootPath);
            $this->extractResources($phar, $this->mRootPath);
            $this->log("Extracted resources.\n");

    }


    /**
     * Extracts the contents of a zip archive
     * 
     * @param String $file
     * @param String $destFolder 
     */
    private function unzip($file, $destFolder) {
        $zip = new ZipArchive();
        if($zip->open($file) === true) {
            $zip->extractTo("$destFolder");
            $zip->close();
        } else {
            echo "Failed to open archive";
        }
    }

    /**
     * Downloads a file from the internet
     * 
     * @param String $url
     * @param String $destFolder
     * @return String 
     */
    private function curlFile($url, $destFolder) {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "blob curler 1.2", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;

        $file = explode("/", $url);
        $file = $file[count($file)-1];
        $this->log("Writing file $destFolder/$file");
        file_put_contents("$destFolder/$file", $header['content']);
        return "$destFolder/$file";
    }
}

