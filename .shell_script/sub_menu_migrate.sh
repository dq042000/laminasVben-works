#!/bin/bash

# 子選單： Migrate
SubMenuMigrate () {
    ##
    # for php >= 8.0 (php < 8.0 migrations_paths 請留空)
    #
    # 請參考 config/autoload/doctrine.local.php 搜尋: migrations_paths 的設置名稱
    # ex.
    # 'migrations_paths' => [
    #    'Application' => 'data/DoctrineORMModule/Migrations',
    # ],
    # 取得 `Application`
    ##
    migrations_paths='Application\'
    ReadEnv # 讀取「.env」

    # 開始執行
    echo $COLOR_YELLOW "(1) 執行 workbench export + migrate" $COLOR_REST;
    echo $COLOR_YELLOW "(2) 執行 workbench export" $COLOR_REST;
    echo $COLOR_YELLOW "(3) 產生 migrate 檔案" $COLOR_REST;
    echo $COLOR_YELLOW "(4) 執行 migrate" $COLOR_REST;
    echo $COLOR_YELLOW "(5) 還原 migrate" $COLOR_REST;
    echo $COLOR_YELLOW "(B) 回到主選單" $COLOR_REST;
    read -p "請輸入要執行的項目($(tput setaf 2 )1-5$(tput sgr0)):" migrate_select
    migrate_select_uppercase=$(echo "$migrate_select" | tr '[:upper:]' '[:lower:]')   # 轉換為小寫

    # (1) 執行 workbench export + migrate
    if [ $migrate_select_uppercase = 1 ]; then
        read -p "$(echo $COLOR_GREEN"確定要執行嗎？(yes/no)"$COLOR_REST"["$COLOR_YELLOW"yes"$COLOR_REST"]")" user_confirm
        user_confirm=${user_confirm:-yes}   # 預設為 yes
        user_confirm_uppercase=$(echo "$user_confirm" | tr '[:upper:]' '[:lower:]')   # 轉換為小寫

        # yes 就執行
        if [ "$user_confirm_uppercase" = 'yes' ]; then
            rm -f ${dir}/web/${PHP_DIRECTORY}/data/temp/*
            docker exec -ti ${containerNamePrefix}_api_1 sh bin/export.sh ${MIGRATION_FILE}
            cp ${dir}/web/${PHP_DIRECTORY}/data/temp/*.php ${dir}/web/${PHP_DIRECTORY}/module/Base/src/Entity/
            docker exec -ti ${containerNamePrefix}_api_1 sh bin/doctrine.sh migrations:diff
            docker exec -ti ${containerNamePrefix}_api_1 sh bin/doctrine.sh migrations:migrate --no-interaction
            rm -f ${dir}/web/${PHP_DIRECTORY}/data/temp/*
        fi
        echo "$COLOR_BACKGROUND_YELLOW Migrate... 成功 $COLOR_REST"

    # (2) 執行 workbench export
    elif [ $migrate_select_uppercase = 2 ]; then
        read -p "$(echo $COLOR_GREEN"確定要執行嗎？(yes/no)"$COLOR_REST"["$COLOR_YELLOW"yes"$COLOR_REST"]")" user_confirm
        user_confirm=${user_confirm:-yes}   # 預設為 yes

        user_confirm_uppercase=$(echo "$user_confirm" | tr '[:upper:]' '[:lower:]')   # 轉換為小寫

        # yes 就執行
        if [ "$user_confirm_uppercase" = 'yes' ]; then
            rm -f ${dir}/web/${PHP_DIRECTORY}/data/temp/*
            docker exec -ti ${containerNamePrefix}_api_1 sh bin/export.sh ${MIGRATION_FILE}
        fi
        echo "$COLOR_BACKGROUND_YELLOW Migrate... 成功 $COLOR_REST"

    # (3) 產生 migrate 檔案
    elif [ $migrate_select_uppercase = 3 ]; then
        read -p "$(echo $COLOR_GREEN"確定要執行嗎？(yes/no)"$COLOR_REST"["$COLOR_YELLOW"yes"$COLOR_REST"]")" user_confirm
        user_confirm=${user_confirm:-yes}   # 預設為 yes

        user_confirm_uppercase=$(echo "$user_confirm" | tr '[:upper:]' '[:lower:]')   # 轉換為小寫

        # yes 就執行
        if [ "$user_confirm_uppercase" = 'yes' ]; then
            cp ${dir}/web/${PHP_DIRECTORY}/data/temp/*.php ${dir}/web/${PHP_DIRECTORY}/module/Base/src/Entity/
            docker exec -ti ${containerNamePrefix}_api_1 sh bin/doctrine.sh migrations:diff
        fi
        echo "$COLOR_BACKGROUND_YELLOW Migrate... 成功 $COLOR_REST"

    # (4) 執行 migrate
    elif [ $migrate_select_uppercase = 4 ]; then
        read -p "$(echo "請輸入要"$COLOR_YELLOW"migrate"$COLOR_REST"的版本號碼["$COLOR_YELLOW"ex.Version20221202033436"$COLOR_REST"]"):" version_number
        read -p "$(echo $COLOR_GREEN"確定要 migrate 嗎？(yes/no)"$COLOR_REST"["$COLOR_YELLOW"yes"$COLOR_REST"]")" user_answer
        user_answer=${user_answer:-yes}   # 預設為 yes
        user_confirm_uppercase=$(echo "$user_answer" | tr '[:upper:]' '[:lower:]')   # 轉換為小寫

        # yes 就執行
        if [ "$user_confirm_uppercase" = 'yes' ]; then
            # docker exec -ti ${containerNamePrefix}_api_1 bin/doctrine.sh migrations:migrate "${migrations_paths}${version_number}"
            # or 
            docker exec -ti ${containerNamePrefix}_api_1 bin/doctrine.sh migrations:execute "${migrations_paths}${version_number}" --up
        fi
        echo "$COLOR_BACKGROUND_YELLOW Migrate... 成功 $COLOR_REST"

    # (5) 還原 migrate
    elif [ $migrate_select_uppercase = 5 ]; then
        read -p "$(echo "請輸入要"$COLOR_RED"還原"$COLOR_REST"的版本號碼["$COLOR_YELLOW"ex.Version20221202033436"$COLOR_REST"]"):" version_number
        if [ -z "$version_number" ]; then
            echo "$COLOR_RED 請輸入版本號碼 $COLOR_REST"
        else
            read -p "$(echo $COLOR_GREEN"確定要"$COLOR_REST$COLOR_RED"還原"$COLOR_REST $COLOR_GREEN"嗎？(yes/no)"$COLOR_REST"["$COLOR_YELLOW"yes"$COLOR_REST"]")" user_answer
            user_answer=${user_answer:-yes}   # 預設為 yes
            user_answer_uppercase=$(echo "$user_answer" | tr '[:upper:]' '[:lower:]')   # 轉換為小寫

            # yes 就執行
            if [ "$user_answer_uppercase" = 'yes' ]; then
                docker exec -ti ${containerNamePrefix}_api_1 bin/doctrine.sh migrations:execute "${migrations_paths}${version_number}" --down
            fi
        fi
        echo "$COLOR_BACKGROUND_YELLOW Migrate... 成功 $COLOR_REST"

    # (B) 回主選單
    elif [ "$migrate_select_uppercase" = 'b' ]; then
        clear
        MainMenu    # 主選單

        return 0

    else
        clear
        echo $COLOR_BACKGROUND_RED"請輸入要執行的指令..."$COLOR_REST
        SubMenuMigrate  # Migrate

        return 0
    fi
}