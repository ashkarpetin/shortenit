<?php

require_once (dirname(__FILE__) . '/Config.php');
require_once (dirname(__FILE__) . '/Loader.php');
require_once (dirname(__FILE__) . '/Controller.php');
// composer autoload
require_once(dirname(__FILE__) . '/vendor/autoload.php');

require_once (dirname(__FILE__) . '/Db.php');

new Controller(); 
