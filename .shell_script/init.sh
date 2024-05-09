#!/bin/bash

# 初始化
Init () {
    echo "$COLOR_BACKGROUND_BLUE_GREEN 切換分支為 develop $COLOR_REST"

    # Copy config files
    cp env-sample .env
    cp docker-compose.yml.sample docker-compose.yml
    cp .docker/nginx/default.conf.dist .docker/nginx/default.conf
    echo "$COLOR_BACKGROUND_YELLOW 準備啟動檔案... 成功 $COLOR_REST"
}