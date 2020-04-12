<?php
return array(
     'DATA_CACHE_PREFIX' => '',//缓存前缀
    'DATA_CACHE_TYPE'=>'Redis',//默认动态缓存为Redis
    'REDIS_RW_SEPARATE' => false, //Redis读写分离 true 开启
    'REDIS_HOST'=>'127.0.0.1', //redis服务器ip，多台用逗号隔开；读写分离开启时，第一台负责写，其它[随机]负责读；
    'REDIS_PORT'=>'6379',//端口号
    'REDIS_TIMEOUT'=>'300',//超时时间
    'REDIS_PERSISTENT'=>false,//是否长连接 false=短连接
    'REDIS_AUTH'=>'',//AUTH认证密码 
     
    
    'DEFAULT_MODULE'        =>  'Home',  // 默认模块
    'DEFAULT_CONTROLLER'    =>  'Notice', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'main', // 默认操作名称
    
    'DB_TYPE' => 'mysql',     // 数据库类型
    'DB_HOST' => 'localhost', // 服务器地址
    'DB_NAME' => 'community_app',          // 数据库名
    'DB_USER' => 'root',      // 用户名
    'DB_PWD' => 'root',           // 密码
    'DB_PREFIX' => 'u_', // 数据库表前缀
    'DB_CHARSET'=> 'utf8', // 字符集
    'SERVICE_SITE' => 'http://localhost:8011/community_address_book_web/' ,
    
    
    'CORE_SERVICE_URL' => 'http://localhost:8081/',
    
    'ADMIN_USER_OPEN' => array('','',''), 
    
    'PAGE_LIMIT' => 15 ,
    
    'APP_ID' => '' ,
    "APP_SECRITE" => '',
    //支付相关
    'MCH_ID' => '',
    'MCH_KEY' => '',
    
    //订阅消息的列表
    "RESPONSE_TEMP_IDS" => array('YLxzYe_cjVEmLvmT3iqCaLt9f3DQDHOCC6BIq-Rn4Zg','wgrhwtONPQUnoYsMaIpwIcEbKOXGf_HJ-FX2lqgfAz4'
    ,'MfSkY7an1q5QoDnhL1loTuiWGZRvnOuUPMGRFfwHrHQ','nlxY_fvSbx_wFRW8PcZS3wlp1qviCK6-jh4Dcby0ABA'),
    
    //腾讯云cos配置
    "COS_SECRETID"=>"" ,
    "COS_SECRETKEY"=>"",
    "COS_BUCKET"=>'',
    "COS_ACCESS_BASE_URL" => '', //腾讯云存储的默认的url
    
    
    //超级VIP年价
    'VIP_PRICE' => '99',
    
    /**
     * 免校验报名单
     */
    'NEED_SESSION_ACTION_LIST' => array(
        'checkToken','getWXPhone','updateUserInfo','updateUserHouseBind','addUserHouse','getUserInfoById','getUserInfoByToken','hasHouse'
        ,'addWorker','addWorkerComment','addTrend','delTrend','addTrendComment','addReport','changeUserHouse',"getAboutContactList","addCalendar"
        ,"geCalendartList","getHouseFriendAllList","getMessageList","getUserTicketList","addUserTicket","getListWithSession",'orderGood','doPay','addFromId'
        ,'getUserOrderList','getVIPInfo','addUserOrderLocation','getUserOrderLocationList','getUserOrderDefaultLocation',"getGoodSharePic"
        ,'getContactListByHouseAndCate','getUserWalletList','addBusinessComment','addGood','getUserGoodList','getMySaledOrderList','addWords','getWordsListWithToUser'
    ),
    'UPLOAD_PATH_ROOT' => './Uploads/' ,
    
    "REPORT_REASON_LIST" => array(
        '广告电话' ,'电话无效','价格贵','服务差','其他原因'
    )
    
);