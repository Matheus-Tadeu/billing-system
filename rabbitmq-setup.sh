#!/bin/bash

rabbitmqctl add_vhost /ms-import
rabbitmqctl set_permissions -p /ms-import guest ".*" ".*" ".*"