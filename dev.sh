#!/bin/sh

docker run --rm -it \
  --name test \
  -p 8080:80 \
  -v $(pwd):/srv \
  0lfi/ng-php7
