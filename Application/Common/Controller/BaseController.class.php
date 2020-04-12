<?php
namespace Common\Controller ;
use Home\Service\UserService;
use Home\Service\WXUserService;
use Think\Controller;
use Home\Service\CateService;
use Home\Service\HouseService;
use Home\Service\TrendService;
use Home\Service\WorkerService;
use Home\Service\WXContentCheckService;
use Home\Service\ReportService;
use Home\Service\GlobalContactService;
use Home\Service\NoticeService;
use Home\Service\WXMsgService;
use Think\Log;
use Qcloud\Cos\Client ;
class BaseController extends Controller {
    
    protected $wxUserService = null ;
    protected $userService = null ;
    protected $cateService = null ;
    protected $houseService = null;
    protected $trendService = null ;
    protected $workerService = null ;
    protected $checkService = null ;
    protected $reportService = null ;
    protected $globalContactService = null ;
    protected $noticeService = null ;
    protected $msgService = null; 
    protected function initService(){
        $this->wxUserService = new WXUserService(C("APP_ID"), C("APP_SECRITE")) ;
        $this->msgService = new WXMsgService(C("APP_ID"), C("APP_SECRITE")) ;
        $this->userService = new UserService() ;
        $this->cateService = new CateService() ;
        $this->houseService = new HouseService() ;
        $this->trendService = new TrendService() ;
        $this->workerService = new WorkerService() ;
        $this->checkService = new WXContentCheckService(C("APP_ID"), C("APP_SECRITE")) ;
        $this->reportService = new ReportService() ;
        $this->globalContactService = new GlobalContactService() ;
        $this->noticeService = new NoticeService() ;
    }
    public function __construct(){
        parent::__construct();
        
        $this->corsConfig() ;
        
        $this->initService() ;
        
        $this->checkToken() ;
        
        $this->getLoginUserID() ;
    }
    private function corsConfig() {
        //处理跨域问题
        header('Content-Type:application/json; charset=utf-8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Max-Age:1728000'); // 允许访问的有效期
        header('Access-Control-Allow-Headers:*');
        header('Access-Control-Allow-Methods:OPTIONS, GET, POST, DELETE');
    }
    /**
     * 检查会话状态，错误直接返回202
     */
    private function checkToken(){
        $token = $this->getToken() ;
        
        $black_list = C("NEED_SESSION_ACTION_LIST");        
        if(in_array(ACTION_NAME, $black_list)){ //说明需要token
            
            if(empty($token)){ // || empty(S($token))
                $this->returnJson(202,'','会话失效，请重新登陆') ;
            }else{
                //FIXME 原则此处需要校验session_key的有效性，暂不做处理                
                if(!S($token)){
                    $this->returnJson(206,'','会话失效，请重新登陆') ;
                }
            }
        }
    }
    protected function getToken(){
        $token = $_SERVER['HTTP_TOKEN'];
        return $token ;
    }
    protected function getLoginUserID(){
        $token = $this->getToken() ;
        $session_info = S($token);
        $user_info = $this->userService->getInfoByOpenId($session_info['openid']);
        return $user_info['u_id'];
    }
    
    protected function getLoginUserHoseId(){
        $house_info = S('house_house'.$this->getToken()) ;
        if(empty($house_info['u_id'])){
            return '';
        }
        return $house_info['u_id'] ;
    }
    
    protected function getLoginUserInfo(){
        $token = $this->getToken() ;
        $session_info = S($token);
        $user_info = $this->userService->getInfoByOpenId($session_info['openid']);
        return $user_info;
    }
    protected function returnSuccess($data,$msg='操作成功'){
        $this->ajaxReturn(array(
            'code' => 200 ,
            'data' => $data ,
            'msg' => $msg
        )) ;
    }
    protected function returnError($msg='操作失败'){
        $this->ajaxReturn(array(
            'code' => 400 ,
            'msg' => $msg
        )) ;
    }
    protected function returnJson($code ,$data,$msg='操作成功'){
        $this->ajaxReturn(array(
            'code' => $code ,
            'data' => $data ,
            'msg' => $msg
        )) ;
    }
    
