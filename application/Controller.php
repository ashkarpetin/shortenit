<?php
/**
 * Controller class file.
 *
 * @author Alexander Shkarpetin ashkarpetin@gmail.com
 */

require_once (dirname(__FILE__) . '/models/Model.php');
require_once (dirname(__FILE__) . '/models/Url.php');
require_once (dirname(__FILE__) . '/models/Hash.php');

class Controller 
{
   public $loader;

   private $_scriptUrl;
   private $_baseUrl;
   private $_hostInfo;

   public function __construct()
   {                 
      $this->loader = new Loader();
      
      // determine the page
      if (isset($_GET['u']))
      {
         $this->getHash();
      }
      else if (isset($_GET['h']))
      {
         $this->getUrl();
      }
      else
      {
         $this->home(); 
      }
   }

   /**
    * Controller action. Home page.
    */
   public function home()
   {
      $this->loader->view('home.php');
   }

   /**
    * Controller action. Process request provided by user long url to get shortened url.
    */
   public function getHash()
   {
      $model = new Url();
      $model->url = $_GET['u'];
      if ($model->validate())
      {
         $hash = $model->toHash();
         $output = array('return' => 1, 'msg' => '', 'url' => $this->getBaseUrl(true) . (URL_REWRITE ? "/" : "?h=") . $hash);
      }
      else
      {
         $output = array('return' => 0, 'msg' => $model->error, 'url' => '');   
      }
      header('Content-Type: application/json');
      echo json_encode(array($output));  
   }

   /**
    * Controller action. Process request provided by user short url to get long url.
    */
   public function getUrl()
   {    
      $model = new Hash();
      $model->hash = $_GET['h'];
      if ($model->validate())
      {
         $url = $model->toUrl();
         header('HTTP/1.1 301 Moved Permanently');
         header('Location: '.$url);
         header("Cache-Control: no-store, no-cache, must-revalidate");
         header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
      }
      else
      {
         $this->loader->view('home.php', array('error' => $model->error));
      }
      return;
   }

   /**
    * Returns the entry script relative URL.
    * @return string the entry script relative URL.
    */
   protected function getScriptUrl()
   {
      if($this->_scriptUrl === null)
      {
         $scriptName = basename($_SERVER['SCRIPT_FILENAME']);
         if(basename($_SERVER['SCRIPT_NAME']) === $scriptName)
            $this->_scriptUrl = $_SERVER['SCRIPT_NAME'];
         elseif(basename($_SERVER['PHP_SELF']) === $scriptName)
            $this->_scriptUrl = $_SERVER['PHP_SELF'];
         elseif(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName)
            $this->_scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
         elseif(($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false)
            $this->_scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos). '/' . $scriptName;
         elseif(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0)
            $this->_scriptUrl = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
         else
            throw new Exception('Unable to determine the entry script URL.');
      }
      return $this->_scriptUrl;
   }
   
   /**
    * Returns the application relative URL.
    * @param boolean $absolute To return absolute URL. Defaults to false - retuen relative URL.
    * @return string application relative URL
    */
   protected function getBaseUrl($absolute = false)
   {
      if($this->_baseUrl === null)
         $this->_baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');
      return $absolute ? $this->getHostInfo() . $this->_baseUrl : $this->_baseUrl;
   }
   
   /**
    * Return host part of application URL
    * @return string hostname part the request URL (with port number if needed)
    * @see setHostInfo
    */
   protected function getHostInfo()
   {
      if($this->_hostInfo === null)
      {
         if($secure = (!empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'],'off')))
            $http = 'https';
         else
            $http = 'http';
         if(isset($_SERVER['HTTP_HOST']))
            $this->_hostInfo = $http . '://' . $_SERVER['HTTP_HOST'];
         else
         {
            $this->_hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
            $port = $secure ? (isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 443) : (isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 80);
            if(($port !== 80 && !$secure) || ($port !== 443 && $secure))
               $this->_hostInfo .= ':' . $port;
         }
      }
      return $this->_hostInfo;
   }
}
