<?php

namespace NoLibForIt\API;

class Engine {

  /**
    * @param array $map
    * @example Engine::handle(
    *   array(
    *     'ping'   => '\NoLibForIt\Service\Ping',
    *     'server' => '\NoLibForIt\Service\DumpServer',
    *     'auth'   => '\NoLibForIt\Service\CheckAuth',
    *   ))
    * @static
    * @uses NoLibForIt\API\Request
    * @uses NoLibForIt\API\Answer
    **/
  public static function handle( $map ) {
    if( ! is_array($map) ) {
      Answer::json(500,array("error"=>"invalid map"));
    }
    $request = new Request;
    $serviceClass = @$map[@$request->argv[0]];
    if( empty($serviceClass) ) {
      Answer::json(404,array("error"=>"not found"));
    }
    if( ! class_exists($serviceClass) ) {
      Answer::json(500,array("error"=>"class $serviceClass does not exist"));
    }


    if( $request->method == "OPTIONS" ) {

      # Allow: GET,HEAD,POST,OPTIONS,TRACE
      # Content-Type: application/json

      if(property_exists($serviceClass,"allow")) {
        header("Allow: ".$serviceClass::allow);
      }
      if(property_exists($serviceClass,"contentType")) {
        header("Content-Type: ".$serviceClass::contentType);
      }
      Answer::code(200);

    }


    $serviceClass::handle($request);
    Answer::json(520,array("error"=>"service ended with no reply"));
  }

}

?>
