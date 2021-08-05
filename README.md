# NoLibForIt\API

An API layer in PHP to handle queries and answers on specific endpoints.


## TL;TR

See <https://github.com/thomas-is/php-skeleton> to run a fully configured
environnement using docker.

## Setup

### Environnement variables

`API_BASE_URI` defines the services location.
```php
define( "API_BASE_URI", "/api" );
```

The http server **must** be configured to rewrite `API_BASE_URI` to
`API_BASE_URI/index.php` which must call `Engine::handle()`.

`API_SERVICE` defines the routing table to map endpoints to an object which
extends `\NoLibForIt\API\Service`
```php
define( 'API_SERVICE', array(
  'ping'   => '\NoLibForIt\Service\Ping',
  'server' => '\NoLibForIt\Service\DumpServer',
  'auth'   => '\NoLibForIt\Service\CheckAuth',
));
```

`API_AUTH_FILE` defines the file containing the token expected in the
`Authorization` header request.
```php
define( "API_AUTH_FILE", "/srv/token"  );
```

### Authorization

Authorization **must** be required explicity in the service object with a
`$this->request->requireAuth();`

If auth fails, the `requireAuth()` method **will** die with a 403.

The `API_AUTH_FILE` content (the token) is parsed excluding starting and
trailing " ","\n","\r".

Once parsed, the token **must** be at least 16 chars long.


## Service object

The `Service` object is the abstract object which implements
- `$this->request`
- `$this->answer`
- `$this->handle()`

The `handle()` method is called when querying the endpoint.


### Answer with a body

Depending on the request method, body answer **may** be ignored even if
specified.

Any http server will discard the body of an answer to an `HEAD` request.

You **should** always check for the request method.
