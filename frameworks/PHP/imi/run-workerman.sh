#!/bin/bash
service redis-server start && \
php vendor/bin/imi-workerman workerman/start
