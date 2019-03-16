#!/bin/sh
set -eo pipefail

if [ $# -eq 0 ]; then
    echo "TAG is not defined. Usage: ./update_images.sh <TAG>"
    exit 1
fi

sudo CARONAE_ENV_TAG=$1 su

echo "Updating caronae-docker..."
cd /var/caronae/caronae-docker
git fetch origin master
git reset --hard origin/master

echo "Updating images using the tag $CARONAE_ENV_TAG"
/usr/local/bin/docker-compose pull caronae-ufrj-authentication
/usr/local/bin/docker-compose stop caronae-ufrj-authentication
/usr/local/bin/docker-compose rm -f caronae-ufrj-authentication
/usr/local/bin/docker-compose up -d

echo "Clean-up unused Docker images..."
docker image prune -af
