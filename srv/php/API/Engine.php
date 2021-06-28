<?php

namespace API;

class Engine {

  private $request;

  public function __construct() {
    $this->request = new Request;
    $service = @API_SERVICE[@$this->request->argv[0]];
    if( ! class_exists($service) ) {
      Answer::code(400);
    }
    new $service;
    Answer::code(520);
  }

}

?>
