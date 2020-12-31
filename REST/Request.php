<?php

namespace NoLib\REST;

class Request {

  public  $args   = array();
  public  $params = array();
  /*
   *  /service/foo/bar?key=abc
   *
   *  $args   == array( 'service', 'foo', 'bar' )
   *  $params == array( "key" => "abc" )
   */
  public  $headers = array();
  public  $method;
  public  $body;

  public function __construct() {

    $path  = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
    $query = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY );

    $this->args = explode('/',$path);
    array_shift($this->args);

    parse_str( $query , $this->params);
    array_shift($this->params);

    foreach($_SERVER as $key => $value) {
      if (substr($key, 0, 5) <> 'HTTP_') { continue; }
      $header = substr($key, 5);
      $header = strtolower($header);
      $header = ucwords($header,'_');
      $header = str_replace('_','-',$header);
      $this->headers[$header] = $value;
    }

    $this->method  = @$_SERVER['REQUEST_METHOD'];
    $this->body    = file_get_contents('php://input');

  }

}

?>
