#!/bin/bash

# 查看 CLI 指令
CliLists () {
    docker exec -ti ${containerNamePrefix}_api_1 composer
    while true; do
        read -p "請輸入要執行的指令，或輸入 (B) 回到主選單：" cli_select

        cli_select_uppercase=$(echo "$cli_select" | tr '[:upper:]' '[:lower:]')   # 轉換為小寫

        if [ "$cli_select_uppercase" = "b" ]; then
            clear
            MainMenu
            break
        elif [ -n "$cli_select" ]; then
            docker exec -ti ${containerNamePrefix}_api_1 composer $cli_select
            echo "$COLOR_BACKGROUND_GREEN 執行... 成功 $COLOR_REST"
        else
            clear
            docker exec -ti ${containerNamePrefix}_api_1 composer
        fi
    done
}