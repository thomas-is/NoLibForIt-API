<?php

namespace NoLibForIt\API;

class Engine {

  public static function handle() {
    if( ! defined('API_SERVICE') ) {
      Answer::json(500,array("error"=>"API_SERVICE is undefined"));
    }
    $request = new Request;
    $serviceClass = @API_SERVICE[@$request->argv[0]];
    if( empty($serviceClass) ) {
      Answer::json(404,array("error"=>"not found"));
    }
    if( ! class_exists($serviceClass) ) {
      Answer::json(500,array("error"=>"class $serviceClass does not exist"));
    }
    $service = new $serviceClass($request);
    $service->handle();
    Answer::json(520,array("error"=>"service ended with no reply"));
  }

}

?>
