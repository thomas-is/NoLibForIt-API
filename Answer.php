<?php

namespace NoLibForIt\API;

class Answer {

  private static function http( int $code ) {
    if( ! defined('API_HEADERS') ) {
      define('API_HEADERS',array());
    }
    $protocol = @$_SERVER['SERVER_PROTOCOL'] ?: "HTTP/1.1";
    header("$protocol $code");
    foreach( API_HEADERS as $key => $value ) {
      header("$key: $value");
    }
  }

  public static function code( int $code ) {
    self::http($code);
    die();
  }

  public static function content( int $code, $type, $body ) {
      self::http($code);
      header("Content-Type: $type");
      die($body);
  }

  public static function json( int $code, $obj ) {
    $body = json_encode($obj, JSON_INVALID_UTF8_IGNORE);
    if( empty($body) ) {
      self::code(520);
    } else {
      self::content($code,"application/json",$body);
    }
  }

}

?>
