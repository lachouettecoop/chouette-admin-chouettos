#!/bin/bash

set -e
cd `dirname $0`

function container_full_name() {
    # Retourne le nom complet du coneneur $1 si il est en cours d'exécution
    # workaround for docker-compose ps: https://github.com/docker/compose/issues/1513
    ids=$(docker-compose ps -q)
    if [ "$ids" != "" ] ; then
        echo `docker inspect -f '{{if .State.Running}}{{.Name}}{{end}}' $ids \
              | cut -d/ -f2 | grep -E "_${1}_[0-9]"`
    fi
}

function dc_dockerfiles_images() {
    # Retourne la liste d'images Docker depuis les Dockerfile build listés dans docker-compose.yml
    local DOCKERDIRS=`grep -E '^\s*build:' docker-compose.yml|cut -d: -f2 |xargs`
    local dockerdir
    for dockerdir in $DOCKERDIRS; do
        echo `grep "^FROM " ${dockerdir}/Dockerfile |cut -d' ' -f2|xargs`
    done
}

function dc_exec_or_run() {
    # Lance la commande $2 dans le container $1, avec 'exec' ou 'run' selon si le conteneur est déjà lancé ou non
    local CONTAINER_SHORT_NAME=$1
    local CONTAINER_FULL_NAME=`container_full_name ${CONTAINER_SHORT_NAME}`
    shift
    if test -n "$CONTAINER_FULL_NAME" ; then
        # container already started
        docker exec -it $CONTAINER_FULL_NAME "$@"
    else
        # container not started
        docker-compose run --rm $CONTAINER_SHORT_NAME "$@"
    fi
}

case $1 in
    "")
        docker-compose up -d
        ;;

    init)
        test -e docker-compose.yml || cp docker-compose.yml.dist docker-compose.yml
        ;;

    upgrade)
        read -rp "Êtes-vous sûr de vouloir effacer et mettre à jour les images et conteneurs Docker ? (o/n) "
        if [[ $REPLY =~ ^[oO]$ ]] ; then
            docker-compose pull
            for image in `dc_dockerfiles_images`; do
                docker pull $image
            done
            docker-compose build
            docker-compose stop
            docker-compose rm -f
        fi
        ;;

    bash)
        dc_exec_or_run php "$@"
        ;;

    mysql|mysqldump)
        cmd=$1
        shift
        if [ "$cmd" = "mysql" ] ; then
            # check if input file descriptor (0) is a terminal
            if [ -t 0 ] ; then
                option="-it";
            else
                option="-i";
            fi
        else
            option="";
        fi
        MYSQL_CONTAINER=`container_full_name db`
        MYSQL_USER=`grep MYSQL_USER docker-compose.yml|cut -d: -f2|xargs`
        MYSQL_PASSWORD=`grep MYSQL_PASSWORD docker-compose.yml|cut -d: -f2|xargs`
        MYSQL_DATABASE=`grep MYSQL_DATABASE docker-compose.yml|cut -d: -f2|xargs`
        docker exec $option $MYSQL_CONTAINER $cmd --user="$MYSQL_USER" --password="$MYSQL_PASSWORD" "$MYSQL_DATABASE" "$@"
        ;;

    dumpall)
        shift
        MYSQL_CONTAINER=`container_full_name db`
        MYSQL_PASSWORD=`grep MYSQL_ROOT_PASSWORD docker-compose.yml|cut -d: -f2|xargs`
        docker exec $MYSQL_CONTAINER mysqldump --user=root --password="$MYSQL_PASSWORD" --all-databases --events "$@"
        ;;

    restoreall)
        shift
        MYSQL_CONTAINER=`container_full_name db`
        MYSQL_PASSWORD=`grep MYSQL_ROOT_PASSWORD docker-compose.yml|cut -d: -f2|xargs`
        docker exec -i $MYSQL_CONTAINER mysql --user=root --password="$MYSQL_PASSWORD" "$@"
        ;;

    build|config|create|down|events|exec|kill|logs|pause|port|ps|pull|restart|rm|run|start|stop|unpause|up)
        docker-compose "$@"
        ;;

    *)
        cat <<HELP
Utilisation : $0 [COMMANDE]
  init         : initialise les données
               : lance les conteneurs
  upgrade      : met à jour les images et les conteneurs Docker
  bash         : lance bash sur le conteneur php
  mysql        : lance mysql sur le conteneur db
  mysqldump    : lance mysqldump redmine sur le conteneur db
  dumpall      : lance mysqldump --all-databases --events
  restoreall   : permet de restaure le contenu d'un dumpall
  stop         : stoppe les conteneurs
  rm           : efface les conteneurs
  logs         : affiche les logs des conteneurs
HELP
        ;;

esac

