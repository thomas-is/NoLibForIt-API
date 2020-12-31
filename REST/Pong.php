<?php

namespace Nolib\REST;

class Pong extends Service {

  public function GET() {
    $this->answer->code = 200;
    $this->answer->attach($this->request);
    $this->answer->send();
  }

}

?>
