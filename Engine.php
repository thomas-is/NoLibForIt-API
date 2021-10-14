<?php

namespace NoLibForIt\API;

/**
  * @uses API_BASE_URL
  * @uses API_ALLOW_ORIGIN
  * @uses API_MAP_FILE
  * {
  *   "ping"   : "\NoLibForIt\Service\Ping",
  *   "server" : "\NoLibForIt\Service\DumpServer",
  *   "auth"   : "\NoLibForIt\Service\CheckAuth"
  * }
  **/


abstract class Service {
  public static $allow       = array( "GET", "POST", "PUT");
  public static $contentType = "application/json";
  public static function handle( $request ) {
    Answer::code(501);
  }
}

class Ping extends Service {
  public static function handle( $request ) {
    Answer::json(200,$request);
  }
}


class Engine {

  public static function start() {

    $map = json_decode(file_get_contents(getenv('API_MAP_FILE')),true);

    if( empty($map) ) {
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

      if( property_exists($serviceClass,"allow") ) {
        if( is_array($serviceClass::allow) && ! empty($serviceClass::allow) ) {
          header("Allow: ".implode(",",$serviceClass::allow));
        }
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


class Answer {

  private static function http( int $code ) {
    $protocol = @$_SERVER['SERVER_PROTOCOL'] ?: "HTTP/1.1";
    header("$protocol $code");
    if( ! empty(getenv('API_ALLOW_ORIGIN')) ) {
      header("Access-Control-Allow-Origin: ".getenv('API_ALLOW_ORIGIN'));
    }
  }

  public static function code( int $code ) {
    self::http($code);
    die();
  }

  public static function content( int $code, $type, $body ) {
      self::http($code);
      header("Content-Type: $type");
      die($body);
  }

  public static function json( int $code, $obj ) {
    $body = json_encode($obj, JSON_INVALID_UTF8_IGNORE);
    if( empty($body) ) {
      self::json(520,array("error"=>"unable to encode json"));
    }
    self::content($code,"application/json",$body);
  }

}

/**
  * API_BASE_URL must be defined
  * the REQUEST_URI is parsed relatively to API_BASE_URL
  *
  * for example with
  *   API_BASE_URL/endpoint/foo/bar?flag&prop=42
  *
  *   $this->argv[0]       = 'endpoint'
  *   $this->argv[1]       = 'foo'
  *   $this->argv[2]       = 'bar'
  *   $this->param['flag'] = ''
  *   $this->param['prop'] = '42'
  *
  * $this->argc     arguments count
  * $this->argv     arguments list
  * $this->param    associative array or query parameters
  * $this->header   associative array of HTTP headers
  * $this->method   method
  * $this->body     body
  *
  * all query parameters are set as strings
  *
  * empty argv are allowed
  *   API_BASE_URL/////foo
  * will set
  *   $this->argv[0] = ''
  *   $this->argv[1] = ''
  *   $this->argv[2] = ''
  *   $this->argv[3] = ''
  *   $this->argv[4] = 'foo'
  *
  * empty query param is allowed
  *   API_BASE_URL/endpoint?=42
  * will set
  *   $this->param[''] = '42'
  *
  * in case of duplicated query param, only the last one is set
  *   API_BASE_URL/endpoint?a=1&a=2&a=3
  * will set
  *   $this->param['a'] = '3'
  *
  *
  **/
class Request {

  public  int    $argc     = 0;
  public  array  $argv     = array();
  public  array  $param    = array();
  public  array  $header   = array();
  public  string $method   = "";
  public  string $body     = "";
  private string $baseURL  = "";
  private string $authFile = "";

  public function __construct() {

    if( ! isset($_SERVER) ) {
      Answer::json(500,array("error"=>"undefined \$_SERVER"));
    }
    if( ! isset($_SERVER['REQUEST_URI']) ) {
      Answer::json(500,array("error"=>"undefined \$_SERVER['REQUEST_URI']"));
    }

    $this->baseURL  = getenv('API_BASE_URL');
    $this->authFile = getenv('API_AUTH_FILE');

    if( empty($this->baseURL) ) {
      Answer::json(500,array("error"=>"undefined API_BASE_URL"));
    }

    $uri = $_SERVER['REQUEST_URI'];

    if( substr($uri,0,strlen($this->baseURL)) != $this->baseURL ) {
      Answer::json(500,
        array(
          "error"        => "API_BASE_URL mismatch",
          "API_BASE_URL" => $this->baseURL,
          "REQUEST_URI"  => $uri,
        )
      );
    }

    $uri = substr($uri,strlen($this->baseURL));
    $uri = parse_url($uri);

    if( empty(@$uri['path']) ) {
      Answer::json(500,array("error"=>"no path in URI"));
    }

    $arg = explode( '/', $uri['path'] );
    if( empty($arg[0]) ) {
      array_shift($arg);
    }
    foreach( $arg as $urlencoded ) {
      $this->argv[]  = urldecode($urlencoded);
    }
    $this->argc = count($this->argv);

    $param = array();
    if( @$uri['query'] ) {
      $param = explode( '&', $uri['query'] );
    }
    foreach( $param as $line ) {
      $element = explode("=",$line);
      $key     = urldecode(@$element[0]);
      $value   = urldecode(@$element[1]);
      $this->param[$key] = $value;
    }

    foreach($_SERVER as $key => $value) {
      if ( substr($key,0,5) <> 'HTTP_' ) {
        continue;
      }
      $prop = str_replace(' ','-',
        ucwords(
          str_replace('_',' ',
            strtolower(
              substr($key, 5)
            )
          )
        )
      );
      $this->header[$prop] = $value;
    }

    $this->method  = @$_SERVER['REQUEST_METHOD'];
    $this->body    = file_get_contents('php://input');

  }

  public function requireAuth() {

    $xauth = @$this->header["Authorization"];
    if( ! $xauth ) {
      Answer::json(401,array("message"=>"Unauthorized"));
    }

    if( empty($this->authfile) ) {
      Answer::json(500,array("error"=>"undefined API_AUTH_FILE"));
    }
    if( ! file_exists($this->authFile) ) {
      Answer::json(500,array("error"=>"API_AUTH_FILE not found"));
    }

    $token = (string) file_get_contents($this->authFile);
    $token = trim($token);

    if( strlen($token) < 16 ) {
      Answer::json(500,array("error"=>"token too small"));
    }

    if( $xauth != $token ) {
      Answer::json(403,array("message"=>"Forbidden"));
    }

  }


}

?>
