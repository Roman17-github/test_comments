#!/bin/bash
set -e

# Volume mount keeps host ownership; Apache and the host user both need write access.
if [ -d /var/www/html/writable ]; then
  chmod -R a+rwX /var/www/html/writable
fi

exec docker-php-entrypoint "$@"
