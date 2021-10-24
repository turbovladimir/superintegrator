#!/usr/bin/env bash

function up() {
  echo "Prepare to build container with app and nginx"

  if docker ps | grep portainer; then
    echo "portainer already init"
  else
    echo 'portainer start'
    docker volume create portainer_data
    docker run -d -p 8000:8000 -p 9000:9000 --name=portainer --restart=always -v /var/run/docker.sock:/var/run/docker.sock -v portainer_data:/data portainer/portainer
  fi

  echo "Checking super_network..."
  if docker network ls | grep super_network; then
    echo "super_network exist"
  else
    echo 'super_network start'
    docker network create --subnet=172.20.0.0/16 super_network
  fi

  cd ../
  APP_PATH=$(pwd)
  cd docker/
  echo "Starting container with app and nginx"
  composeEnvFile
  docker-compose -f app.yml up -d
  docker-compose -f nginx.yml up -d
  docker-compose -f mysql.yml up -d
  rm -rf .env
}

function setEnv() {
  question=$1
  answerRegExp=$2
  envName=$3
  envDefaultValue=$4

  read -r -p "$question" response
  if [[ "$response" =~ $answerRegExp ]]; then
    eval "$envName"="$response"
  else
    eval "$envName"="$envDefaultValue"
  fi

  echo "Set var $envName: ${!envName}"
}

function composeEnvFile() {
  touch .env
  echo "HOST=$HOST" >>.env
  echo "XDEBUG_HOST=$XDEBUG_HOST" >>.env
  echo "XDEBUG_PORT=$XDEBUG_PORT" >>.env
  echo "APP_PATH=$APP_PATH" >>.env
  echo "-------------"
  cat .env
  echo "-------------"
  source .env
}

function down() {
  if docker ps | grep -E "(c-nginx-super|c-superintegrator|c-mysql-super)"; then
    echo "Down container with app and nginx"
    docker-compose -f app.yml down
    docker-compose -f nginx.yml down
    docker-compose -f mysql.yml down
  fi
}

function main() {
  setEnv 'Choose what you want? [up/down]' '^down$' OPERATION up

  if [[ "$OPERATION" == "down" ]]; then
    down
  else
    up
  fi
}

main
exit
