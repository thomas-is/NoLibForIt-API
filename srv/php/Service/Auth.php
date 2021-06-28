<?php

namespace Service;

class Auth extends \API\Service {

  protected function handle() {
    $this->request->requireAuth();
    $reply = new \stdClass;
    $reply->message = "Success.";
    \API\Answer::json(200,$reply);
  }


}
