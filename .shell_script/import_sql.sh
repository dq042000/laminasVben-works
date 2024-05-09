#!/bin/bash

# 匯入 SQL 檔案
ImportSql () {
    ReadEnv # 讀取「.env」

    # 存放SQL位置
    dirSQL=${dir}/web/${PHP_DIRECTORY}/data/sql
    for fileLevelOne in "$dirSQL"/*
    do
        for fileLevelTwo in "$fileLevelOne"
        do
            # 取得檔案名稱
            fileLevelTwoName=$(basename "${fileLevelTwo}")

            # 只允許副檔名為「.sql」
            if [ "${fileLevelTwoName##*.}" = "sql" ]; then
                env LANG=zh_TW.UTF-8 cat $fileLevelTwo | docker exec -i ${containerNamePrefix}_mysqldb_1 mariadb -u${MYSQL_ROOT_USER} -p${MYSQL_ROOT_PASSWORD} -h${MYSQL_HOST} ${MYSQL_DATABASE}
            fi
        done
    done

    echo "$COLOR_BACKGROUND_YELLOW 資料匯入... 成功 $COLOR_REST"
}