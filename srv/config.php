<?php

define( "DIR_SRV"     , getenv("DIR_SRV")      ?: "/srv"          );
define( "DIR_PHP"     , getenv("DIR_PHP")      ?: "/srv/php"      );
define( "DIR_WEBROOT" , getenv("DIR_WEBROOT")  ?: "/srv/webroot"  );

define( "API_AUTH_FILE", getenv("API_AUTH_FILE") ?: "/srv/token"  );
define( "API_BASE_URI",  getenv("API_BASE_URI")  ?: "/api"        );
define( 'API_SERVICE', array(
  'ping'    => '\Service\Ping',
  'auth'    => '\Service\Auth',
));

?>
