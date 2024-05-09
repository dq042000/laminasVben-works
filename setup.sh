#!/bin/bash

#####################################################################
# 一旦任何命令返回非零的退出狀態，腳本將立即終止執行，而不會繼續執行後續命令
set -e

#####################################################################
# Color https://blog.csdn.net/qq_42372031/article/details/104137272
# 文字顏色
COLOR_RED=$(tput setaf 1);
COLOR_GREEN=$(tput setaf 2);
COLOR_YELLOW=$(tput setaf 3);
COLOR_BLUE=$(tput setaf 4);
COLOR_REST=$(tput sgr0); # No Color

# 背景顏色
COLOR_BACKGROUND_RED=$(tput setab 1);
COLOR_BACKGROUND_GREEN=$(tput setab 2);
COLOR_BACKGROUND_YELLOW=$(tput setab 3);
COLOR_BACKGROUND_BLUE_GREEN=$(tput setab 6); # 青色
COLOR_BACKGROUND_WHITE=$(tput setab 7);

RemoveContainer () {
    lastResult=$?
    if [ $lastResult = 16888 ]; then
        echo "$COLOR_BACKGROUND_RED 狀態:$lastResult，中止... $COLOR_REST"
        docker-compose down
    fi
}
trap RemoveContainer EXIT

#####################################################################
# 先清空畫面
clear

#####################################################################
# 取得資料夾名稱，因資料夾名稱是容器名稱的 prefix
dir=$(pwd)
fullPath="${dir%/}";
containerNamePrefix=${fullPath##*/}
echo "$COLOR_BACKGROUND_BLUE_GREEN 現在位置 - ${containerNamePrefix} $COLOR_REST"

#####################################################################
# 先檢查網段是否存在，如果不存在，則建立網段
networkName=${containerNamePrefix}_network
if [ -z "$(docker network ls | grep $networkName)" ]; then
    docker network create $networkName && echo "$COLOR_BACKGROUND_GREEN 建立網段... 成功 $COLOR_REST"
fi

#####################################################################
# 引入模組
. ${dir}/.shell_script/init.sh               # 初始化
. ${dir}/.shell_script/read_env.sh           # 讀取「.env」
. ${dir}/.shell_script/default_setting.sh    # 預設設定
. ${dir}/.shell_script/main_menu.sh          # 主選單

#####################################################################
# 開始執行
MainMenu