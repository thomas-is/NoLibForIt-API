#!/bin/bash

docker run --rm -it \
  -p 8888:80 \
  -e API_BASE_URL="/" \
  -e API_ALLOW_ORIGIN="true" \
  -e API_MAP_FILE="/srv/api_map.json" \
  -e FPM_CLEAR_ENV="no" \
  -v $(pwd):/srv \
  0lfi/ng-php8
