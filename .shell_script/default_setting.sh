#!/bin/bash

# 預設設定
DefaultSetting () {
    Init # 初始化
    ReadEnv # 讀取「.env」

    # Copy php config files
    ln .env web/${PHP_DIRECTORY}/config/.env
    cp web/${PHP_DIRECTORY}/config/autoload/oauth.local.php.dist web/${PHP_DIRECTORY}/config/autoload/oauth.local.php
    cp web/${PHP_DIRECTORY}/config/autoload/doctrine.local.php.dist web/${PHP_DIRECTORY}/config/autoload/doctrine.local.php
    cp web/${PHP_DIRECTORY}/config/autoload/local.php.dist web/${PHP_DIRECTORY}/config/autoload/local.php
    cp web/${PHP_DIRECTORY}/config/autoload/module.doctrine-mongo-odm.local.php.dist web/${PHP_DIRECTORY}/config/autoload/module.doctrine-mongo-odm.local.php
    cp web/${PHP_DIRECTORY}/config/autoload/recaptcha.local.php.dist web/${PHP_DIRECTORY}/config/autoload/recaptcha.local.php
    cp web/${PHP_DIRECTORY}/config/autoload/memcached.local.php.dist web/${PHP_DIRECTORY}/config/autoload/memcached.local.php
    # cp web/${PHP_DIRECTORY}/data/admin.htpasswd.dist web/${PHP_DIRECTORY}/data/admin.htpasswd
    echo "$COLOR_BACKGROUND_YELLOW 複製 專案 Config 檔案... 成功 $COLOR_REST"

    # Start container
    docker-compose up -d --build
    echo "$COLOR_BACKGROUND_GREEN 啟動容器... 成功 $COLOR_REST"
}