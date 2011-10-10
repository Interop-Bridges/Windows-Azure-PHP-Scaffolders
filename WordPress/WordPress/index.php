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

/**
 * @command-handler WordPress
 */ 
class WordPress
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
             $this->p->add('DB_NAME', true, '', 'Name of database to store WordPress data in');
             $this->p->add('DB_USER', true, '', 'User account name with permissions to the WordPress database');
             $this->p->add('DB_PASSWORD', true, '', 'Password of account with permissions to the WordPress database');
             $this->p->add('DB_HOST', true, '', 'URL to database host');
             $this->p->add('DB_TYPE', false, 'sqlsrv', 'Database driver to use');
             $this->p->add('DB_CHARSET', false, 'utf8', 'Database character set');
             $this->p->add('DB_COLLATE', false, '', 'Database collation');
             $this->p->add('AUTH_KEY', false, uniqid(), 'Auth key');
             $this->p->add('SECURE_AUTH_KEY', false, uniqid(), 'Secure auth key');
             $this->p->add('LOGGED_IN_KEY', false, uniqid(), 'Logged in key');
             $this->p->add('NONCE_KEY', false, uniqid(), 'Nonce in salt');
             $this->p->add('LOGGED_IN_SALT', false, uniqid(), 'Logged in salt');
             $this->p->add('NONCE_SALT', false, uniqid(), 'Nonce salt');
             $this->p->add('AUTH_SALT', false, uniqid(), 'Auth salt');
             $this->p->add('SECURE_AUTH_SALT', false, uniqid(), 'Nonce salt');
             $this->p->add('DB_TABLE_PREFIX', false, 'wp_', 'WordPress table prefix');
             $this->p->add('WPLANG', false, '', 'WordPress language');
             $this->p->add('WP_DEBUG', false, 'false', 'WordPress debugging flag');
             $this->p->add('SAVEQUERIES', false, 'false', 'Save queries');
             $this->p->add('RELOCATE', false, 'false', 'Relocate WordPress installation');
             $this->p->add('WP_ALLOW_MULTISITE', false, 'false', 'WordPress Multisite');
             $this->p->add('MULTISITE', false, 'false', 'WordPress Multisite');
             $this->p->add('SUBDOMAIN_INSTALL', false, 'false', 'Subdomain install');
             $this->p->add('base', false, '/', 'Root of WordPress installation');
             $this->p->add('DOMAIN_CURRENT_SITE', false, '', 'Domain of current site');
             $this->p->add('PATH_CURRENT_SITE', false, '/', 'Path of current site');
             $this->p->add('SITE_ID_CURRENT_SITE', false, '1', 'ID of current site');
             $this->p->add('BLOG_ID_CURRENT_SITE', false, '1', 'Blog ID of current site');
             $this->p->add('sync_account', true, '', 'Windows Azure Storage account endpoint');
             $this->p->add('sync_key', true, '', 'Windows Azure Storage account key');
             $this->p->add('sync_container', false, 'wpsync', 'Windows Azure Storage container to sync files to');
             $this->p->add('sync_folder', false, 'wp-content', 'WordPress folder to watch for changes');
             $this->p->add('sync_exclude_paths', false, '', 'Path to not sync');
             $this->p->add('sync_frequency', false, '7200', 'Sync time interval in seconds'); 
             $this->p->add('source', false, '', 'If there is an existing WordPress code base you can use it via a path');


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
        $fs = new Filesystem();
        // Ensure tmp working dir exists
        $tmp = $this->mRootPath . "\\tmp";
        $this->log("Creating temporary build directory: " . $tmp);
        $fs->mkdir($tmp);

        if($this->p->get('source') != '' && $fs->exists($this->p->get('source'))) {
            // Use WordPress codebase from source parameter
            $this->log("Copying WordPress from " . $this->p->get('source'));
            $fs->copy($this->p->get('source'), $this->mAppRoot);
        } else {
            // Download and unpack WordPress
            $this->log('Downloading WordPress');
            $file = $this->curlFile("http://wordpress.org/wordpress-3.2.1.zip", $tmp);
            $this->log('Extracting WordPress');
            $this->unzip($file, $tmp);
            $this->log('Moving WordPress files to ' . $this->mAppRoot);
            $fs->move("$tmp\wordpress", $this->mAppRoot);
        }

        // Download and unpack DB abstraction layer
        $this->log('Downloading Database Abstraction Layer');
        //$file = $this->curlFile("http://downloads.wordpress.org/plugin/wordpress-database-abstraction.1.1.0.zip", $tmp);
        $file = $this->curlFile("http://downloads.wordpress.org/plugin/wordpress-database-abstraction.1.1.1.zip", $tmp);
        $this->log('Extracting Database Abstraction Layer');
        $this->unzip($file, $tmp);
        $this->log('Moving Database Abstraction Layer files to ' . $this->mAppRoot . "\wp-content\mu-plugins");
        $fs->copy("$tmp\wordpress-database-abstraction\wp-db-abstraction\db.php", $this->mAppRoot ."\wp-content\db.php");
        $fs->move("$tmp\wordpress-database-abstraction", $this->mAppRoot ."\wp-content\mu-plugins");


        // Download and unpack Azure Storage Plugin
        $this->log('Downloading Azure Storage Plugin');
        $file = $this->curlFile("http://downloads.wordpress.org/plugin/windows-azure-storage.zip", $tmp);
        $this->log('Extracting Azure Storage Plugin');
        $this->unzip($file, $tmp);
        $this->log('Moving Azure Storage Plugin files to ' . $this->mAppRoot . "\wp-content\plugins");
        $fs->move("$tmp\windows-azure-storage", $this->mAppRoot . "\wp-content\plugins\windows-azure-storage");


        if($this->p->get('WP_ALLOW_MULTISITE') && $this->p->get('WP_ALLOW_MULTISITE') != 'false') {
            $fs->mkdir($this->mAppRoot . "\wp-content\blogs.dir");
            unlink("$this->mAppRoot.config");

            if($this->p->get('SUBDOMAIN_INSTALL')) {
                copy($this->mAppRoot . "\\resources\Web-network-subdomains.config", $this->mAppRoot . "\Web.config"); 
            } else {
                copy($this->mAppRoot . "\\resources\Web-network-subfolders.config", $this->mAppRoot . "\Web.config");
            }
        }
        // Remove tmp build folder
        $fs->rm($tmp);
        $fs->rm($this->mRootPath . "/Params.class.php");
        $fs->rm($this->mRootPath . "/FileSystem.class.php");
        
        $this->updateWpConfig();

    echo "\nNOTE: Do not forget to install the FileSystemDurabilityPlugin before packaging your application!";
    echo "\n\nCongratulations! You now have a brand new Windows Azure WordPress project at " . $this->mRootPath . "\n";

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
     * Will update the ServiceConfiguration.cscfg file with any values 
     * specified from the command line paramters. Tags in the .cscfg file
     * will be found and replaced. Tags are of the form $tagName$
     */
    private function updateWpConfig() {
        $this->log("Updating wp-config.php\n");
         $contents = file_get_contents($this->mRootPath . "/WebRole/wp-config.php");
         $values = $this->p->valueArray();
        foreach ($values as $key => $value) {
                $contents = str_replace('$' . $key . '$', $value, $contents);
        }

        file_put_contents($this->mRootPath . "/WebRole/wp-config.php", $contents);
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
