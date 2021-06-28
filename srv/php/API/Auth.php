<?php

namespace API;

class Auth {

  public function __construct( $request ) {

    $answer = new Answer;
    $xauth = @$this->request->headers["Authorization"];

    if( ! $xauth ) {
      $answer->code = 401;
      $answer->send();
    }

    if( ! file_exists(@API_AUTH_FILE) ) {
      $answer->code = 503;
      $answer->send();
    }

    $token = file_get_contents(API_AUTH_FILE);
    $token = trim($token);

    if( $xauth != $token ) {
      $this->answer->code = 403;
      $this->answer->send();
    }

  }


}



?>
