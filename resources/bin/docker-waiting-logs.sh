#!/usr/bin/env bash

stty -echo

set -m

docker-compose logs --tail=0 -f | while read line;
do
    echo $line

    if  [[ $line =~ $1 ]]; then
        echo "Pattern found : $1"
        sh -c 'PGID=$( ps -o pgid= $$ | tr -d \  ); kill -TERM -$PGID'
    fi
done

stty echo
