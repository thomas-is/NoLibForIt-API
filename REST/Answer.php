<?php

namespace NoLib\REST;

class Answer {

  public $code = 501;
  public $content_type;
  public $body;
  /*
   * some request methods (like HEAD) won't allow a body in the reply.
   * $this->body may be ignored.
   */
  public function send() {
    header($_SERVER["SERVER_PROTOCOL"]." {$this->code}");
    if( $this->content_type ) {
      header("Content-Type: {$this->content_type}");
    }
    die($this->body);
  }

  public function attach( $obj ) {
    $this->content_type = "application/json";
    $this->body = json_encode($obj);
  }

}

?>
