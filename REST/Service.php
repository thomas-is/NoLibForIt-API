<?php

namespace NoLib\REST;

abstract class Service {

  protected $request;
  protected $answer;

  public function __construct() {

    $this->request = new Request;
    $this->answer  = new Answer;

    $this->answer->code = 405;  // defaulting to "Method not allowed"

    $method = $this->request->method;
    if( method_exists($this,$method) ) { $this->$method(); }

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

?>
