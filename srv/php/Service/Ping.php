<?php

namespace Service;

class Ping extends \API\Service {

  protected function handle() {
    \API\Answer::json(200,$this->request);
  }


}
