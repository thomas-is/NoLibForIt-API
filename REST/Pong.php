<?php

namespace Nolib\REST;

class Pong extends Core {

  public function __construct() {
    $answer->code = 200;
    $answer->attach($this->request);
  }

}
