<?php

namespace NoLibForIt\API;

class DumpServer extends Service {

  public function handle() {
    $this->answer->json(200,$_SERVER);
  }

}

?>
