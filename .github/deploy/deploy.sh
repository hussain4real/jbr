#!/usr/bin/env bash
set -Eeuo pipefail
umask 0027
export PATH=/usr/local/bin:/usr/bin:/bin

readonly app_root=/var/www/jbr
readonly repository=https://github.com/hussain4real/jbr.git
readonly revision=${1:-}
[[ "$revision" =~ ^[0-9a-f]{40}$ ]] || { echo 'Invalid commit SHA.' >&2; exit 1; }
[[ $(id -un) == jbr ]] || { echo 'Run as the jbr application user.' >&2; exit 1; }

exec 9>"$app_root/shared/deploy.lock"
flock -w 1200 9

main_revision() {
    git ls-remote "$repository" refs/heads/main | awk '{print $1}'
}

if [[ $(main_revision) != "$revision" ]]; then
    echo 'Refusing to deploy a commit that is no longer the main branch head.' >&2
    exit 1
fi

previous=$(readlink -f "$app_root/current")
release="$app_root/releases/$(date -u +%Y%m%dT%H%M%SZ)-${revision:0:12}"
readonly release
[[ "$previous" == "$app_root/releases/"* && -f "$previous/artisan" ]]
[[ -f "$app_root/shared/.env" && -d "$app_root/shared/storage" ]]
maintenance=false
services_stopped=false
activated=false

restore_previous_release() {
    local status=$?
    trap - ERR INT TERM
    set +e
    echo "Deployment failed (exit $status); restoring the previous application release." >&2
    if [[ "$activated" == true ]]; then
        sudo -n /usr/local/sbin/jbr-services stop
        ln -s "$previous" "$app_root/current.rollback"
        mv -Tf "$app_root/current.rollback" "$app_root/current"
        services_stopped=true
    fi
    if [[ "$services_stopped" == true ]]; then
        sudo -n /usr/local/sbin/jbr-services start
    fi
    if [[ "$maintenance" == true ]]; then
        /usr/bin/php "$previous/artisan" up --no-interaction
    fi
    echo 'Database migrations are not automatically reversed; migrations must remain compatible with the previous release.' >&2
    exit "${status:-1}"
}
trap restore_previous_release ERR
trap 'false' INT TERM

mkdir "$release"
curl --fail --silent --show-error --location --retry 3 --max-time 120 \
    "https://api.github.com/repos/hussain4real/jbr/tarball/$revision" \
    | tar -xz --strip-components=1 -C "$release"
cd "$release"
# The extracted storage tree contains only Git placeholders. Preserve live data.
mv storage storage.skeleton
ln -s "$app_root/shared/storage" storage
ln -s "$app_root/shared/.env" .env
mkdir -p bootstrap/cache

composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader
composer check-platform-reqs --no-dev
npm ci --include=dev --no-audit --no-fund
npm run build:ssr
test -s public/build/manifest.json
test -s bootstrap/ssr/app.js
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

# The application user belongs to www-data solely for web-readable release files.
chgrp -hR www-data "$release"
find "$release" -type d -exec chmod g+rx {} +
find "$release" -type f -exec chmod g+r {} +
printf '%s\n' "$revision" > REVISION

if [[ $(main_revision) != "$revision" ]]; then
    echo 'Main advanced during the build. Leaving the active release unchanged.' >&2
    exit 1
fi

maintenance=true
php "$previous/artisan" down --retry=30 --no-interaction
services_stopped=true
sudo -n /usr/local/sbin/jbr-services stop
php artisan migrate --force --no-interaction
ln -s "$release" "$app_root/current.next"
mv -Tf "$app_root/current.next" "$app_root/current"
activated=true
sudo -n /usr/local/sbin/jbr-services start
services_stopped=false
php artisan up --no-interaction
maintenance=false

for attempt in {1..15}; do
    if php artisan inertia:check-ssr --no-interaction; then
        break
    fi
    [[ "$attempt" -lt 15 ]] || false
    sleep 2
done
php .github/deploy/health-check.php
printf '%s\n' "$revision" > "$app_root/shared/deployed-revision"
trap - ERR INT TERM
echo "Deployment healthy: $revision"