    protected function jobGoodInfoPaper($goodInfo,$userInfo,$codePath){
        if(empty($goodInfo)){
            return false ;
        }
        try{
            include dirname(dirname(dirname(dirname(__FILE__)))).'/Application/Home/Utils/Qrcode/phpQrcode.class.php';
            include dirname(dirname(dirname(dirname(__FILE__)))).'/Application/Home/Utils/Qrcode/poster.class.php';
            $config = array(
                'bg_url' => dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/draw/bg.png',//背景图片路径
                'text' => array(
                    array(
                        'text' => $goodInfo['u_name'],//文本内容
                        'left' => 20, //左侧字体开始的位置
                        'top' => 745, //字体的下边框
                        'width' => 680,
                        'fontSize' => 17, //字号
                        'fontColor' => '0,0,0', //字体颜色
                        'angle' => 0,
                    ),
                    array(
                        'text' => '现价￥：'.$goodInfo['u_now_price'],
                        'left' => 20,
                        'top' => 820,
                        'width' => 400,
                        'fontSize' => 18, //字号
                        'fontColor' => '220,20,60', //字体颜色
                        'angle' => 0,
                    ),
                    array(
                        'text' => '原价￥：'.$goodInfo['u_past_price'],
                        'left' => 250,
                        'top' => 820,
                        'width' => 400,
                        'fontSize' => 17, //字号
                        'fontColor' => '169,169,169', //字体颜色
                        'angle' => 0,
                    ),
                    array(
                        'text' => $userInfo['u_nick_name'],
                        'left' => 150,
                        'top' => 890,
                        'width' => 400,
                        'fontSize' => 17, //字号
                        'fontColor' => '169,169,169', //字体颜色
                        'angle' => 0,
                    ),
                    array(
                        'text' => '给你分享的社区通信录好物',
                        'left' => 150,
                        'top' => 920,
                        'width' => 400,
                        'fontSize' => 17, //字号
                        'fontColor' => '169,169,169', //字体颜色
                        'angle' => 0,
                    ),
                    array(
                        'text' => '长按识别好物码，查看更多好物详情',
                        'left' => 220,
                        'top' => 1050,
                        'width' => 400,
                        'fontSize' => 17, //字号
                        'fontColor' => '169,169,169', //字体颜色
                        'angle' => 0,
                    ),
                    array(
                        'text' => '微信付款送货到家，放心购',
                        'left' => 220,
                        'top' => 1100,
                        'width' => 400,
                        'fontSize' => 17, //字号
                        'fontColor' => '169,169,169', //字体颜色
                        'angle' => 0,
                    ),
                    array(
                        'text' => '@皖礼来  版权所有',
                        'left' => 260,
                        'top' => 1250,
                        'width' => 400,
                        'fontSize' => 14, //字号
                        'fontColor' => '169,169,169', //字体颜色
                        'angle' => 0,
                    )
                ),
                'image' => array(
                    array(
                        'url' => '',
                        'stream' => file_get_contents($userInfo['u_icon']),
                        'left' => 50,
                        'top' => 860,
                        'right' => 0,
                        'bottom' => 0,
                        'width' => 80,
                        'height' => 80,
                        'radius' => 0,
                        'opacity' => 100
                    ),
                    array(
                        'url' => '',
                        'stream' => file_get_contents($codePath),
                        'left' => 50,
                        'top' => 1000,
                        'right' => 0,
                        'bottom' => 0,
                        'width' => 150,
                        'height' => 150,
                        'radius' => 0,
                        'opacity' => 100
                    ),
                    array(
                        'url' => '',
                        'stream' => file_get_contents($goodInfo['u_main_cover']),
                        'left' => 10,
                        'top' => 10,
                        'right' => 0,
                        'bottom' => 0,
                        'width' => 700,
                        'height' => 700,
                        'radius' => 0,
                        'opacity' => 100
                    ),
                    array(
                        'url' => dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/draw/lin-row.png',
                        'stream' => 0,
                        'left' => 250,
                        'top' => 805,
                        'right' => 0,
                        'bottom' => 0,
                        'width' => 200,
                        'height' => 15,
                        'radius' => 0,
                        'opacity' => 100
                    )
                )
            );
            //设置海报背景图
            \poster::setConfig($config);
            //设置保存路径
            $tmp = dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/goodInfo'.$goodInfo['u_id'].'.png' ;
            //设置保存路径
            \poster::make($tmp);
            
            
           $secretId = C("COS_SECRETID"); //"云 API 密钥 SecretId";
            $secretKey = C("COS_SECRETKEY"); //"云 API 密钥 SecretKey";
            $region = "ap-shanghai"; //设置一个默认的存储桶地域
            $cosClient = new Client(
                array(
                    'region' => $region,
                    'schema' => 'http', //协议头部，默认为http
                    'credentials'=> array(
                        'secretId'  => $secretId ,
                        'secretKey' => $secretKey)
                )
                );
            
            $tmp = str_replace('./Uploads', '/Uploads', $tmp) ;
            $file = fopen($tmp, "rb");
            if ($file) {
                $result = $cosClient->putObject(array(
                    'Bucket' => C('COS_BUCKET'),
                    'Key' => 'goodInfo'.$goodInfo['u_id'].'.png',
                    'Body' => $file));
                
                unlink($tmp) ;
                
                return 'https://'.$result['Location'] ;
            } 
            Log::record("生成图片参数".json_encode($goodInfo)."生成图片结果：".json_encode($tmp)) ;
        }catch (\Exception $e){
            Log::record("生成报错了：".$e->getMessage()) ;
        }
        return null ;
    }
    protected function jobWorkerCardPaper($workerInfo,$codePath){
        if(empty($workerInfo)){
            return false ;
        }
        include dirname(dirname(dirname(dirname(__FILE__)))).'/Application/Home/Utils/Qrcode/phpQrcode.class.php';
        include dirname(dirname(dirname(dirname(__FILE__)))).'/Application/Home/Utils/Qrcode/poster.class.php';
        try {
            $config = array(
                'bg_url' => dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/draw/bg1.png',//背景图片路径
                'text' => array(
                    array(
                        'text' => $workerInfo['u_name'],//文本内容
                        'left' => 282.5, //左侧字体开始的位置
                        'top' => 130, //字体的下边框
                        'fontSize' => 17, //字号
                        'fontColor' => '0,0,0', //字体颜色
                        'angle' => 0,
                    ),
                    array(
                        'text' => $workerInfo['u_cate_name'],
                        'left' => 282.5,
                        'top' => 175,
                        'width' => 400,
                        'fontSize' => 14, //字号
                        'fontColor' => '0,0,0', //字体颜色
                        'angle' => 0,
                    ),
                    array(
                        'text' => substr($workerInfo['u_phone'], 0,3).'****'.substr($workerInfo['u_phone'], -11,4),
                        'left' => 282.5,
                        'top' => 200,
                        'width' => 640,
                        'fontSize' => 14, //字号
                        'fontColor' => '0,0,0', //字体颜色
                        'angle' => 0,
                    ),
                    array(
                        'text' => '@皖礼来  版权所有',
                        'left' => 170,
                        'top' => 290,
                        'width' => 400,
                        'fontSize' => 14, //字号
                        'fontColor' => '169,169,169', //字体颜色
                        'angle' => 0,
                    )
                ),
                'image' => array(
                    array(
                        'url' => '',
                        'stream' => file_get_contents($codePath),
                        'left' => 97.5,
                        'top' => 93,
                        'right' => 0,
                        'bottom' => 0,
                        'width' => 120,
                        'height' => 120,
                        'radius' => 0,
                        'opacity' => 100
                    ),
                    array(
                        'url' => dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/draw/red-line.png',
                        'stream' => 0,
                        'left' => 254,
                        'top' => 100.5,
                        'right' => 0,
                        'bottom' => 0,
                        'width' => 1,
                        'height' => 110,
                        'radius' => 0,
                        'opacity' => 100
                    ),
                )
            );
            $tmp = dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/cricleInfo'.$workerInfo['u_id'].'.png' ;
            //设置海报背景图
            \poster::setConfig($config);
            //设置保存路径
            \poster::make($tmp);
            
            
            $secretId = C("COS_SECRETID"); //"云 API 密钥 SecretId";
            $secretKey = C("COS_SECRETKEY"); //"云 API 密钥 SecretKey";
            $region = "ap-shanghai"; //设置一个默认的存储桶地域
            $cosClient = new Client(
                array(
                    'region' => $region,
                    'schema' => 'http', //协议头部，默认为http
                    'credentials'=> array(
                        'secretId'  => $secretId ,
                        'secretKey' => $secretKey)
                )
                );
            
            $tmp = str_replace('./Uploads', '/Uploads', $tmp) ;
            $file = fopen($tmp, "rb");
            if ($file) {
                $result = $cosClient->putObject(array(
                    'Bucket' => C('COS_BUCKET'),
                    'Key' => 'cricleInfo'.$workerInfo['u_id'].'.png',
                    'Body' => $file));
                
                unlink($tmp) ;
                
                $tmp = 'https://'.$result['Location'];
            }
            return $tmp ;
            Log::record("生成图片参数".json_encode($workerInfo)."生成图片结果：".json_encode($tmp)) ;
        }catch (\Exception $e){
            Log::record("生成报错了：".$e->getMessage()) ;
        }
        return null ;
    }
   
