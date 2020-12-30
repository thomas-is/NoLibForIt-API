<?php

namespace NoLib\REST;

class Request {

  public  $argc;   // (int)   path: argument count
  public  $argv;   // (array) path: arguments
  public  $params; // (array) parameters after "?"
  /*
   *  arguments can be passed via url
   *  (ie: mydomain.org/api/command/foo/bar?key=abc)
   *  following C notation:
   *   argc is argument count (including 'command')
   *   argv is arguments list (including 'command')
   *  $argv[0] == 'command'
   *  $argv[1] == 'foo'
   *  $argv[2] == 'bar'
   *  $params == array( "key" => "abc" )
   */
  public  $headers = array();
  public  $method;
  public  $body;

  public function __construct() {

    $this->argv = explode( '/', $_SERVER['QUERY_STRING'] );
    $this->argc = count($this->argv);

    $last = $this->argv[$this->argc - 1];
    parse_str($last, $this->params);
    array_shift($this->params);

    foreach($_SERVER as $key => $value) {
      if (substr($key, 0, 5) <> 'HTTP_') continue;
      $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
      $this->headers[$header] = $value;
    }

    $this->method  = @$_SERVER['REQUEST_METHOD'];
    $this->body    = file_get_contents('php://input');

  }

}

?>
