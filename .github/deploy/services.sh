#!/usr/bin/env bash
set -euo pipefail

# Installed root-owned. No service names or commands come from the caller.
case "${1:-}" in
    stop)
        /usr/bin/supervisorctl stop jbr-queue jbr-ssr
        ;;
    start)
        /usr/bin/systemctl reload php8.5-fpm
        /usr/bin/supervisorctl start jbr-queue jbr-ssr
        ;;
    *)
        echo 'Expected stop or start.' >&2
        exit 1
        ;;
esac
