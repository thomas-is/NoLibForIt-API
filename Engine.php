<?php

namespace NoLibForIt\API;

class Engine {

  public static function handle() {
    $request = new Request;
    $serviceClass = @API_SERVICE[@$request->argv[0]];
    if( ! class_exists($serviceClass) ) {
      Answer::code(400);
    }
    $service = new $serviceClass($request);
    $service->handle();
    Answer::code(520);
  }

}

?>
