<?php

namespace Nolib\REST;

class Pong extends Service {

  public function __construct() {
    $answer->code = 200;
    $answer->attach($this->request);
    $answer->send();
  }

}

?>
