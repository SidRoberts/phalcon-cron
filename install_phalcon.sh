#!/usr/bin/env bash

git clone -q --depth=1 https://github.com/phalcon/cphalcon.git -b phalcon-v$1

cd cphalcon/ext/

export CFLAGS="-g3 -O1 -std=gnu90 -Wall -DZEPHIR_RELEASE=1"; phpize && ./configure --enable-phalcon && make --silent -j4 && make --silent install && phpenv config-add ../unit-tests/ci/phalcon.ini