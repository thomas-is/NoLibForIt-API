<?php

namespace API;

abstract class Service {

  protected $request;

  public function __construct() {
    $this->request = new Request;
    $this->handle();
  }

  protected function handle() {

  }


}



?>
