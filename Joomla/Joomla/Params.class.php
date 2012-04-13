<?php

/**
* Reads parameters passed in from the command line and verifies they all exist.
* Provides a useful object to manipulate cmd parameters
*
* @author Ben Lobaugh <ben@lobaugh.net>
* @url http://ben.lobaugh.net
* @license Free to use and modify as you choose. Please give credits.
*/
class Params {
    
    /**
    * Holds a list of parameters for the command line tools
    *
    * array (<param_name>, array(<required|optional>, <value>, <message>, <get_from>))
    * @var Array
    */
    private $mParams;
    
    /**
    * Holds a list of missing required parameters
    *
    * @var String
    */
    private $mError;
    
    public function __construct(){
        $this->buildmParams();
    }
    
    /**
    * Returns the number of parameters passed
    *
    * @return Integer
    */
    public function count() {
        global $argv;
        return count($argv);
    }
    
    /**
    * Adds a new parameter to the list of parameters that should be available
    *
    * @param String $name - The flag to be used on the parameter
    * @param Boolean $required - Boolean indicating if the parameter is required to be on the command line
    * @param String $default_value - Default value for optional parameters
    * @param String $message - Helpful message to be displayed to a user describing the parameter
    */
    public function add($name, $required, $default_value, $message) {
        $this->mParams[$name] = array('required' => $required, 'default_value' => $default_value, 'message' => $message);
    }
    
    /**
    * Removes a parameter from the list of available parameters
    *
    * @param String $name
    */
    public function remove($name) {
        unset($this->mParams[$name]);
    }
    
    /**
    * Checks the passed in parameters against the required list to ensure
    * all parameters that are required are present
    */
    public function verify() {
        $params = $this->getCmdParams();
    
        $keys = array_keys($params);
        $msg = "";
        if(is_array($this->mParams)) {
            foreach($this->mParams AS $k => $v) { 
                if(isset($params[$k])) $this->set($k, $params[$k]); // Set all values from the cmd line params
                if($v['required'] /* required */ && (!in_array($k, $keys) || is_null($params[$k]))) {
                    $msg .= "\n{$k} - {$v['message']}";
                }
            }
            if($msg != '') {
                $this->mError = $msg;
                return false;
            }
        }
        return true;
    }
    
    /**
    * Builds up $this->mParams with the parameters that currently exist
    */
    private function buildmParams() {
        $params = $this->getCmdParams();
        
        foreach($params as $k => $v) {
            $this->add($k, false, 'unknown', 'Auto-added from command line parameter');
            $this->set($k, $v);
        }
    }
    
    /**
    * Returns the error message from verify()
    * @return String
    */
    public function getError() {
        return $this->mError;
    }
    
    /**
    * Sets the value of a command line parameter
    *
    * @param String $param
    * @param String $value
    */
    public function set($param, $value) {
        $this->mParams[$param]['value'] = $value;
    }
    
    /**
    * Returns the value of a single command line parameter
    *
    * @param String $param
    * @return String
    */
    public function get($param) {
        if(isset($this->mParams[$param]['value'])) return $this->mParams[$param]['value'];
        if(!isset($this->mParams[$param])) return false;
        return $this->mParams[$param]['default_value'];
    }
    
    /**
    * Returns an associative array of the parameters passed via the command
    * line or set through a default
    *
    * @return Array
    */
    public function valueArray() {
        $arr = array();
        if(is_array($this->mParams)) {
            foreach($this->mParams AS $k => $v) {
                $val = '';
                if(isset($v['value'])) $val = $v['value'];
                else if(isset($v['default_value'])) $val = $v['default_value'];
                
                $arr[$k] = $val;
            }
        }
        return $arr;
    }
    
    
    /**
    * Returns all the parameters passed by the command line as key/value pairs.
    * If a flag is used (param with no value) it will be set to true
    *
    * @global Array $argv
    * @return Array
    */
    private function getCmdParams() {
        global $argv;

        $params = array();
        for($i = 0; $i < count($argv); $i++) {
            if(substr($argv[$i], 0, 1) == '-') {
                if($i <= count($argv)-2 && substr($argv[($i + 1)], 0, 1) != '-') {
                    // Next item is flag
                    $value = $argv[$i + 1];
                } else {
                    $value = "true";
                }
                $key = str_replace("-", "", $argv[$i]);
                $params[$key] = $value;
            }
        }
        return $params;
    }
    
    /**
    * Convert this object into a string
    */
    public function __toString() {
        return print_r($this->mParams, true);
    }
    
} // end class