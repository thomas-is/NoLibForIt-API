#!/bin/bash

docker run --rm -it \
  -p 8888:80 \
  -e API_BASE_URL="/api" \
  -e API_ALLOW_ORIGIN="true" \
  -e API_MAP_FILE="/srv/map.json" \
  -e FPM_CLEAR_ENV="no" \
  -v $(pwd):/srv \
  -v $(pwd)/ng-default.conf:/etc/nginx/http.d/default.conf \
  0lfi/ng-php8
