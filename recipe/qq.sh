#!/bin/bash

stty -echo

php ./vendor/worldfactory/qq/bin/console $1 $2 $3 $4 $5 $6 $7 $8 $9

stty echo
