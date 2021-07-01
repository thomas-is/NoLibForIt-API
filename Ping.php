<?php

namespace NoLibForIt\API;

class Ping extends Service {

  public function handle() {
    $this->answer->json(200,$this->request);
  }


}
