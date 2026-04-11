<?php
/**
 * AJAX request handler for Bludit Image Gallery Lite
 * @author    novafacile OÜ
 * @copyright 2022-2026 by novafacile OÜ
 * @license   AGPL-3.0
 * @see       https://bludit-plugins.com
 * This program is distributed in the hope that it will be useful - WITHOUT ANY WARRANTY.
 */
// Security constant
define('BLUDIT', true);
define('DS', DIRECTORY_SEPARATOR);

// DEBUG Settings
define('DEBUG', 'false');

if(DEBUG){
  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
  error_reporting(E_ALL);
}

// Load Helper Classes
require 'AJAX.php';

// set JSON Header
AJAX::setHeader();

// auth
AJAX::auth();

// check parameters
if(!isset($_POST['action'])){
  AJAX::exit('400', 'Missing action in request.');
}

// clear POST data
foreach ($_POST as $key => $value) {
  if(strpos($value, '..'.DS) !== false){
    AJAX::exit();
  }
  $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  $_POST[$key] = $value;
}

// set vars
$action = $_POST['action']; // Todo: some protection
$success = false;
$basePath = dirname( __FILE__, 4);
$pluginPath = dirname(pathinfo(__FILE__, PATHINFO_DIRNAME));
$storageRoot = 'imagegallery';
$storage = $basePath.DS.'bl-content'.DS.$storageRoot;
$configFile = $basePath.DS.'bl-content'.DS.'databases'.DS.'plugins'.DS.'imagegallery-lite'.DS.'db.php';

// load helper
require 'Config.php';
require 'helper.php';

// perform action
switch ($action) {
  case 'deleteImage':
    $album = $_POST['album']; // Todo: add some protection
    $file = $_POST['file']; // Todo: add some protection
    checkFileDirExists($storage.DS.$album.DS.$file);
    $success = deleteImage($storage, $album, $file);
    break;

  case 'uploadImage':
    $album = $_POST['album']; // Todo: add some protection
    $config = new Config($configFile, true);
    $success = uploadImage($pluginPath, $storage.DS.$album, $config);
    @unlink($storage.DS.$album.DS.'cache'.DS.'files.php'); // clear album cache
    @unlink($storage.DS.'cache'.DS.'files.php'); // clear global cache
    break;

  default:
    $success = false;
    break;
}

if($success){
  AJAX::exit(200, $success);
} else {
  AJAX::exit(500);
}


?>
