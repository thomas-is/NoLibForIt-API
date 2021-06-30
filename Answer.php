<?php

namespace NoLibForIt\API;

class Answer {

  private static function http( int $code ) {
    header($_SERVER["SERVER_PROTOCOL"]." $code");
  }

  public static function json( $code, $obj ) {
    $body = json_encode($obj, JSON_INVALID_UTF8_IGNORE);
    if( empty($body) ) {
      self::code(520);
    } else {
      self::http($code);
      header("Content-Type: application/json");
      die($body);
    }
  }

  public static function code( int $code ) {
    self::http($code);
    die();
  }

}

?>