    protected function jobCricleInfoPaper($cricleInfo,$remote_img_arr,$user_info,$codePath){
        if(empty($cricleInfo)){
            return false ;
        }
        include dirname(dirname(dirname(dirname(__FILE__)))).'/Application/Home/Utils/Qrcode/phpQrcode.class.php';
        include dirname(dirname(dirname(dirname(__FILE__)))).'/Application/Home/Utils/Qrcode/poster.class.php';
       try {
           
           $config = array(
               'bg_url' => dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/draw/bg.png',//背景图片路径
               'text' => array(
                   array(
                       'text' => $user_info['u_nick_name'],//文本内容
                       'left' => 150, //左侧字体开始的位置
                       'top' => 160, //字体的下边框
                       'fontSize' => 19, //字号
                       'fontColor' => '0,0,0', //字体颜色
                       'angle' => 0,
                   ),
                   array(
                       'text' => getCurrentTime(),
                       'left' => 150,
                       'top' => 200,
                       'width' => 400,
                       'fontSize' => 16, //字号
                       'fontColor' => '0,0,0', //字体颜色
                       'angle' => 0,
                   ),
                   array(
                       'text' => $cricleInfo['u_content'],
                       'left' => 40,
                       'top' => 270,
                       'width' => 640,
                       'fontSize' => 25, //字号
                       'fontColor' => '0,0,0', //字体颜色
                       'angle' => 0,
                   ),
                   array(
                       'text' => '@皖礼来  版权所有',
                       'left' => 260,
                       'top' => 960,
                       'width' => 400,
                       'fontSize' => 18, //字号
                       'fontColor' => '169,169,169', //字体颜色
                       'angle' => 0,
                   ),
                   array(
                       'text' => '长按识别小程序码，进入社区通信录',
                       'left' => 180,
                       'top' => 900,
                       'width' => 400,
                       'fontSize' => 18, //字号
                       'fontColor' => '169,169,169', //字体颜色
                       'angle' => 0,
                   ),
               ),
               'image' => array(
                   array(
                       'url' => dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/draw/mouse.png',
                       'stream' => 0, //图片资源是否是字符串图像流
                       'left' => 260,
                       'top' => 1000,
                       'right' => 0,
                       'bottom' => 0,
                       'width' => 203,
                       'height' => 130,
                       'radius' => 0,
                       'opacity' => 100
                   ),
                   array(
                       'url' => dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/draw/top.png',
                       'stream' => 0, //图片资源是否是字符串图像流
                       'left' => 0,
                       'top' => 0,
                       'right' => 0,
                       'bottom' => 0,
                       'width' => 720,
                       'height' => 125,
                       'radius' => 0,
                       'opacity' => 100
                   ),
                   array(
                       'url' => dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/draw/bottom.png',
                       'stream' => 0, //图片资源是否是字符串图像流
                       'left' => 0,
                       'top' => 1155,
                       'right' => 0,
                       'bottom' => 0,
                       'width' => 720,
                       'height' => 125,
                       'radius' => 0,
                       'opacity' => 100
                   ),
                   array(
                       'url' => dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/draw/line.png',
                       'stream' => 0, //图片资源是否是字符串图像流
                       'left' => 1,
                       'top' => 125,
                       'right' => 0,
                       'bottom' => 0,
                       'width' => 10,
                       'height' => 1030,
                       'radius' => 0,
                       'opacity' => 100
                   ),
                   array(
                       'url' => dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/draw/line.png',
                       'stream' => 0, //图片资源是否是字符串图像流
                       'left' => 709,
                       'top' => 125,
                       'right' => 0,
                       'bottom' => 0,
                       'width' => 10,
                       'height' => 1030,
                       'radius' => 0,
                       'opacity' => 100
                   ),
                   array(
                       'url' => '',
                       'stream' => file_get_contents($user_info['u_icon']), //图片资源是否是字符串图像流
                       'left' => 40,
                       'top' => 120,
                       'right' => 0,
                       'bottom' => 0,
                       'width' => 100,
                       'height' => 100,
                       'radius' => 0,
                       'opacity' => 100
                   ),
                   array(
                       'url' => '',
                       'stream' => file_get_contents($codePath),
                       'left' => 255,
                       'top' => 650,
                       'right' => 0,
                       'bottom' => 0,
                       'width' => 210,
                       'height' => 210,
                       'radius' => 0,
                       'opacity' => 100
                   ), array(
                       'url' => '',
                       'stream' => file_get_contents($remote_img_arr[0]),
                       'left' => 40,
                       'top' => 350,
                       'right' => 0,
                       'bottom' => 0,
                       'width' => 210,
                       'height' => 210,
                       'radius' => 0,
                       'opacity' => 100
                   ),
                   array(
                       'url' => '',
                       'stream' => file_get_contents($remote_img_arr[1]),
                       'left' => 252,
                       'top' => 350,
                       'right' => 0,
                       'bottom' => 0,
                       'width' => 210,
                       'height' => 210,
                       'radius' => 0,
                       'opacity' => 100
                   ),
                   array(
                       'url' => '',
                       'stream' => file_get_contents($remote_img_arr[2]),
                       'left' => 465,
                       'top' => 350,
                       'right' => 0,
                       'bottom' => 0,
                       'width' => 210,
                       'height' => 210,
                       'radius' => 0,
                       'opacity' => 100
                   )
               )
           );
           $tmp = dirname(dirname(dirname(dirname(__FILE__)))).'/Uploads/cricleInfo'.$cricleInfo['u_id'].'.png' ;
           //设置海报背景图
           \poster::setConfig($config);
           //设置保存路径
           \poster::make($tmp);
           
           
           $secretId = C("COS_SECRETID"); //"云 API 密钥 SecretId";
           $secretKey = C("COS_SECRETKEY"); //"云 API 密钥 SecretKey";
           $region = "ap-shanghai"; //设置一个默认的存储桶地域
           $cosClient = new Client(
               array(
                   'region' => $region,
                   'schema' => 'http', //协议头部，默认为http
                   'credentials'=> array(
                       'secretId'  => $secretId ,
                       'secretKey' => $secretKey)
               )
           );
           
           $tmp = str_replace('./Uploads', '/Uploads', $tmp) ;
           $file = fopen($tmp, "rb");
           if ($file) {
               $result = $cosClient->putObject(array(
                   'Bucket' => C('COS_BUCKET'),
                   'Key' => 'cricleInfo'.$cricleInfo['u_id'].'.png',
                   'Body' => $file));
               
               unlink($tmp) ;
               
               $tmp = 'https://'.$result['Location'];
           }  
           return $tmp ;
           Log::record("生成图片参数".json_encode($cricleInfo)."生成图片结果：".json_encode($tmp)) ;
       }catch (\Exception $e){
           Log::record("生成报错了：".$e->getMessage()) ;
       }
        return null ;
    }
}