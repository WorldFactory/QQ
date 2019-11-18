#!/bin/bash

stty -echo

php ./vendor/worldfactory/qq/bin/console $@

status=$?

stty echo

[[ ${status} -eq 0 ]] && exit ${status}
