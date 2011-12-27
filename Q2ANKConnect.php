<?php

require_once 'nk-php-sdk/src/NK.php';

class Q2ANKConnect
{
  const NKCONNECT_KEY = 'nkconnect_site_key';
  const NKCONNECT_SECRET = 'nkconnect_site_secret';
  const LOGIN_SOURCE = 'nkconnect';
  
  /**
   * 
   * @var NKConnect
   */
  private $auth;
  
  private static $instance;

  public static function instance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function init()
  {
    if ($this->auth === null) {
      $this->auth = new NKConnect($this->get_config());
    }
  }

  private function get_config()
  {
    $config = new NKConfig();
    $config->key = qa_opt(self::NKCONNECT_KEY);
    $config->secret = qa_opt(self::NKCONNECT_SECRET);
    $config->permissions = array(
        NKPermissions::BASIC_PROFILE, 
        NKPermissions::EMAIL_PROFILE
    );
    return $config;
  }

  public function get_login_button()
  {
    $button = null;
    try{
      $auth = $this->getAuth();
      $auth->handleCallback();
      $button = $auth->button();
    }catch (NKConnectUnusableException $e){
      error_log($e->getMessage());
    }
    return $button;
  }

  /**
   * @return NKConnnect
   */
  public function getAuth()
  {
    $this->init();
    return $this->auth;
  }

  /**
   * 
   * @return bool
   */
  public static function is_ready_data()
  {
    return qa_opt(self::NKCONNECT_KEY) != "" && qa_opt(self::NKCONNECT_SECRET) != "";
  }
}

?>