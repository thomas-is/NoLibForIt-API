<?php

namespace NoLibForIt\API;

class Answer {

  private static function http( int $code ) {
    $protocol = @$_SERVER['SERVER_PROTOCOL'] ?: "HTTP/1.1";
    header("$protocol $code");
    if( defined('API_ALLOW_ORIGIN') ) {
      header("Access-Control-Allow-Origin: ".API_ALLOW_ORIGIN);
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
      self::json(520,array("error"=>"unable to encode json"));
    }
    self::content($code,"application/json",$body);
  }

}

?>
