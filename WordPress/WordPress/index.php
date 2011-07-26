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

require_once('Params.class.php');
/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure_CommandLine
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * 
 * @command-handler WordPress
 * 
 * @command-handler-description Windows Azure SDK for PHP default scaffolder.
 * @command-handler-header Windows Azure SDK for PHP
 * @command-handler-header Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @command-handler-footer 
 * @command-handler-footer The DefaultScaffolder automatically installs PHP
 * @command-handler-footer to the Windows Azure virtual machine. If a customized
 * @command-handler-footer php.ini is required, add it in the /php folder after
 * @command-handler-footer running the scaffolder.
 */ 
class WordPress
	extends Microsoft_WindowsAzure_CommandLine_PackageScaffolder_PackageScaffolderAbstract
{
    
    
            // this should be in parent
        protected $p;
        
        // this could be
        public function parameters($options) {
                $this->p = new Params(); // this should be in the parent
                $this->p->add('diagnosticsConnectionString', false, 'UseDevelopmentStorage=true', 'Connections string to storage for diagnostics');
                $this->p->add('DB_NAME', true, '', 'Name of database to store WordPress data in');
                 $this->p->add('DB_USER', true, '', 'User account name with permissions to the WordPress database');
                 $this->p->add('DB_PASSWORD', true, '', 'Password of account with permissions to the WordPress database');
                 $this->p->add('DB_HOST', true, '', 'URL to database host');
                 $this->p->add('DB_TYPE', false, 'sqlsrv', 'Database driver to use');
                 $this->p->add('DB_CHARSET', false, 'utf8', 'Database character set');
                 $this->p->add('DB_COLLATE', false, '', 'Database collation');
                 $this->p->add('AUTH_KEY', false, 'kladjfkladjf', 'Auth key');
                 $this->p->add('SECURE_AUTH_KEY', false, 'lkadjflafj', 'Secure auth key');
                 $this->p->add('LOGGED_IN_SALT', false, 'lakjdfladkfj', 'Logged in salt');
                 $this->p->add('NONCE_SALT', false, 'lkadfjlkkjadlfk', 'Nonce salt');
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
                 $this->p->add('sync_mode', false, '60', 'Sync time interval in seconds');
                 $this->p->add('sync_queue', false, '', 'queue');
                 
                 $this->p->verify($options); // this should be in the parent
        }
    
    
    
    
    
    
    
    
    
    
	/**
	 * Runs a scaffolder and creates a Windows Azure project structure which can be customized before packaging.
	 * 
	 * @command-name Run
	 * @command-description Runs the scaffolder.
	 * 
	 * @command-parameter-for $scaffolderFile Argv --Phar Required. The scaffolder Phar file path. This is injected automatically.
	 * @command-parameter-for $rootPath Argv|ConfigFile --OutputPath|-out Required. The path to create the Windows Azure project structure. This is injected automatically. 
	 * @command-parameter-for $diagnosticsConnectionString Argv|ConfigFile|Env --DiagnosticsConnectionString|-d Optional. The diagnostics connection string. This defaults to development storage.
         * @command-parameter-for $DB_NAME Argv|ConfigFile|Env --DB_NAME|-DB_NAME Required. Database name
	 * @command-parameter-for $DB_USER Argv|ConfigFile|Env --DB_USER|-DB_USER Required. Database user
	 * @command-parameter-for $DB_PASSWORD Argv|ConfigFile|Env --DB_PASSWORD|-DB_PASSWORD Required. Database password
	 * @command-parameter-for $DB_HOST Argv|ConfigFile|Env --DB_HOST|-DB_HOST Required. Database host
	 * @command-parameter-for $DB_TYPE Argv|ConfigFile|Env --DB_TYPE|-DB_TYPE Optional. Database type. 'sqlsrv' for SQL Azure
	 * @command-parameter-for $DB_CHARSET Argv|ConfigFile|Env --DB_CHARSET|-DB_CHARSET Optional. Database character set
	 * @command-parameter-for $DB_COLLATE Argv|ConfigFile|Env --DB_COLLATE|-DB_COLLATE Optional. Database collation
	 * @command-parameter-for $AUTH_KEY Argv|ConfigFile|Env --AUTH_KEY|-AUTH_KEY Required. Auth key
	 * @command-parameter-for $SECURE_AUTH_KEY Argv|ConfigFile|Env --SECURE_AUTH_KEY|-SECURE_AUTH_KEY Required. Secure auth key
	 * @command-parameter-for $LOGGED_IN_SALT Argv|ConfigFile|Env --LOGGED_IN_SALT|-LOGGED_IN_SALT Required. Logged in salt
	 * @command-parameter-for $NONCE_SALT Argv|ConfigFile|Env --NONCE_SALT|-NONCE_SALT Required. Nonce salt
	 * @command-parameter-for $DB_TABLE_PREFIX Argv|ConfigFile|Env --DB_TABLE_PREFIX|-DB_TABLE_PREFIX Optional. Database table prefix
	 * @command-parameter-for $WPLANG Argv|ConfigFile|Env --WPLANG|-WPLANG Optional. International language
	 * @command-parameter-for $WP_DEBUG Argv|ConfigFile|Env --WP_DEBUG|-WP_DEBUG Optional. WordPress debugging
	 * @command-parameter-for $SAVEQUERIES Argv|ConfigFile|Env --SAVEQUERIES|-SAVEQUERIES Optional. Save all database queries
	 * @command-parameter-for $RELOCATE Argv|ConfigFile|Env --RELOCATE|-RELOCATE Optional. Relocate
	 * @command-parameter-for $WP_ALLOW_MULTISITE Argv|ConfigFile|Env --WP_ALLOW_MULTISITE|-WP_ALLOW_MULTISITE Optional. Allow multisite
	 * @command-parameter-for $MULTISITE Argv|ConfigFile|Env --MULTISITE|-MULTISITE Optional. Multisite switch
	 * @command-parameter-for $SUBDOMAIN_INSTALL Argv|ConfigFile|Env --SUBDOMAIN_INSTALL|-SUBDOMAINS_INSTALL Optional. Subdomain install
	 * @command-parameter-for $base Argv|ConfigFile|Env --base|-base Optional. Base path to WordPress
	 * @command-parameter-for $DOMAIN_CURRENT_SITE Argv|ConfigFile|Env --DOMAIN_CURRENT_SITE|-DOMAIN_CURRENT_SITE Optional. Domain of current site
	 * @command-parameter-for $PATH_CURRENT_SITE Argv|ConfigFile|Env --PATH_CURRENT_SITE|-PATH_CURRENT_SITE Optional. Path to current site
	 * @command-parameter-for $SITE_ID_CURRENT_SITE Argv|ConfigFile|Env --SITE_ID_CURRENT_SITE|-SITE_ID_CURRENT_SITE Optional. Current site ID
	 * @command-parameter-for $BLOG_ID_CURRENT_SITE Argv|ConfigFile|Env --BLOG_ID_CURRENT_SITE|-BLOG_ID_CURRENT_SITE Optional. Current blog ID
	 * @command-parameter-for $sync_account Argv|ConfigFile|Env --sync_account|-sync_account Required. File sync Windows Azure Storage account endpoint
	 * @command-parameter-for $sync_key Argv|ConfigFile|Env --sync_key|-sync_key Required. File sync Windows Azure connection key
	 * @command-parameter-for $sync_container Argv|ConfigFile|Env --sync_container|-sync_container Optional. File sync Windows Azure storage container
	 * @command-parameter-for $sync_folder Argv|ConfigFile|Env --sync_folder|-sync_folder Optional. File sync folder to sync
	 * @command-parameter-for $sync_exclude_paths Argv|ConfigFile|Env --sync_exclude_paths|-sync_exclude_paths Optional. File sync folders to exclude from sync
	 * @command-parameter-for $sync_mode Argv|ConfigFile|Env --sync_mode|-sync_mode Optional. File sync mode
	 * @command-parameter-for $sync_queue Argv|ConfigFile|Env --sync_queue|-sync_queue Optional. File sync queue
	 *
	 */
	public function runCommand($scaffolderFile, $rootPath, $diagnosticsConnectionString = 'UseDevelopmentStorage=true', $DB_NAME, $DB_USER, $DB_PASSWORD, $DB_HOST, $DB_TYPE = 'sqlsrv', 
                                   $DB_CHARSET = 'utf8', $DB_COLLATE = '', $AUTH_KEY, $SECURE_AUTH_KEY, $LOGGED_IN_SALT, $NONCE_SALT, $DB_TABLE_PREFIX = 'wp_', $WPLANG = '', $WP_DEBUG = 'false', 
                                   $SAVEQUERIES = 'false', $RELOCATE = 'false', $WP_ALLOW_MULTISITE = "false", $MULTISITE = 'false', $SUBDOMAIN_INSTALL = 'false',
                                   $base = '/', $DOMAIN_CURRENT_SITE = '', $PATH_CURRENT_SITE = '/', $SITE_ID_CURRENT_SITE = '1', $BLOG_ID_CURRENT_SITE = '1',
                                   $sync_account, $sync_key, $sync_container = 'wpsync', $sync_folder = 'wp-content', $sync_exclude_paths = '', $sync_mode = '60', $sync_queue = '')	{


                // This array of course should come from $options as was originally passed. All params were passed as an array previously and this was not necessary to be built
                $this->parameters(array(
			'diagnosticsConnectionString' => $diagnosticsConnectionString,
                        'DB_NAME' => $DB_NAME,
                        'DB_USER' => $DB_USER,
                        'DB_PASSWORD' => $DB_PASSWORD,
                        'DB_HOST' => $DB_HOST,
                        'DB_TYPE' => $DB_TYPE,
                        'DB_CHARSET' => $DB_CHARSET,
                        'DB_COLLATE' => $DB_COLLATE,
                        'AUTH_KEY' => $AUTH_KEY,
                        'SECURE_AUTH_KEY' => $SECURE_AUTH_KEY,
                        'LOGGED_IN_SALT' => $LOGGED_IN_SALT,
                        'NONCE_SALT' => $NONCE_SALT,
                        'DB_TABLE_PREFIX' => $DB_TABLE_PREFIX,
                        'WPLANG' => $WPLANG,
                        'WP_DEBUG' => $WP_DEBUG,
                        'SAVEQUERIES' => $SAVEQUERIES,
                        'RELOCATE' => $RELOCATE,
                        'WP_ALLOW_MULTISITE' => $WP_ALLOW_MULTISITE,
                        'MULTISITE' => $MULTISITE,
                        'SUBDOMAIN_INSTALL' => $SUBDOMAIN_INSTALL,
                        'base' => $base,
                        'DOMAIN_CURRENT_SITE' => $DOMAIN_CURRENT_SITE,
                        'PATH_CURRENT_SITE' => $PATH_CURRENT_SITE,
                        'SITE_ID_CURRENT_SITE' => $SITE_ID_CURRENT_SITE,
                        'BLOG_ID_CURRENT_SITE' => $BLOG_ID_CURRENT_SITE,
                        'sync_account' => $sync_account,
                        'sync_key' => $sync_key,
                        'sync_container' => $sync_container,
                        'sync_folder' => $sync_folder,
                        'sync_exclude_paths' => $sync_exclude_paths,
                        'sync_mode' => $sync_mode,
                        'sync_queue' => $sync_queue
		));
		// Load Phar
		$phar = new Phar($scaffolderFile);
		
		// Extract to disk
		$this->log('Extracting resources...');
		$this->createDirectory($rootPath);
		$this->extractResources($phar, $rootPath);
		$this->log('Extracted resources.');
                
		// Apply transforms
		$this->log('Applying transforms...');
		$this->applyTransforms($rootPath, $this->p->valueArray() );
		$this->log('Applied transforms.');
		
                // Ensure tmp working dir exists
                $tmp = realpath($rootPath) . "\\tmp";
                $this->log("Creating temporary build directory: " . $tmp);
                mkdir($tmp);
                
                $approot = realpath($rootPath) . "\WebRole";
                
                // Download and unpack WordPress
                $this->log('Downloading WordPress');
                $file = $this->curlFile("http://wordpress.org/wordpress-3.2.1.zip", $tmp);
                $this->log('Extracting WordPress');
                $this->unzip($file, $tmp);
                $this->log('Moving WordPress files to ' . $approot);
                $this->move("$tmp\wordpress", $approot);
                
                // Download and unpack DB abstraction layer
                $this->log('Downloading Database Abstraction Layer');
                $file = $this->curlFile("http://downloads.wordpress.org/plugin/wordpress-database-abstraction.1.0.1.zip", $tmp);
                $this->log('Extracting Database Abstraction Layer');
                $this->unzip($file, $tmp);
                $this->log('Moving Database Abstraction Layer files to ' . $approot . "\wp-content\mu-plugins");
                copy("$tmp\wordpress-database-abstraction\wp-db-abstraction\db.php", $approot ."\wp-content\db.php");
                $this->move("$tmp\wordpress-database-abstraction", $approot ."\wp-content\mu-plugins");
                
                
                // Download and unpack Azure Storage Plugin
                $this->log('Downloading Azure Storage Plugin');
                $file = $this->curlFile("http://downloads.wordpress.org/plugin/windows-azure-storage.zip", $tmp);
                $this->log('Extracting Azure Storage Plugin');
                $this->unzip($file, $tmp);
                $this->log('Moving Azure Storage Plugin files to ' . $approot . "\wp-content\plugins");
                $this->move("$tmp\windows-azure-storage", $approot . "\wp-content\plugins\windows-azure-storage");
                
                // Remove tmp build folder
                @unlink($tmp);
                
		echo "\nNOTE: Do not forget to install the FileSystemDurabilityPlugin before packaging your application!";
		echo "\n\nCongratulations! You now have a brand new Windows Azure WordPress project at " . realpath($rootPath) . "\n";
	}
        
        private function move($src, $dest){
    
            // If source is not a directory stop processing
            if(!is_dir($src)) return false;

            // If the destination directory does not exist create it
            if(!is_dir($dest)) { 
                if(!mkdir($dest)) {
                    // If the destination directory could not be created stop processing
                    return false;
                }    
            }

            // Open the source directory to read in files
            $i = new DirectoryIterator($src);
            foreach($i as $f) {
                if($f->isFile()) {
                    rename($f->getRealPath(), "$dest/" . $f->getFilename());
                } else if(!$f->isDot() && $f->isDir()) {
                    $this->move($f->getRealPath(), "$dest/$f");
                    @unlink($f->getRealPath());
                }
            }
            @unlink($src);
        }
        
        private function unzip($file, $destFolder) {
            $zip = new ZipArchive();
            if($zip->open($file) === true) {
                $zip->extractTo("$destFolder");
                $zip->close();
            } else {
                echo "Failed to open archive";
            }
        }
        
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
