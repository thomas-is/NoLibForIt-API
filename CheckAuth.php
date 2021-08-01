<?php

namespace NoLibForIt\API;

class CheckAuth extends Service {

  public function handle() {
    $this->request->requireAuth();
    $this->answer->json(200,array("message"=>"Access granted"));
  }

}

?>
