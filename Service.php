<?php

namespace NoLibForIt\API;


abstract class Service {

  protected $request;
  protected $answer;

  public function __construct( $request ) {
    $this->request = $request;
    $this->answer = new Answer;
  }

  public function handle() {
    $this->answer->json(500,array("error"=>"not implemented"));
  }

}



?>
