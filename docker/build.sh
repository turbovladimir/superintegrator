#!/usr/bin/env bash

if  sudo docker ps | grep portainer
then
    echo "portainer already init"
else
    echo 'portainer start'
    sudo docker volume create portainer_data
    sudo docker run -d -p 8000:8000 -p 9000:9000 --name=portainer --restart=always -v /var/run/docker.sock:/var/run/docker.sock -v portainer_data:/data portainer/portainer
fi

echo "Enter your domain (default is dev.superintegrator.loc)"
read DOMAIN
echo "Enter xdebug server (ex. server):"
read XDEBUG_SERVER
echo "Enter xdebug host (ex. 10.8.0.0):"
read XDEBUG_HOST
echo "Enter xdebug port (ex. 9009):"
read XDEBUG_PORT
echo "Enter path to app (/var/www/superintegrator):"
read APP_PATH

[[ -f ./.env ]]&&{ echo "Remove old .env"; rm ./.env; }

echo "Creating new .env"
cp .env.example .env
sed -i 's/dev.superintegrator.loc/'$DOMAIN'/g' .env
sed -i 's/__DOMAIN__/'$DOMAIN'/g' ./images/nginx/config/superintegrator.conf
sed -i 's/server/'$XDEBUG_SERVER'/g' .env
sed -i 's/10.8.2.146/'$XDEBUG_HOST'/g' .env
sed -i 's/9009/'$XDEBUG_PORT'/g' .env
sed -i 's/\/var\/www\/superintegrator/'$APP_PATH'/g' .env
sed -i 's/__APP_PATH__/'$APP_PATH'/g' ./images/nginx/config/superintegrator.conf
echo "-------------"
cat .env
echo "-------------"
source .env

echo "Starting container with app and nginx"
sudo docker-compose -f ./images/php7-fpm/env.php7.yml build
sudo docker-compose -f app.yml up -d
sudo docker-compose -f nginx.yml up -d