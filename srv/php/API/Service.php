<?php

namespace API;

abstract class Service {

  protected $request;
  protected $answer;

  public function __construct() {
    $this->request = new Request;
    $this->answer  = new Answer;
    $this->handle();
  }

  protected function handle() {

  }


}



?>
