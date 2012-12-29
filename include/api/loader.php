<?php
class dbinfo {
  var $host = 'localhost';
  var $user = 'root';
  var $pass = 'pass';
  var $db   = 'dbname';
  var $name = 'dbinfo';
  var $core = 'DefaultAccount';
  var $_con = null;
  var $maxplayers = 0;

  function &con() {
    if ( is_object( $this->_con ) && Database::ping( $this->_con ) )
      return $this->_con;

    $this->_con = Database::connect( $this->name, array($this->host, $this->user, $this->pass, $this->db));
    return $this->_con;
  }
  function getcore() {
    if ( $this->core == 'DefaultAccount' )
      return null;

    $this->core = ucfirst( $this->core );
    $tmp        = NULL;

    if ( class_exists( $this->core ) ) {
      $tmp       = new $this->core();
      $tmp->_con = $this->con();
    }

    return $tmp;
  }
}

class Config {
  static $site  = array();
  static $forum = array();
  static $accounts = array();
  static $characters = array();
  static $version = "1.0.42 (Beta)";

  static function Build() {
    global $site, $forum, $accounts, $characters;
    self::$site = new dbinfo();

    foreach ( $site as $key => $value )
      self::$site->$key = $value;

    self::$forum = new dbinfo();
    if ( isset($forum) && is_array($forum) )
    {
        foreach ( $forum as $key => $value )
            self::$forum->$key = $value;
    }

    self::$accounts = new dbinfo();

    foreach ( $accounts as $key => $value )
      self::$accounts->$key = $value;

    foreach ( $characters as $key => $a ) {
      self::$characters[$key] = new dbinfo();
      foreach ( $a as $k => $v )
        self::$characters[$key]->$k = $v;
    }
  }
  static function findcore( $match ) {
      return self::$accounts->getcore();
    foreach ( self::$accounts as $a ) {
      if ( strtolower( $match ) == strtolower( $a->core ) )
        return $a->getcore();
    }

    return null;
  }
}
?>
