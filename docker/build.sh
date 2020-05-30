#!/usr/bin/env bash

read -r -p "What are you want to do with docker env?[up/down]" response
if [[ "$response" =~ ^down$ ]]
then
    if docker ps | grep -E "(c-nginx-super|c-superintegrator)"
    then
        echo "Down container with app and nginx"
        docker-compose -f app.yml down
        docker-compose -f nginx.yml down
    fi

    exit
fi

echo "Prepare to build container with app and nginx"

if  docker ps | grep portainer
then
    echo "portainer already init"
else
    echo 'portainer start'
    docker volume create portainer_data
    docker run -d -p 8000:8000 -p 9000:9000 --name=portainer --restart=always -v /var/run/docker.sock:/var/run/docker.sock -v portainer_data:/data portainer/portainer
fi

echo "Checking super_network..."
if  docker network ls | grep super_network
then
    echo "super_network exist"
else
    echo 'super_network start'
    docker network create --subnet=172.20.0.0/16 super_network
fi

read -r -p "Enter your domain (default is superintegrator.loc)" response
if [[ "$response" =~ ^.+$ ]]
then
    DOMAIN="$response"
else
    DOMAIN="superintegrator.loc"
fi

echo "Enter host (ex. 10.8.0.0):"
read HOST
read -r -p "Is xdebug host same like host?[y/N]" response
if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]
then
    XDEBUG_HOST="$HOST"
else
    echo "Enter xdebug_host (ex. 10.8.0.0):"
    read XDEBUG_HOST
fi

read -r -p "Enter xdebug port (default 9009)" response
if [[ "$response" =~ ^.+$ ]]
then
    XDEBUG_PORT="$response"
else
    XDEBUG_PORT=9009
fi

cd ../
APP_PATH=$(pwd)
cd docker/
read -r -p  "Is your local path to app $APP_PATH? [y/N]" response
if [[ "$response" =~ ^([nN])$ ]]
then
    echo "Enter local path: "
    read APP_PATH
fi


touch .env
echo "HOST=$HOST" >> .env
echo "XDEBUG_HOST=$XDEBUG_HOST" >> .env
echo "DOMAIN=$DOMAIN" >> .env
echo "XDEBUG_PORT=$XDEBUG_PORT" >> .env
echo "APP_PATH=$APP_PATH" >> .env
echo "-------------"
cat .env
echo "-------------"
source .env
sed -i 's/__DOMAIN__/'$DOMAIN'/g' nginx/conf.d/*

echo "Starting container with app and nginx"
docker-compose -f app.yml up -d
docker-compose -f nginx.yml up -d
sed -i 's/'$DOMAIN'/__DOMAIN__/g' nginx/conf.d/*
rm .env