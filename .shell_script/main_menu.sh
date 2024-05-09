#!/bin/bash

# 主選單
MainMenu () {
    # 檢查 .env 檔案是否存在
    if [ -f .env ]; then
        ReadEnv # 讀取「.env」
    fi

    echo $COLOR_YELLOW"======= 選單 ======================================================================="$COLOR_REST;
    echo $COLOR_YELLOW"|    (1) 專案初始化 + 啟動開發環境                                                 |"$COLOR_REST;
    echo $COLOR_YELLOW"|    (2) 啟動開發環境                                                              |"$COLOR_REST;
    echo $COLOR_YELLOW"|    (3) 模擬啟動正式環境                                                          |"$COLOR_REST;
    echo $COLOR_YELLOW"|    (4) 匯入資料庫 $COLOR_RED( 確保匯入前將資料庫清空及匯入檔案放置: $COLOR_GREEN./web/${PHP_DIRECTORY}/data/sql $COLOR_RED) $COLOR_YELLOW  |"$COLOR_REST;
    echo $COLOR_YELLOW"|    (5) 執行 Migrate                                                              |"$COLOR_REST;
    echo $COLOR_YELLOW"|    (6) 查看 CLI 指令                                                             |"$COLOR_REST;
    echo $COLOR_YELLOW"|    (7) 查看 Composer 指令                                                        |"$COLOR_REST;
    echo $COLOR_YELLOW"|    (8) 更新 Composer 套件                                                        |"$COLOR_REST;
    echo $COLOR_YELLOW"|    (9) 更新 Node 套件                                                            |"$COLOR_REST;
    echo $COLOR_YELLOW"|    (10) 停用 Supervisord                                                         |"$COLOR_REST;
    echo $COLOR_YELLOW"|    (11) 重啟 Supervisord                                                         |"$COLOR_REST;
    echo $COLOR_YELLOW"|    (Q) 離開                                                                      |"$COLOR_REST;
    echo $COLOR_YELLOW"===================================================================================="$COLOR_REST;
    read -p "請輸入要執行的項目($(tput setaf 2 )1-11$(tput sgr0))[$(tput setaf 3 )2$(tput sgr0)]:" -r user_select
    user_select=${user_select:-2}   # 預設為 2
    user_select_uppercase=$(echo "$user_select" | tr '[:upper:]' '[:lower:]')   # 轉換為小寫

    ########################################
    # 專案初始化 + 啟動開發環境
    if [ $user_select_uppercase = 1 ]; then
        # Run default setting
        DefaultSetting

        # Install php packages
        docker exec -it ${containerNamePrefix}_api_1 composer install && echo "$COLOR_BACKGROUND_GREEN 安裝 php 相關套件... 成功 $COLOR_REST"

        # Install node modules
        docker exec -it ${containerNamePrefix}_vue_1 yarn && echo "$COLOR_BACKGROUND_GREEN 安裝前端所需套件... 成功 $COLOR_REST"

        # Cache disabled
        docker exec -it ${containerNamePrefix}_api_1 composer development-enable && echo "$COLOR_BACKGROUND_GREEN 取消 Cache 功能... 成功 $COLOR_REST"

        # Change permission
        sudo chmod 777 -R web/${PHP_DIRECTORY}/data web/${PHP_DIRECTORY}/config web/${PHP_DIRECTORY}/module

        # Start container
        docker-compose down && echo "$COLOR_BACKGROUND_GREEN 停止容器... 成功 $COLOR_REST"
        docker-compose up -d --build && echo "$COLOR_BACKGROUND_GREEN 啟動容器... 成功 $COLOR_REST"
        docker exec -it ${containerNamePrefix}_api_1 bin/cli.sh base:install && echo "$COLOR_BACKGROUND_GREEN 安裝 DB... 成功 $COLOR_REST"

        # Start develop
        # docker exec -it ${containerNamePrefix}_vue_1 yarn local
        # docker exec -it ${containerNamePrefix}_vue_1 yarn dev

        return 16888

    ########################################
    # 啟動開發環境
    elif [ $user_select_uppercase = 2 ]; then
        # Start container
        docker-compose up -d --build
        echo "$COLOR_BACKGROUND_GREEN 啟動容器... 成功 $COLOR_REST"

        # Update php packages
        docker exec -it ${containerNamePrefix}_api_1 composer install && echo "$COLOR_BACKGROUND_GREEN 更新 php 相關套件... 成功 $COLOR_REST"

        # Update node modules
        docker exec -it ${containerNamePrefix}_vue_1 pnpm install && echo "$COLOR_BACKGROUND_GREEN 更新前端所需套件... 成功 $COLOR_REST"

        # Start develop
        # docker exec -it ${containerNamePrefix}_vue_1 yarn local
        docker exec -it ${containerNamePrefix}_vue_1 pnpm serve

        # return 16888
        return 0

    ########################################
    # 模擬啟動正式環境
    elif [ $user_select_uppercase = 3 ]; then
        # Run docker
        docker-compose up -d --build

        # Update php packages
        docker exec -it ${containerNamePrefix}_api_1 composer install && echo "$COLOR_BACKGROUND_GREEN 更新 php 相關套件... 成功 $COLOR_REST"

        # Update node modules
        docker exec -it ${containerNamePrefix}_vue_1 yarn && echo "$COLOR_BACKGROUND_GREEN 更新前端所需套件... 成功 $COLOR_REST"

        # Start build
        docker exec -it ${containerNamePrefix}_vue_1 yarn build

        return 0

    ########################################
    # 匯入資料庫
    elif [ $user_select_uppercase = 4 ]; then
        . ./.shell_script/import_sql.sh   # 匯入 SQL 檔案
        ImportSql

        return 0

    ########################################
    # Migrate
    elif [ $user_select_uppercase = 5 ]; then
        . ./.shell_script/sub_menu_migrate.sh   # 子選單： Migrate
        SubMenuMigrate

        return 0

    ########################################
    # 查看 CLI 指令
    elif [ $user_select_uppercase = 6 ]; then
        . ./.shell_script/sub_menu_cli.sh
        CliLists

        return 0

    ########################################
    # 查看 Compose 指令
    elif [ $user_select_uppercase = 7 ]; then
        . ./.shell_script/sub_menu_composer.sh
        CliLists

        return 0

    ########################################
    # 更新 Compose 套件
    elif [ $user_select_uppercase = 8 ]; then
        # Update php packages
        docker exec -it ${containerNamePrefix}_api_1 composer update && echo "$COLOR_BACKGROUND_GREEN 更新 php 相關套件... 成功 $COLOR_REST"

        return 0

    ########################################
    # 更新 Node 套件
    elif [ $user_select_uppercase = 9 ]; then
        # Update node modules
        docker exec -it ${containerNamePrefix}_vue_1 yarn && echo "$COLOR_BACKGROUND_GREEN 更新前端所需套件... 成功 $COLOR_REST"

        return 0

    ########################################
    # 停用 Supervisord
    elif [ $user_select_uppercase = 10 ]; then
        docker exec -it ${containerNamePrefix}_api_1 /usr/bin/supervisorctl stop "slm-queue:*"

        return 0 

    ########################################
    # 重啟 Supervisord
    elif [ $user_select_uppercase = 11 ]; then
        docker exec -it ${containerNamePrefix}_api_1 /bin/supervisorctl restart "slm-queue:*"

        return 0 

    ########################################
    # 離開
    elif [ "$user_select_uppercase" = 'q' ]; then
        clear

        return 0

    else
        clear
        echo $COLOR_BACKGROUND_RED "請輸入要執行的項目..." $COLOR_REST;
        MainMenu

        return 0
    fi
}