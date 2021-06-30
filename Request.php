<?php

namespace NoLibForIt\API;

/**
  *   arguments can be passed via url
  *   (ie: mydomain.org/api/command/foo/bar)
  *   following C notation:
  *    argc is argument count (including 'command')
  *    argv is arguments list (including 'command')
  *   $argv[0] == 'command'
  *   $argv[1] == 'foo'
  *   $argv[2] == 'bar'
  **/
class Request {

  public int    $argc   = 0;
  public array  $argv   = array();
  public array  $param  = array();
  public array  $header = array();
  public string $method = "";
  public string $body   = "";

  public function __construct() {

    if( empty(@API_BASE_URI) ) {
      Anwser::code(520);
    }

    $uri = $_SERVER['REQUEST_URI'];

    if( substr($uri,0,strlen(API_BASE_URI)) != API_BASE_URI ) {
      Anwser::code(520);
    }

    $uri = substr($uri,strlen(API_BASE_URI));
    $uri = parse_url($uri);

    if( empty(@$uri['path']) ) {
      Answer::code(520);
    }

    $arg = explode( '/', $uri['path'] );
    if( empty($arg[0]) ) {
      array_shift($arg);
    }
    foreach( $arg as $encoded ) {
      $this->argv[]  = urldecode($encoded);
    }
    $this->argc = count($this->argv);


    $param = array();
    if( @$uri['query'] ) {
      $param = explode( '&', $uri['query'] );
    }
    foreach( $param as $line ) {
      $element = explode("=",$line);
      $key     = urldecode(@$element[0]);
      $value   = urldecode(@$element[1]);
      if( $key == "" ) { continue; }
      $this->param[$key] = $value;
    }

    foreach($_SERVER as $key => $value) {
      if ( substr($key,0,5) <> 'HTTP_' ) {
        continue;
      }
      $prop = str_replace(
        ' ','-',
        ucwords(
          str_replace(
            '_',' ',
            strtolower(
              substr($key, 5)
            )
          )
        )
      );
      $this->header[$prop] = $value;
    }

    $this->method  = @$_SERVER['REQUEST_METHOD'];
    $this->body    = file_get_contents('php://input');

  }

  public function requireAuth() {
    $xauth = @$this->header["Authorization"];
    if( ! $xauth ) {
      Answer::code(401);
    }
    if( ! file_exists(@API_AUTH_FILE) ) {
      Answer::code(503);
    }
    $token = file_get_contents(API_AUTH_FILE);
    $token = trim($token);
    if( $xauth != $token ) {
      Answer::code(403);
    }
  }


}

?>
