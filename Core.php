<?php

namespace NoLib\REST;

abstract class Core {

  protected $request;
  protected $answer;

  public function __construct() {

    $this->request = new Request;
    $this->answer  = new Answer;

    $this->answer->code = 405;  // defaulting to "Method not allowed"

    $method = $this->request->method;
    if( method_exists($this,$method) ) $this->$method();

    $this->answer->send();

  }

  // valid methods *MUST* be declared as empty here
  // overridden by extended classes

  // Valid HTTP 1.1 methods used in RESTful
  // https://restfulapi.net/http-methods/
  public function GET()       {}
  public function HEAD()      {}
  public function POST()      {}
  public function PUT()       {}
  public function DELETE()    {}
  // Extended HTTP method used in RESTful
  // https://tools.ietf.org/html/rfc5789
  public function PATCH()     {}
  // HTTP 1.1 methods *NOT USED* in RESTful
  // https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
  // https://tools.ietf.org/html/rfc7231
  public function CONNECT()   {}
  public function OPTIONS()   {}
  public function TRACE()     {}
  // WebDAV
  // https://tools.ietf.org/html/rfc4918
  public function PROPFIND()  {}
  public function LOCK()      {}
  public function UNLOCK()    {}
  public function COPY()      {}
  public function MOVE()      {}

  public function UPDATE()    {}
  public function LINK()      {}
  public function UNLINK()    {}
  public function VIEW()      {}
  public function PURGE()     {}

}


class Request {

  public  $argc;   // (int)   argument count
  public  $argv;   // (array) arguments
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

class Answer {

  public $code = 501;
  public $content_type;
  public $body;
  /*
   *
   * some request methods (like HEAD) won't allow a body in the reply.
   * $this->_body may be ignored.
   *
   */
  public function send() {
    header($_SERVER["SERVER_PROTOCOL"]." {$this->code}");
    if( $this->content_type ) {
      header("Content-Type: {$this->content_type}");
    }
    die("$this->body");
  }

  public function attach( $obj ) {
    $this->content_type = "application/json";
    $this->body = json_encode($obj);
  }

}

?>
