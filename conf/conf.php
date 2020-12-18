<?php

namespace YeuolyShellConf;

const DB_INFO = [
    'DB_HOST' => '127.0.0.1',
    'DB_USER' => 'root',
    'DB_PASSWORD' => 'root',
    'DB_NAME' => '',
    'DB_TYPE' => 'mysql',
    'DB_TABLE_PREPEND' => '',
    'DB_COLUMN_PREPEND' => ''
];

const APP_INFO = [
    'APP_NAME' => '',
    'APP_LOCATION' => '',
    'APP_IP' => '',
    'APP_ADMINISTRATOR' => '',
    'SHOW_FRAMEWORK' => true
];

const APP_SECURITY = [
    [
        'username',
        'password'
    ]
];

const ALLOW_LIST = [
    'ORIGIN' => ''
    // [
    //     'danmuji.srmxy.cn',
    //     'api.ypm.srmxy.cn'
    // ]
];