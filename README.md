# NoLibForIt\API

An API layer in PHP to handle queries and answers on specific endpoints.


## TL;TR

See <https://github.com/thomas-is/php-skeleton> to run a fully configured
environnement using docker.

## Setup

Assuming `/api` is the relative path you want to configure on your server, first
add a rewrite rule to redirect `/api/*` to `/api/index.php`.

For Nginx, add:
```
  location /api/ {
    rewrite /api/(.*) /api/index.php last;
  }
```

Then in `/api/index.php`
- you may either use an autoloader or include:
  - Answer.php
  - Engine.php
  - Request.php
- define `API_BASE_URL` as `"/api"`
- declare an associative array to map each endpoint to a class
- invoke `Engine::handle($map)`

If you want to use the token authentication, you **must** define `API_AUTH_FILE`
with the path to the file containing the token (see below).

If defined, `API_ALLOW_ORIGIN` controls the `Access-Control-Allow-Origin` header
value. Set to `"*"` to avoid client side javascript CORS issues.

Example:
```php
<?php

include('/srv/autoloader.php');

define( "API_BASE_URI", "/api" );
define( "API_AUTH_FILE", getenv("API_AUTH_FILE") ?: "/srv/token" );
define( "API_ALLOW_ORIGIN", "*" );

$map = array(
  'ping'   => '\NoLibForIt\Service\Ping',
  'server' => '\NoLibForIt\Service\DumpServer',
  'auth'   => '\NoLibForIt\Service\CheckAuth',
);

NoLibForIt\API\Engine::handle($map);

?>
```

## Service object

Each service object **must**
- have a **static** `handle($request)` method
- have a way to call `Answer` **staticaly**

Example:
```php
<?php

namespace Vendor\Service;

use \NoLibForIt\API\Answer as Answer;

class Ping {

  public static function handle($request) {
    Answer::json(200,$request);
  }

}

?>
```

### Authorization

Authorization **must** be required explicity in the service object with a
`$request->requireAuth();`

If the request does not have an Authorization header, the method **will** die
with a 401 (unauthorized).
If an Authorization header is specified but mismatch the token, the method
**will** die with a 403 (forbidden).

The `API_AUTH_FILE` content (the token) is parsed excluding starting and
trailing " ","\n","\r".

Once parsed, the token **must** be at least 16 chars long.


### Answer with a body

Depending on the request method, body answer **may** be ignored even if
specified.

Any http server will discard the body of an answer to an `HEAD` request.

You **should** always check for the request method.
