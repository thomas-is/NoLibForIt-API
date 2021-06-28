<?php

namespace Service;

class Ping extends \API\Service {

  public function __construct() {
    parent::__construct();
  }

  protected function handle() {
    $this->answer->json($this->request);
  }


}
