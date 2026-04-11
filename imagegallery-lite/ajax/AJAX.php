<?php defined('BLUDIT') or die('Bludit CMS.');
/**
 * AJAX helper for novafacile Bludit Plugins
 * @author    novafacile OÜ
 * @copyright 2022-2026 by novafacile OÜ
 * @license   MIT
 * @see       https://bludit-plugins.com
 * This program is distributed in the hope that it will be useful - WITHOUT ANY WARRANTY.
 */

class AJAX {

  static function setHeader(){
    header('Content-Type: application/json');
  }

  static function auth(){
    self::checkSession();
    self::checkRole();
    self::checkCSRF();
  }

  private static function isHTTPS() : bool {
    $isHTTPS = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
               (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    return $isHTTPS;
    }

  private static function checkSession(){
    // Load Session Handling
    $basePath = dirname( __FILE__, 4); // Bludit3 Base
    define('SESSION_GC_MAXLIFETIME', 3600); // Session timeout server side, gc_maxlifetime (3600 = 1hour)
    require $basePath.DS.'bl-kernel'.DS.'helpers'.DS.'session.class.php';
    
    // Todo: Session start don't work well, if path is set and if no url is set in settings
    Session::start('', self::isHTTPS());
    if (Session::started()===false) {
	    self::exit(401, 'Session initialization failed.');
    }
  }

  private static function checkRole(){
    $role = $_SESSION['s_role'];
    if($role !== 'admin' && $role != 'author' && $role != 'editor'){
      self::exit(401);
    }
  }

  public static function checkCSRF(){
    if(!isset($_SESSION['s_tokenCSRF'])){
      self::exit(401);
    }
   
    if($_POST['tokenCSRF'] !== $_SESSION['s_tokenCSRF']){
      self::exit(401);
    }
  }

  public static function exit($statusCode = 403, $message = false){
    switch ($statusCode) {
      case 200:
        $header = 'HTTP/1.1 200 Found';
        $defaultMessage = 'Success';
        break;
      case 400:
        $header = 'HTTP/1.1 400 Bad Request';
        $defaultMessage = '400 Bad Request';
        break;
      case 401:
        $header = 'HTTP/1.1 401 Unauthorized';
        $defaultMessage = '401 Unauthorized';
        break;
      case 404:
        $header = 'HTTP/1.1 404 Not Found';
        $defaultMessage = '404 Not Found';
        break;
      case 500:
        $header = 'HTTP/1.1 500 Internal Server Error';
        $defaultMessage = 'Internal Server Error';
        break;
      default:
        $header = 'HTTP/1.1 403 Forbidden';
        $defaultMessage = '403 Forbidden';
        break;
    }

    if(!$message){
      $message = $defaultMessage;
    }

    header($header);
    echo json_encode($message);
    exit;
  }
  
}

?>