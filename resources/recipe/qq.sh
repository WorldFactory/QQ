#!/bin/bash

stty -echo

php ./vendor/worldfactory/qq/bin/console $@

stty echo
