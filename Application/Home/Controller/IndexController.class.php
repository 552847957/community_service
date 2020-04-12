<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Service\ActivityService;
use Home\Service\CalendarService;
use Home\Service\MessageService;
use Home\Service\OrderService;
use Home\Service\RoleService;
use Home\Service\ShopGoodService;
use Home\Service\TicketService;
use Home\Service\TopicService;
use Home\Service\UserOrderLocationService;
use Home\Service\WXCodeService;
use Home\Service\WXPayService;
use Qcloud\Cos\Client;
use Think\Controller;
use Think\Log;
use Home\Service\UserWalletService;
use Home\Adapter\CoreAdapter;
use Home\Service\UserWordsService;

class IndexController extends BaseController {
    public function __construct(){
        parent::__construct() ;
    }
    
    public function uploadImgLocal () {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     314572812 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     C('UPLOAD_PATH_ROOT'); // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        // 上传文件
        $info  =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->returnError($upload->getError()) ;
        }else{// 上传成功
            $this->returnSuccess($info) ;
        }
    }
    /**
     * 上传图片
     */
    public function uploadImgRemote () {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     314572812 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     C('UPLOAD_PATH_ROOT'); // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        // 上传文件
        $info  =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->returnError($upload->getError()) ;
        }else{// 上传成功
            
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
            try {
                $key = $info['file']['savename'];
                $srcPath = dirname(dirname(dirname(dirname(__FILE__)))).C('UPLOAD_PATH_ROOT').$info['file']['savepath'].$key;//本地文件绝对路径
               
                $srcPath = str_replace('./Uploads', '/Uploads', $srcPath) ;
                $file = fopen($srcPath, "rb");
                if ($file) {
                    $result = $cosClient->putObject(array(
                        'Bucket' => C('COS_BUCKET'),
                        'Key' => $key,
                        'Body' => $file));
                    
                    //删除文件
                    unlink($srcPath) ;
                    
                    $this->ajaxReturn(array(
                        'code' => 200 ,
                        'msg' => "上传成功" ,
                        'data' => 'https://'.$result['Location']
                    )) ;
                }else{
                    $this->ajaxReturn(array(
                        'code' => 400 ,
                        'msg' => "云存储获取资源路径失败"
                    )) ;
                }
            } catch (\Exception $e) {
                $this->ajaxReturn(array(
                    'code' => 400 ,
                    'msg' => $e->getMessage()
                )) ;
            }
        }
    }
    /**
     * FIXME
     */
    public function checkToken(){
        if(IS_POST){
            $token = $_SERVER['HTTP_TOKEN'];
            $session_info = S($token);
            if(empty($session_info)){
                //说明token无效
                $this->returnJson(204, "token无效，请重新授权登录") ;
            }
            
            $user_info = $this->userService->getInfoByOpenId($session_info['openid']);
            if(empty($user_info)){
                $this->returnJson(204, "token无效，请重新授权登录") ;
            }
            
            $this->returnSuccess("token有效") ;            
        }else {
            $this->returnError('method不支持');
        }
    }
    public function getWXPhone(){
        if(IS_POST){
            $token = $this->getToken();
            $session_info = S($token) ;
            
            $encryptedData = $_POST['encryptedData'];
            $iv = $_POST['iv'];
            $phone = $this->wxUserService->getMobile($session_info['session_key'],$encryptedData,$iv);
            
            if(!empty($phone)) {
                $this->userService->updateByOpenid(array('u_phone'=>$phone),$session_info['openid'] ) ;
                $this->returnSuccess($phone) ;
            }else{
                $this->returnSuccess('') ;
            }
            
        }else {
            $this->returnError('method不支持');
        }
    }
    
    public function updateSessionKey(){
        if(IS_POST){
            $code = $_POST['code'] ;
            // 微信登录 (获取session_key)
            if (!$session = $this->wxUserService->sessionKey($code)) {
                $this->returnError($this->wxUserService->getError()) ;
            }
            $session_key = $session['session_key'];
            $openid = $session['openid'];
            
            $token = $openid ;
            
            S($token,array(
                'session_key' => $session_key ,
                'openid' => $openid
            ),0) ;
            $this->returnSuccess($token) ;
        }else {
            $this->returnError('method不支持');
        }
    }
    /**
     * 登录逻辑v2版本
     */
    public function loginV2(){
        if(IS_POST){
            $code = $_POST['code'] ;
            // 微信登录 (获取session_key)
            if (!$session = $this->wxUserService->sessionKey($code)) {
                $this->returnError($this->wxUserService->getError()) ;
            }
            $session_key = $session['session_key'];
            $openid = $session['openid'];
            
            $token = $openid ;
            
            S($token,array(
                'session_key' => $session_key ,
                'openid' => $openid
            ),0) ;
            
            
            $user_info = $this->userService->getInfoByOpenId($openid);
            if(empty($user_info)){
                $this->returnSuccess(array(
                    'token' => $token ,
                    'user_state' => false 
                )) ;
            }
            
            $this->returnSuccess(array(
                'token' => $token ,
                'user_state' => true ,
                'user_info' => $user_info
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    
    public function updateUserInfo(){
        if(IS_POST){
            $token = $this->getToken();
            $session_info = S($token) ;
            $data = '';
            if(!empty($_POST['u_nick_name'])){
                $data['u_nick_name'] = $_POST['u_nick_name'];
            }
            if(!empty($_POST['u_name'])){
                $data['u_name'] = $_POST['u_name'];
            }
            if(!empty($_POST['u_phone'])){
                $data['u_phone'] = $_POST['u_phone'];
            }
            $state = $this->userService->updateByOpenid($data,$session_info['openid']) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function addUserInfo(){
        if(IS_POST){
            
            $code = $_POST['code'] ;
            // 微信登录 (获取session_key)
            if (!$session = $this->wxUserService->sessionKey($code)) {
                $this->returnError($this->wxUserService->getError()) ;
            }
            $session_key = $session['session_key'];
            $openid = $session['openid'];
            
            $token = $openid ;
            
            S($token,array(
                'session_key' => $session_key ,
                'openid' => $openid
            ),0) ;
            
            
            
            $userInfo = json_decode(htmlspecialchars_decode($_POST['user_info']), true);
            
            if(empty($userInfo)){
                $this->returnError("添加失败") ;
            }
            
            $data['u_nick_name'] = $userInfo['nickName'];
            $data['u_gender'] = $userInfo['gender'];
            $data['u_icon'] = $userInfo['avatarUrl'];
            $data['u_country'] = $userInfo['country'];
            $data['u_province'] = $userInfo['province'];
            $data['u_city'] = $userInfo['city'];
            $data['u_area'] = $_POST['u_area'];
            $data['u_open_id'] = $openid;
         //   $data['u_phone'] = $_POST['u_phone'];
            $data['u_wx'] = $_POST['u_wx'];
            $data['u_create_time'] = getCurrentTime();
            $user_info = $this->userService->addAndReturn($data) ;
            
            if(!empty($user_info)){
                //获取用户的vip信息
                $roleService = new RoleService() ;
                $role_list = $roleService->getUserVIPInfo($user_info['u_id']) ;
                $user_info['vip'] = empty($role_list)?'':$role_list[0] ;
            }
            
            //获取用户社区信息
            $house_info = $this->userService->hasHouse($user_info['u_id']) ;
            S('house_house'.$token,$house_info['data'],0) ;
            
            $this->returnSuccess(array(
                'token' => $token ,
                'user_info' => $user_info ,
                'house_info' => $house_info['data']
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function login(){
        if(IS_POST){
            $userInfo = json_decode(htmlspecialchars_decode($_POST['user_info']), true);
            
            if(empty($userInfo)){
                $this->returnError("登录失败") ;
            }
            $code = $_POST['code'] ;
            // 微信登录 (获取session_key)
            if (!$session = $this->wxUserService->sessionKey($code)) {
                $this->returnError($this->wxUserService->getError()) ;
            }
            $data['u_nick_name'] = $userInfo['nickName'];
            $data['u_gender'] = $userInfo['gender'];
            $data['u_icon'] = $userInfo['avatarUrl'];
            $data['u_country'] = $userInfo['country'];
            $data['u_province'] = $userInfo['province'];
            $data['u_city'] = $userInfo['city'];
            $data['u_area'] = $_POST['b_area'];
            $data['u_open_id'] = $session['openid'];
            $data['u_phone'] = $_POST['b_phone'];
            $data['u_wx'] = $_POST['u_wx'];
            $data['u_create_time'] = getCurrentTime();
            $user_id = $this->userService->add($data) ;
            // 记录缓存, 7天
            $token = $user_id.$session['openid'] ;
            S($token,$data,0) ;
            
            $this->returnSuccess(array(
                'user_id' => $user_id ,
                'token' => $token
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function getUserList(){
        if(IS_POST){
            if(empty($this->getLoginUserHoseId())){
                $this->returnJson(203,'','没有绑定社区，请绑定') ;
            }
            
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $u_house_id = $this->getLoginUserHoseId();
            $list = $this->userService->getList(array(
                'u_house_id' => $u_house_id
            ),$page,$limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function updateUserHouseBind (){
        if(IS_POST){
            $u_user_id = $this->getLoginUserID() ;
            
            $house_id = $_POST['u_house_id'];
            $u_building = $_POST['u_building'];
            $u_number = $_POST['u_number'];
            
            $state = $this->houseService->updateUserBind(array(
                'u_building' => $u_building ,
                'u_number' => $u_number
            ), array(
                'u_user_id' => $u_user_id ,
                'u_house_id' => $house_id
            )) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function addUserHouse(){
        if(IS_POST){
            $u_user_id = $this->getLoginUserID() ;
            $data['u_name'] = $_POST['u_name'];
            $data['u_mark'] = $_POST['u_mark'];
            $data['u_icon'] = $_POST['u_icon'];
            $data['u_users'] = $_POST['u_users'];
            $data['u_admin_user_id'] = $u_user_id ;
            $data['u_lat'] = $_POST['u_lat'];
            $data['u_lng'] = $_POST['u_lng'];
            $info = $this->houseService->add($data) ;
            
            $state = $this->userService->addUserHouse($u_user_id,$info['u_id']) ;
            
            $current_num = (intval($info['u_users']) + 1) ;
            $this->houseService->update(array('u_users'=>$current_num), $info['u_id']) ;
            
            $info['u_users'] = $current_num ;
            $this->returnSuccess($info) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function changeUserHouse(){
        if(IS_POST){
            $u_user_id = $this->getLoginUserID() ;
            
            $u_house_id = $_POST['u_house_id'];
            
            $info = $this->userService->changeUserHouse($u_user_id,$u_house_id) ;
          
            $this->returnSuccess($info) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function getUserInfoById(){
        if(IS_POST){
            if(!empty($_POST['u_user_id'])){
                $state = $this->userService->getInfoById($_POST['u_user_id']) ;
                
                if(!empty($state)){
                    //获取用户的vip信息
                    $roleService = new RoleService() ;
                    $role_list = $roleService->getUserVIPInfo($_POST['u_user_id']) ;
                    $state['vip'] = empty($role_list)?'':$role_list[0] ;
                }
                
            }else{
                $b_id = $this->getLoginUserID();
                $state = $this->userService->getInfoById($b_id) ;
                if(!empty($state)){
                    //获取用户的vip信息
                    $roleService = new RoleService() ;
                    $role_list = $roleService->getUserVIPInfo($b_id) ;
                    $state['vip'] = empty($role_list)?'':$role_list[0] ;
                }
            }
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function getUserInfoByToken(){
        if(IS_POST){
            $b_id = $this->getLoginUserID();
            $state = $this->userService->getInfoById($b_id) ;
            
            if(!empty($state)){
                //获取用户的vip信息
                $roleService = new RoleService() ;
                $role_list = $roleService->getUserVIPInfo($b_id) ;
                $state['vip'] = empty($role_list)?'':$role_list[0] ;
            }
            
            //获取用户社区信息
            $house_info = $this->userService->hasHouse($b_id) ;
            S('house_house'.$this->getToken(),$house_info['data'],0) ;
            
            $this->returnSuccess(array(
                'user_info' => $state ,
                'house_info' => $house_info
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    
    public function hasHouse(){
        if(IS_POST){
            $u_user_id = $this->getLoginUserID();
            $state = $this->userService->hasHouse($u_user_id) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    /**
     * 获取已经被注册的房子列表
     */
    public function getRegisterHouseList(){
        if(IS_POST){
            
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            $list = $this->houseService->getList($params,$page,$limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function getWorkerInfoById(){
        if(IS_POST){
            $b_id = $_POST['u_id'];
            $state = $this->workerService->getWorkerInfoById($b_id) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function updateWorkerCard(){
        if(IS_POST){
            $data['u_card_url'] = $_POST['u_card_url'];
            $state = $this->workerService->updateWorkerCard($data,$_POST['u_id']) ;
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
        
    }
    private function createWorkerSharePic($worker_id,$u_phone,$u_name,$u_cate_code){
        $codePath = $this->getULimitCode($worker_id, 'pages/index/center') ;
        if(!empty($codePath)){
            $data['u_id'] = $worker_id ;
            //获取类别名称
            $cateInfo = $this->cateService->getCateInfoByCode($u_cate_code) ;
            $data['u_cate_name'] = $cateInfo['u_name'] ;
            $data['u_phone'] = $u_phone;
            $data['u_name'] = $u_name ;
            $sharePath = $this->jobWorkerCardPaper($data,$codePath) ;
            
            $this->workerService->update(array(
                'u_share_path' => $sharePath
            ), $worker_id) ;
            return $sharePath ;
        }
    }
    public function updateWorkerSharePic(){
        if(IS_POST){ 
            $worker_id = $_POST['u_id'] ;
            $u_phone = $_POST['u_phone'] ;
            $u_name = $_POST['u_name'] ;
            $u_cate_code = $_POST['u_cate_code'] ;
            
            $sharePath = $this->createWorkerSharePic($worker_id, $u_phone, $u_name, $u_cate_code) ;
            $this->returnSuccess($sharePath) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function addWorker(){
        if(IS_POST){
            //内容检测
            $isOk = $this->checkService->msgSecCheck($_POST['u_name']) ;
            if(!$isOk){
                $this->returnJson(209,"姓名不合法，请重新输入！") ;
            }
            $isOk = $this->checkService->msgSecCheck($_POST['u_mark']) ;
            if(!$isOk){
                $this->returnJson(209,"服务备注不合法，请重新输入！") ;
            }
            
            if(empty($this->getLoginUserHoseId())){
                $this->returnJson(203,'','没有绑定社区，请绑定') ;
            }
            
            $data['u_admin_user_id'] = empty($_POST['u_admin_user_id']) ? $this->getLoginUserID():$_POST['u_admin_user_id'] ;
            $data['u_name'] = $_POST['u_name'];
            
            $data['u_gender'] = $_POST['u_gender'];
            if(empty($_POST['u_icon'])){
                if($data['u_gender'] == 1){
                    $data['u_icon'] = C("COS_ACCESS_BASE_URL").'common/girl.png';
                }else{
                    $data['u_icon'] = C("COS_ACCESS_BASE_URL").'common/boy.png';
                }
            }else{
                $data['u_icon'] = $_POST['u_icon'];
            }
            
            $data['u_cate_code'] = $_POST['u_cate_code'];
            $data['u_mark'] = $_POST['u_mark'];
            $data['u_phone'] = $_POST['u_phone'];
            $data['u_bind_house_id'] = $this->getLoginUserHoseId();
            $state = $this->workerService->add($data) ;
            if($state == 0){
                $this->returnSuccess('recommit') ;
            }
            
            
            $user_info = $this->getLoginUserInfo() ;
            $this->createWorkerSharePic($state,$_POST['u_phone'],$_POST['u_name'],$_POST['u_cate_code']);
            
            //红包设置
            $number = randFloat(0,0.5) ;
            $userWallerService = new UserWalletService() ;
            if(!empty($_POST['u_admin_user_id'])){ //说明此人是被邀请人的，需要返佣金给上级
                $count = $userWallerService->getTodayNumber('1',$_POST['u_admin_user_id']);
                if($count < 4){ 
                    $userWallerService->add(array(
                        'u_user_id' => $_POST['u_admin_user_id'] ,
                        'u_number' => $number>0.5?0.5:$number ,
                        'u_mark' => "邀请技能服务入驻，获得佣金奖励。",
                        'u_cate' => 1
                    )) ;
                    
                    //发送通知
                    $coreAdapter = new CoreAdapter() ;
                    $admin_user_info = $this->userService->getInfoById($_POST['u_admin_user_id']) ;
                    $coreAdapter->pushMoneyMsg($admin_user_info['u_open_id'], 'pages/index/myCenter/myWallet', $admin_user_info['u_nick_name'], "邀请技能服务入驻，获取佣金", $number>0.5?0.5:$number, "社区通信录，我的钱包查看佣金记录，所以佣金记录审核后满足条件即可提现");
                }
            }else{ //自己添加，设置上限，避免恶意的
                $count = $userWallerService->getTodayNumber('0',$this->getLoginUserID());
                if($count < 4){ 
                    $userWallerService->add(array(
                        'u_user_id' => $this->getLoginUserID() ,
                        'u_number' => $number>0.5?0.5:$number ,
                        'u_mark' => "添加技能服务，获得佣金奖励。",
                        'u_cate' => 0
                    )) ;
                    
                    $coreAdapter = new CoreAdapter() ;
                    $login_user_info = $this->getLoginUserInfo() ;
                    $coreAdapter->pushMoneyMsg($login_user_info['u_open_id'], 'pages/index/myCenter/myWallet', $login_user_info['u_nick_name'], "添加技能服务，获取佣金", $number>0.5?0.5:$number, "社区通信录，我的钱包查看佣金记录，所以佣金记录审核后满足条件即可提现");
                }
            }
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    
    public function getWorkerList(){
        if(IS_POST){
            if(empty($this->getLoginUserHoseId())){
                $this->returnJson(203,'','没有绑定社区，请绑定') ;
            }
            
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $u_cate_code = $_POST['u_cate_code'];
            $u_bind_house_id = $this->getLoginUserHoseId();
            $list = $this->workerService->getList(array(
                'u_cate_code' => $u_cate_code ,
                'u_bind_house_id' => $u_bind_house_id
            ),$page,$limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function delWorkerComment(){
        if(IS_POST){
            $u_work_comment_id = $_POST['u_work_comment_id'];
            
            $state = $this->workerService->delWorkerComment($u_work_comment_id) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function addWorkerComment(){
        if(IS_POST){
            //内容检测
            $isOk = $this->checkService->msgSecCheck($_POST['u_content']) ;
            if(!$isOk){
                $this->returnJson(209,"内容不合法，请重新输入！") ;
            }
            $data['u_parent_id'] = $_POST['u_parent_id'];
            $data['u_content'] = $_POST['u_content'];
            $data['u_worker_id'] = $_POST['u_worker_id'];
            $data['u_user_id'] = $this->getLoginUserID();
            $data['u_create_time'] = getCurrentTime();
            
            $state = $this->workerService->addComment($data) ;
            
            //发送模板消息
            $coreAdapter = new CoreAdapter() ;
            $login_user_info = $this->getLoginUserInfo() ;
            if(!empty($_POST['u_parent_id'])){
                $parent_comment_info = $this->workerService->getWorkerCommentInfoByID($_POST['u_parent_id']) ;
                $coreAdapter->pushCommentMsg($parent_comment_info['u_open_id'], 'pages/index/center?u_user_id='.$_POST['u_worker_id'], $parent_comment_info['u_content'], $login_user_info['u_nick_name'], $_POST['u_content'], date("Y年m月d日 H:i",time())) ;
            }
            $work_info = $this->workerService->getWorkerInfoById($_POST['u_worker_id']) ;
            $work_user_info = $this->userService->getInfoById($work_info['u_admin_user_id']) ;
            if(!empty($work_user_info)){
                $coreAdapter->pushCommentMsg($work_user_info['u_open_id'], 'pages/index/center?u_user_id='.$_POST['u_worker_id'], '你的技能服务'.$work_info['u_cate_name'].'有人评论啦', $login_user_info['u_nick_name'], $_POST['u_content'], date("Y年m月d日 H:i",time())) ;
            }
            
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getWorkerCommentList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $u_worker_id = $_POST['u_worker_id'];
            $list = $this->workerService->getCommentList(array(
                'u_worker_id' => $u_worker_id
            ),$page,$limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    private function updateImg($imgs){
        $imgs = explode(",", $imgs) ;
        $arr = array() ;
        for($i=0;$i<count($imgs);$i++){
            if(!empty($imgs[$i])){
                array_push($arr, C("SERVICE_SITE").'Uploads/'.$imgs[$i]) ;
            }
        }
        return implode(',', $arr) ;
    }
    
    
    
    private function createTrendSharePic($trend_id,$content,$remote_img_arr,$u_nick_name,$icon){
        
        $codePath = $this->getULimitCode($trend_id, 'pages/index/cricleInfo') ;
        if(!empty($codePath)){
            $data['u_id'] = $trend_id ;
            $sharePath = $this->jobCricleInfoPaper(array(
                'u_id' => $trend_id ,
                'u_content' => $content
            ),$remote_img_arr,array(
                'u_nick_name' => $u_nick_name ,
                'u_icon' => $icon
            ), $codePath) ;
            
            $this->trendService->update(array(
                'u_share_path' => $sharePath
            ), $trend_id) ;
            return $sharePath ;
        }
    }
    public function updateTrendSharePic(){
        if(IS_POST){
            $trend_id = $_POST['u_id'] ;
            $content = $_POST['content'] ;
            $remote_img_arr = explode(',', $_POST['remote_img_arr']) ;
            $u_nick_name = $_POST['u_nick_name'] ;
            $icon = $_POST['icon'];
            $sharePath = $this->createTrendSharePic($trend_id,$content,$remote_img_arr,$u_nick_name,$icon) ;
            $this->returnSuccess($sharePath) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function addTrend(){
        if(IS_POST){
            //内容检测
            $isOk = $this->checkService->msgSecCheck($_POST['u_content']) ;
            if(!$isOk){
                $this->returnJson(209,"内容不合法，请重新输入！") ;
            }
            if(empty($this->getLoginUserHoseId())){
                $this->returnJson(203,'','没有绑定社区，请绑定') ;
            }
            
            $data['u_content'] = $_POST['u_content'];
            if(!empty($_POST['u_topic_id'])){
                $data['u_topic_id'] = $_POST['u_topic_id'];
            }
            $data['u_house_id'] = $this->getLoginUserHoseId();
            $data['u_user_id'] = $this->getLoginUserID();
            
            
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
            
            if(!empty($_POST['u_imgs'])){
                $tmpArr = explode(',', $_POST['u_imgs']) ;
                for ($i = 0;$i<count($tmpArr);$i++){
                    $state = $this->checkService->imgSecCheck(new \CURLFile('./Uploads/'.$tmpArr[$i])) ;
                    if(!$state){
                        $this->returnJson(209,"图片不合法，请重新上传后再提交！") ;
                    }
                }
                $remote_img_arr = array();
                for ($i = 0;$i<count($tmpArr);$i++){ //全部交验成功后才能上传腾讯云存储
                    try {
                        $tmpInfo = explode('/', $tmpArr[$i]) ;
                        $key = $tmpInfo[1];
                        $srcPath = dirname(dirname(dirname(dirname(__FILE__)))).C('UPLOAD_PATH_ROOT').$tmpInfo[0].'/'.$key;//本地文件绝对路径
                       
                        $srcPath = str_replace('./Uploads', '/Uploads', $srcPath) ;
                        $file = fopen($srcPath, "rb");
                        if ($file) {
                            $result = $cosClient->putObject(array(
                                'Bucket' => C('COS_BUCKET'),
                                'Key' => $key,
                                'Body' => $file));
                            
                            unlink($srcPath) ;
                            
                            array_push($remote_img_arr, 'https://'.$result['Location']) ;
                        }
                    } catch (\Exception $e) {
                       Log::record("上传云存储失败。".$e->getMessage()) ;
                    }
                }
                $data['u_imgs'] = implode(',', $remote_img_arr) ;
            }
            $state = $this->trendService->add($data) ;
            
            
            $user_info = $this->getLoginUserInfo() ;
            $this->createTrendSharePic($state, $_POST['u_content'], $remote_img_arr, $user_info['u_nick_name'], $user_info['u_icon']) ;
            
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function delTrend(){
        if(IS_POST){
            $u_trend_id = $_POST['u_trend_id'];
            
            $state = $this->trendService->delete($u_trend_id) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function getTrendInfoById(){
        if(IS_POST){
            $u_trend_id = $_POST['u_trend_id'];
            
            $state = $this->trendService->getTrendInfoById($u_trend_id) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function getMyTrendList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $u_user_id = $_POST['u_user_id'];
            $list = $this->trendService->getMyTrendList(array(
                'u_user_id' => $u_user_id
            ),$page,$limit) ;
            
            $user_info = $this->userService->getInfoById($u_user_id) ;
            $user_info['index'] = C('COS_ACCESS_BASE_URL').'common/index.png' ;
            $this->returnSuccess(array(
                'user_info'=>$user_info,
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getTrendList(){
        if(IS_POST){
            if(empty($this->getLoginUserHoseId())){
                $this->returnJson(203,'','没有绑定社区，请绑定') ;
            }
            
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $u_house_id = $this->getLoginUserHoseId();
            $list = $this->trendService->getList(array(
                'u_house_id' => $u_house_id
            ),$page,$limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getTrendCommentList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $u_trend_id = $_POST['u_trend_id'];
            $list = $this->trendService->getCommentList(array(
                'u_trend_id' => $u_trend_id
            ),$page,$limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function addTrendComment(){
        if(IS_POST){
            
            //内容检测
            $isOk = $this->checkService->msgSecCheck($_POST['u_content']) ;
            if(!$isOk){
                $this->returnJson(209,"内容不合法，请重新输入！") ;
            }
            $data['u_parent_id'] = $_POST['u_parent_id'];
            $data['u_content'] = $_POST['u_content'];
            $data['u_trend_id'] = $_POST['u_trend_id'];
            $data['u_user_id'] = $this->getLoginUserID();
            $data['u_create_time'] = getCurrentTime();
            
            $state = $this->trendService->addComment($data) ;
            
            //发送模板消息
            $coreAdapter = new CoreAdapter() ;
            $login_user_info = $this->getLoginUserInfo() ;
            //FIXME 暂且处理方法，避免微信消息检测失败不予发送
            $tmp_content = explode("</span>:", $_POST['u_content']) ;
            if(!empty($_POST['u_parent_id'])){
                $parent_comment_info = $this->trendService->getTrendCommentInfoByID($_POST['u_parent_id']) ;
                $coreAdapter->pushCommentMsg($parent_comment_info['u_open_id'], 'pages/index/cricleInfo?trend_id='.$_POST['u_trend_id'], $parent_comment_info['u_content'], $login_user_info['u_nick_name'], $tmp_content[1], date("Y年m月d日 H:i",time())) ;
            }
            $trend_info = $this->trendService->getTrendInfoById($_POST['u_trend_id']) ;
            $trend_user_info = $this->userService->getInfoById($trend_info['u_user_id']) ;
            if(!empty($work_user_info)){
                $coreAdapter->pushCommentMsg($trend_user_info['u_open_id'], 'pages/index/cricleInfo?trend_id='.$_POST['u_trend_id'], $trend_info['u_content'], $login_user_info['u_nick_name'],$tmp_content[1] , date("Y年m月d日 H:i",time())) ;
            }
            
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function delTrendComment(){
        if(IS_POST){
            $u_trend_comment_id = $_POST['u_trend_comment_id'];
            
            $state = $this->trendService->deleteComment($u_trend_comment_id) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function updateTrendView(){
        if(IS_POST){
            $u_trend_id = $_POST['u_trend_id'];
            
            $state = $this->trendService->updateView($u_trend_id) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    
    public function getCateList(){
        if(IS_POST){
            $state = $this->cateService->getCateList() ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    
    /**
     * 添加举报
     */
    public function addReport(){
        if(IS_POST){
            
            $data['u_from_user_id'] = $this->getLoginUserID();
            $data['u_to_user_id'] = $_POST['u_to_user_id'];
            $data['u_content'] = $_POST['u_content'];
                        
            $state = $this->reportService->add($data) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function getReportReasonList(){
        if(IS_POST){
            
            $state = $this->reportService->getReportReasonList() ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    /***********************商业机构的评论 start**********************************/
    public function getBusinessCommentList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $u_global_contact_id = $_POST['u_global_contact_id'];
            $list = $this->globalContactService->getCommentList(array(
                'u_global_contact_id' => $u_global_contact_id
            ),$page,$limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function addBusinessComment(){
        if(IS_POST){
            
            //内容检测
            $isOk = $this->checkService->msgSecCheck($_POST['u_content']) ;
            if(!$isOk){
                $this->returnJson(209,"内容不合法，请重新输入！") ;
            }
            $data['u_parent_id'] = $_POST['u_parent_id'];
            $data['u_content'] = $_POST['u_content'];
            $data['u_global_contact_id'] = $_POST['u_global_contact_id'];
            $data['u_user_id'] = $this->getLoginUserID();
            $data['u_create_time'] = getCurrentTime();
            
            $state = $this->globalContactService->addComment($data) ;
            
            //发送模板消息
            $coreAdapter = new CoreAdapter() ;
            $login_user_info = $this->getLoginUserInfo() ;
            $temp_arr = explode('</span>:', $_POST['u_content']) ;
            if(!empty($_POST['u_parent_id'])){
                $parent_comment_info = $this->globalContactService->getBusinessCommentInfoByID($_POST['u_parent_id']) ;
                $coreAdapter->pushCommentMsg($parent_comment_info['u_open_id'], 'pages/index/business/businessDetail?u_id='.$_POST['u_global_contact_id'], $parent_comment_info['u_content'], $login_user_info['u_nick_name'], $temp_arr[1], date("Y年m月d日 H:i",time())) ;
            }
            
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function setIncBusinessViews(){
        if(IS_POST){
            $u_global_contact_id = $_POST['u_id'];
            
            $state = $this->globalContactService->setIncBusinessViews($u_global_contact_id) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function delBusinessComment(){
        if(IS_POST){
            $u_global_contact_id = $_POST['u_global_contact_id'];
            
            $state = $this->globalContactService->deleteComment($u_global_contact_id) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    /***********************商业机构的评论 end*****************************/
    
    public function getBusinessCateList(){
        if(IS_POST){
            $list = $this->globalContactService->getBBBoxCateAllList() ;
            
            $this->returnSuccess($list) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    
    public function getCommonContactList(){
        if(IS_POST){
            $list = $this->globalContactService->getCommonContactList() ;
            
            $this->returnSuccess($list) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    /**
     * 获取与用户相关的公共服务电话信息
     */
    public function getAboutContactList(){
        if(IS_POST){
            
            if(empty($this->getLoginUserHoseId())){
                $this->returnJson(203,'','没有绑定社区，请绑定') ;
            }
            $house_id = $this->getLoginUserHoseId() ;
            
            $list = $this->globalContactService->getAboutContactList($house_id) ;
            
            $this->returnSuccess($list) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    
    public function getContactInfoById(){
        if(IS_POST){
            
            $u_id = $_POST['u_id'] ;
            
            $list = $this->globalContactService->getBBBoxById($u_id) ;
            
            $this->returnSuccess($list) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function getContactListByHouseAndCate(){
        if(IS_POST){
            
            if(empty($this->getLoginUserHoseId())){
                $this->returnJson(203,'','没有绑定社区，请绑定') ;
            }
            $house_id = $this->getLoginUserHoseId() ;
            $cate_code = $_POST['cate_code'] ;
            
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $list = $this->globalContactService->getContactListByHouseAndCate(array(
                'cate_code' => $cate_code ,
                'u_house_id' => $house_id
            ),$page,$limit) ;
            
            $this->returnSuccess($list) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    /****************************************/
    public function getNewNotice(){
        if(IS_POST){
            $user_id = $this->getLoginUserID() ;
            if(!empty($user_id)){
                $house_info = $this->userService->hasHouse($user_id) ;
                if(!empty($house_info) && $house_info['state']){
                    $house_id = $house_info['data']['u_id'] ;
                    $state = $this->noticeService->getNewHouseNotice($house_id) ;
                    if(!empty($state)){
                        $this->returnSuccess($state) ;
                    }
                }
            }
            
            $state = $this->noticeService->getNewNotice() ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getNoticeById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->noticeService->getNoticeById($u_id) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getNoticeList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $tag = $_POST['tag'];
            if($tag == 'common'){
                $params['u_public'] = 0;
            }else{
                $house_id = $this->getLoginUserHoseId();
                if(empty($house_id)||$house_id == 'undefined'){
                    $this->returnJson(203,'','没有绑定社区，请绑定') ;
                }
                $params['u_public'] = 1;
                $params['u_house_id'] = $house_id;
            }
           
            $list = $this->noticeService->getList($params, $page, $limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function clearToken(){
        S('WXContentCheckServiceToken',null);
    }
    
    private function getULimitCode ($scene_id,$page){
        $codeService = new WXCodeService(C("APP_ID"), C("APP_SECRITE")) ;
        $codePath = $codeService->getUnlimited($scene_id, $page) ;
        if(empty($codePath)){
            return null ;
        }
        return $codePath ;
    }
    public function getCodeUnlimited (){
        if(IS_POST){

            $scene = $_POST['scene_id'];
            $page = $_POST['page'];
            $codePath = $this->getULimitCode($scene, $page) ;
            if(empty($codePath)){
                $this->returnError("生成失败");
            }
            
            
            if($_POST['tag'] == 'cricleInfo'){ //动态详情页的动态分享
                $cricleInfo = $_POST['data_info'];
                $codePath = $this->jobCricleInfoPaper($cricleInfo,$codePath) ;
            }
            $this->returnSuccess($codePath) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    
    public function addCalendar(){
        if(IS_POST){
            $u_take_date = $_POST['u_take_date'];
            $u_user_id = $this->getLoginUserID();
            
            $tmp_arr = explode('-', $u_take_date) ;
            if(!empty($tmp_arr[1]) && strlen($tmp_arr[1]) ==1){
                $tmp_arr[1] = '0'.$tmp_arr[1] ;
            }
            if(!empty($tmp_arr[2]) && strlen($tmp_arr[2]) == 1){
                $tmp_arr[2] = '0'.$tmp_arr[2] ;
            }
            
            $u_take_date = join('-', $tmp_arr);
            
            $calendarService = new CalendarService() ;
            $reslut = $calendarService->add(array(
                'u_take_date' => $u_take_date ,
                'u_user_id' => $u_user_id 
            )) ;
            
            try {
                $coreAdapter = new CoreAdapter() ;
                $login_user_info = $this->getLoginUserInfo() ;
                $res = $calendarService->geCalendartList($u_user_id) ;
                $coreAdapter->pushSignMsg($login_user_info['u_open_id'], 'pages/index/qian', "社区通信录每日签到", date("H:i",time()), count($res), "每日签到，获取积分，邻里活跃，赢得好礼哦") ;
            }catch (\Exception $e){
                Log::record("发送签到模板消息".$e->getMessage()) ;
            }
            
            
            $this->returnSuccess($reslut) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function geCalendartList(){
        if(IS_POST){
            $u_user_id = $this->getLoginUserID();
            
            $calendarService = new CalendarService() ;
            $res = $calendarService->geCalendartList($u_user_id) ;
            $this->returnSuccess($res) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    
    public function getHouseFriendAllList(){
        if(IS_POST){
            $u_user_id = $this->getLoginUserID();
            
            $res = $this->houseService->getHouseFriendAllList($u_user_id);
            
            $this->returnSuccess($res) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function calendarOrder(){
        if(IS_POST){
            $calendarService = new CalendarService() ;
            $res = $calendarService->calendarOrder() ;
            $this->returnSuccess($res) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    /************************************/
    public function getMessageList(){
        if(IS_POST){
            $messageService = new MessageService() ;
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $u_user_id = $this->getLoginUserID();
            $list = $messageService->getList(array(
                'u_user_id' => $u_user_id
            ),$page,$limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    /************************************/
    public function getNewActivity(){
        if(IS_POST){
            $activityService = new ActivityService() ;
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $info = $activityService->getNewActivity();
            
            $this->returnSuccess($info) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getActivityById(){
        if(IS_POST){
            $activityService = new ActivityService() ;
            $id= $_POST['u_id'] ;
            $info = $activityService->getActivityById($id);
            $activityService->setIncView($id);
            $this->returnSuccess($info) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    /************************************/
    public function getListWithSession(){
        if(IS_POST){
            $ticketService = new TicketService() ;
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $user_id = $this->getLoginUserID() ;
            $good_id = $_POST["good_id"];
            $list = $ticketService->getListWithSession($user_id,$good_id);
            
            $this->returnSuccess($list) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getUserTicketList(){
        if(IS_POST){
            $ticketService = new TicketService() ;
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $user_id = $this->getLoginUserID() ;
            $data['u_user_ticket.u_user_id'] = $user_id ;
            if(!empty($_POST['cate']) && $_POST['cate'] == 'about'){ //说明是获取用户的ticket
                $list = $ticketService->getUserTicketList($data, $page, $limit);
            }else{
                $list = $ticketService->getList($data, $page, $limit);
            }
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function addFromId(){
        if(IS_POST){
            $u_form_id = $_POST['u_form_id'];
            $user_id = $this->getLoginUserID() ;
            if(!empty($u_form_id)){
                $this->msgService->add(array(
                    'u_user_id' => $user_id ,
                    'u_from_id' => $u_form_id
                )) ;
            }
            $this->returnSuccess(true) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function addUserTicket(){
        if(IS_POST){
            $u_form_id = $_POST['u_form_id'];
            
            
            $ticketService = new TicketService() ;
            $user_id = $this->getLoginUserID() ;
            $data['u_user_id'] = $user_id ;
            $data['u_ticket_id'] = $_POST['u_ticket_id'] ;
            $info = $ticketService->addUserTicket($data);
            if($info > 0){
                
                /**
                 * 保存用户的form_id
                 */
                if(!empty($u_form_id)){
                    $this->msgService->add(array(
                        'u_user_id' => $user_id ,
                        'u_from_id' => $u_form_id
                    )) ;
                }
                
                
                $this->returnSuccess($info) ;
            }
            $this->returnSuccess('') ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function updateUserTicket(){
        if(IS_POST){
            $ticketService = new TicketService() ;
            $data['u_used'] = $_POST['u_used'] ;
            $info = $ticketService->updateUserTicket($data,$_POST['u_id']);
            $this->returnSuccess($info) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    /****************************************************/
    public function updateGood (){
        if(IS_POST){
           $data['u_ok'] = $_POST['u_ok'] ;
            
            $goodService = new ShopGoodService() ;
            $state = $goodService->update($data,$_POST['u_id']) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function updateGoodView (){
        if(IS_POST){
            $goodService = new ShopGoodService() ;
            $u_id = $_POST['u_id'] ;
            $info = $goodService->updateGoodView('u_views',$u_id) ;
            
            $this->returnSuccess($info) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function addGood(){
        if(IS_POST){
            if(!empty($_POST['u_name'])){
                $data['u_name'] = $_POST['u_name'] ;
            }
            if(!empty($_POST['u_now_price'])){
                $data['u_now_price'] = $_POST['u_now_price'] ;
            }
            if(!empty($_POST['u_past_price'])){
                $data['u_past_price'] = $_POST['u_past_price'] ;
            }
            if(!empty($_POST['u_stores'])){
                $data['u_stores'] = $_POST['u_stores'] ;
            }
            if(!empty($_POST['u_specs'])){
                $data['u_specs'] = $_POST['u_specs'] ;
            }
            $data['u_ok'] = '2'; //用户自主上传的商品必须要审核才能使用
            $data['u_source'] = 'user';
            $data['u_user_id'] = $this->getLoginUserID() ;
            
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
            
            if(!empty($_POST['u_content'])){
                $tmpArr = explode(',', $_POST['u_content']) ;
                for ($i = 0;$i<count($tmpArr);$i++){
                    $state = $this->checkService->imgSecCheck(new \CURLFile('./Uploads/'.$tmpArr[$i])) ;
                    if(!$state){
                        $this->returnJson(209,"图片不合法，请重新上传后再提交！") ;
                    }
                }
                $remote_img_arr = array();
                for ($i = 0;$i<count($tmpArr);$i++){ //全部交验成功后才能上传腾讯云存储
                    try {
                        $tmpInfo = explode('/', $tmpArr[$i]) ;
                        $key = $tmpInfo[1];
                        $srcPath = dirname(dirname(dirname(dirname(__FILE__)))).C('UPLOAD_PATH_ROOT').$tmpInfo[0].'/'.$key;//本地文件绝对路径
                        
                        $srcPath = str_replace('./Uploads', '/Uploads', $srcPath) ;
                        $file = fopen($srcPath, "rb");
                        if ($file) {
                            $result = $cosClient->putObject(array(
                                'Bucket' => C('COS_BUCKET'),
                                'Key' => $key,
                                'Body' => $file));
                            
                            unlink($srcPath) ;
                            
                            array_push($remote_img_arr, '<img style="width:100%;display:block;" src="'.'https://'.$result['Location'].'" />') ;
                        }
                    } catch (\Exception $e) {
                        Log::record("上传云存储失败。".$e->getMessage()) ;
                    }
                }
                $data['u_content'] = '<DIV style="width:100%">'.implode('', $remote_img_arr).'</DIV>' ;
            }
            if(!empty($_POST['u_covers'])){
                $state = $this->checkService->imgSecCheck(new \CURLFile('./Uploads/'.$_POST['u_covers'])) ;
                if(!$state){
                    $this->returnJson(209,"图片不合法，请重新上传后再提交！") ;
                }
                try {
                    $tmpInfo = explode('/', $_POST['u_covers']) ;
                    $key = $tmpInfo[1];
                    $srcPath = dirname(dirname(dirname(dirname(__FILE__)))).C('UPLOAD_PATH_ROOT').$tmpInfo[0].'/'.$key;//本地文件绝对路径
                    
                    $srcPath = str_replace('./Uploads', '/Uploads', $srcPath) ;
                    $file = fopen($srcPath, "rb");
                    if ($file) {
                        $result = $cosClient->putObject(array(
                            'Bucket' => C('COS_BUCKET'),
                            'Key' => $key,
                            'Body' => $file));
                        
                        unlink($srcPath) ;
                        $data['u_covers'] = 'https://'.$result['Location'] ;
                    }
                } catch (\Exception $e) {
                    Log::record("上传云存储失败。".$e->getMessage()) ;
                }
            }
            
            $goodService = new ShopGoodService() ;
            if(!empty($_POST['u_id'])){
                $state = $goodService->update($data,$_POST['u_id']) ;
            }else{
                $data['u_create_time'] = getCurrentTime() ;
                $state = $goodService->add($data) ;
                
                //FIXME 新增的商品需要审核，直接发给管理员
                $coreAdapter = new CoreAdapter() ;
                $admin_opens = C('ADMIN_USER_OPEN');
                for ($i=0;$i<count($admin_opens);$i++){
                    $user_info = $this->getLoginUserInfo() ;
                    $coreAdapter->pushCheckGoodMsg($admin_opens[$i], 'pages/index/checkGood', $user_info['u_nick_name'], "待审核", date("Y.m.d H:i:s",time()), $user_info['u_nick_name'].'提交的闲置等待管理员审核') ;
                }
            }
            $this->returnSuccess($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getGoodSharePic(){
        if(IS_POST){
            $u_good_id = $_POST['u_good_id'];
            //生成分享图
            $codePath = $this->getULimitCode($u_good_id, 'pages/index/shop/produce') ;
            $userInfo = $this->getLoginUserInfo() ;
            
            $goodService = new ShopGoodService() ;
            $goodInfo = $goodService->getGoodInfoById($u_good_id);
            if(!empty($codePath)){
                
                $sharePath = $this->jobGoodInfoPaper($goodInfo,$userInfo,$codePath) ;
                
                $this->returnSuccess($sharePath) ;
            }
            $this->returnSuccess("") ;
        }else {
            $this->returnError('method不支持');
        }
    }
    /**
     * 获取用户自己发布的闲置列表
     */
    public function getUserGoodList(){
        if(IS_POST){
            $goodService = new ShopGoodService() ;
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $data['u_user_id'] = $this->getLoginUserID();
            $list = $goodService->getUserGoodList($data, $page, $limit) ;
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function getGoodList(){
        if(IS_POST){
            $goodService = new ShopGoodService() ;
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $house_id = $this->getLoginUserHoseId();
            $cate = $_POST['cate'];
            $data = array() ;
            if($cate == 'about'){ //邻里闲置商品
                if(empty($house_id)||$house_id == 'undefined'){
                    $this->returnJson(203,'','没有绑定社区，请绑定') ;
                }else{
                    $data['u_house_id'] = $house_id;
                    $list = $goodService->getUserGoodWithHouseList($data, $page, $limit) ;
                }
            }else{
                $data['u_ok'] = '0';
                $data['u_source'] = 'office';
                $list = $goodService->getList($data, $page, $limit) ;
            }
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getGoodInfoById(){
        if(IS_POST){
            $goodService = new ShopGoodService() ;
            $u_id = $_POST['u_id'] ;
            $info = $goodService->getGoodInfoById($u_id) ;
            
            $this->returnSuccess($info) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    /*****************************************************/
    public function getTopicList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $topicService = new TopicService() ;
            $list = $topicService->getList(array(), $page, $limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function getTopicInfoById(){
        if(IS_POST){
            $topicService = new TopicService() ;
            $u_id = $_POST['u_id'] ;
            $info = $topicService->getTopicInfoById($u_id) ;
            
            
            $this->returnSuccess($info) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getTopicTrendList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params['u_content'] = $_POST['u_content'];
            
            if($_POST['u_cate'] == 'common'){
                $list = $this->trendService->getTopicTrendList($params,$page,$limit) ;
            }else{
                $list = $this->trendService->getTrendUserOrderList($params,$page,$limit) ;
            }
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getTrendUserOrderList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params['u_content'] = $_POST['u_content'];
            
            $list = $this->trendService->getTrendUserOrderList($params,$page,$limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getUserOrderList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $u_user_id = $this->getLoginUserID() ;
            $orderService = new OrderService() ;
            $list = $orderService->getOrderWithGoodList(array(
                'u_order.u_user_id' => $u_user_id ,
                'u_order.u_good_id' => array('neq',0)
            ),$page,$limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getMySaledOrderList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $u_user_id = $this->getLoginUserID() ;
            $orderService = new OrderService() ;
            $list = $orderService->getMySaledOrderList(array(
                'u_shop_good.u_user_id' => $u_user_id ,
                'u_order.u_state' => array('in','1,5')
            ),$page,$limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    /**
     * VIP虚拟产品的信息
     */
    public function getVIPInfo(){
        if(IS_POST){
            
            $user_id = $this->getLoginUserID() ;
            $roleService = new RoleService() ;
            $role_list = $roleService->getUserVIPInfo($user_id) ;
            
            $tag = false ;
            for ($i=0;$i<count($role_list);$i++){
                if($role_list[$i]['u_icon'] == 2){
                    $tag = true ;
                    break ;
                }
            }
            
            $this->returnSuccess(array(
                'price' => C('VIP_PRICE') ,
                'is_super_vip' => $tag ,
                'vip_info' => $role_list[0] //FIXME 当前只会存在一种VIP状态
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    /*************************************************/
    public function orderGood(){
        if(IS_POST){
            $data['u_good_id'] = $_POST['u_good_id'] ;
            $data['u_user_id'] = $this->getLoginUserID() ;
      //      $data['u_total_price'] = $_POST['u_total_price'] ;
            
            $orderService = new OrderService() ;
            $state = $orderService->add($data) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function doPay(){
        if(IS_POST){
            $u_total_price = $_POST['u_total_price'];
            $u_total_price = sprintf("%.2f",$u_total_price) ;
            $u_order_id = $_POST['u_order_id'];
            $u_good_id = $_POST['u_good_id'];
            $u_ticket_id = $_POST['u_ticket_id'];
            $u_mark = $_POST['u_mark'];
            $u_number = $_POST['u_number']; //商品数量
            
            
            $u_address_order_id = $_POST['u_address_order_id'] ; //关于收货地址的id
            
            $u_user_id = $this->getLoginUserID() ; 
            $goodService = new ShopGoodService() ;
            
            $orderService = new OrderService() ;
            $order_info = $orderService->getOrderById($u_order_id);
            if(empty($order_info)){
                $this->returnError("订单数据异常") ;
            }
            
            $token = $this->getToken() ;
            $session_info = S($token) ;
            $open_id = $session_info['openid'];
            
            $out_trade_no = $order_info['u_code'] ;
            $total_fee = floatval($u_total_price*100);
            
            if($u_good_id == '0'){ //FIXME 平台暂且的处理，VIP虚拟产品下单
                $body = "社区通信录超级VIP付款";
                $payService = new WXPayService(C('APP_ID'), $open_id, C('MCH_ID'), C('MCH_KEY'), $out_trade_no, $body, $total_fee,C('SERVICE_SITE').'index.php/Home/Notice/vipIndex') ;
                $return=$payService->pay();
                
                if(empty($return)){
                    $this->returnSuccess('') ;
                }else{
                    $orderService->update(array(
                        'u_state' => 4 ,
                        'u_total_price' => $u_total_price,
                        'u_mark' => $u_mark ,
                        'u_ticket_id' => $u_ticket_id
                    ), $u_order_id) ;
                    
                    $this->returnSuccess($return) ;
                }
            }else{
                $good_info = $goodService->getGoodInfoById($u_good_id) ;
                if(!empty($good_info) && intval($good_info['u_stores']) > intval($u_number)){
                  
                    $body = $good_info['u_name']."付款";
                    $payService = new WXPayService(C('APP_ID'), $open_id, C('MCH_ID'), C('MCH_KEY'), $out_trade_no, $body, $total_fee,C('SERVICE_SITE').'index.php/Home/Notice/index') ;
                    $return=$payService->pay();
                    
                    if(empty($return)){
                        $this->returnSuccess('') ;
                    }else{
                        
                        //关联订单的收货地址信息
                        if(!empty($u_address_order_id)){
                            $orderService->updateOrderLocation($u_address_order_id, $order_info['u_id']) ;
                        }
                        
                        $orderService->update(array(
                            'u_state' => 4 ,
                            'u_number' => $u_number ,
                            'u_total_price' => $u_total_price,
                            'u_mark' => $u_mark ,
                            'u_ticket_id' => $u_ticket_id
                        ), $u_order_id) ;
                        
                        $this->returnSuccess($return) ;
                    }
                    
                }else{
                    $this->returnError("商品没有存在或不存在") ;
                }
            }
        }else{
            $this->returnError('method不支持');
        }
    }
    
    
    /*****************************************/
    
    public function addUserOrderLocation(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'];
            $data['u_phone'] = $_POST['u_phone'];
            $data['u_address_name'] = $_POST['u_address_name'];
            $data['u_address'] = $_POST['u_address'];
            $data['u_area'] = $_POST['u_area'];
            $data['u_user_id'] = $this->getLoginUserID();
            $data['u_default'] = $_POST['u_default'] == 'true' ? '1':'0';
            
            $userOrderLocationService = new UserOrderLocationService() ;
            $state = $userOrderLocationService->add($data) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function updateUserOrderLocation(){
        if(IS_POST){
            if(!empty($_POST['u_name'])){
                $data['u_name'] = $_POST['u_name'];
            }
            if(!empty($_POST['u_phone'])){
                $data['u_phone'] = $_POST['u_phone'];
            }
            if(!empty($_POST['u_address_name'])){
                $data['u_address_name'] = $_POST['u_address_name'];
            }
            if(!empty($_POST['u_address'])){
                $data['u_address'] = $_POST['u_address'];
            }
            if(!empty($_POST['u_area'])){
                $data['u_area'] = $_POST['u_area'];
            }
            $data['u_default'] = $_POST['u_default'] == 'true' ? '1':'0';
            
            $userOrderLocationService = new UserOrderLocationService() ;
            $state = $userOrderLocationService->update($data,$_POST['u_id']) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getUserOrderLocationList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $data['u_user_id'] = $this->getLoginUserID() ;
            $userOrderLocationService = new UserOrderLocationService() ;
            $list = $userOrderLocationService->getList($data, $page, $limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getUserOrderLocationById(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            $userOrderLocationService = new UserOrderLocationService() ;
            $info = $userOrderLocationService->getUserOrderLocationById($u_id) ;
            
            $this->returnSuccess($info) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getUserOrderDefaultLocation(){
        if(IS_POST){
            $u_user_id = $this->getLoginUserID() ;
            $userOrderLocationService = new UserOrderLocationService() ;
            $info = $userOrderLocationService->getUserOrderDefaultLocation($u_user_id) ;
            
            $this->returnSuccess($info) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    
    public function getUserWalletList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $data['u_user_id'] = $this->getLoginUserID() ;
            $userWallerService = new UserWalletService() ;
            $list = $userWallerService->getList($data, $page, $limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function getMSGIDSList(){
        if(IS_POST){
            $this->returnSuccess(C("RESPONSE_TEMP_IDS")) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    public function addWords(){
        if(IS_POST){
            $isOk = $this->checkService->msgSecCheck($_POST['u_content']) ;
            if(!$isOk){
                $this->returnJson(209,"内容不合法，请重新输入！") ;
            }
            $to_user_id = $_POST['to_user_id'];
            $data['u_from_user_id'] = $this->getLoginUserID() ;
            $data['u_to_user_id'] = $to_user_id ;
            $data['u_create_time'] = getCurrentTime() ;
            $data['u_content'] = $_POST['u_content'] ;
            $userWordsService = new UserWordsService() ;
            $state = $userWordsService->add($data) ;
            
            $this->returnSuccess($state) ;
        }else{
            $this->returnError('method不支持');
        }
    }
    
    public function getWordsListWithToUser(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $to_user_id = $_POST['to_user_id'];
            $data['u_user_words.u_from_user_id'] = array('in',$this->getLoginUserID().','.$to_user_id) ;
            $data['u_user_words.u_to_user_id'] = array('in',$this->getLoginUserID().','.$to_user_id)  ;
            $userWordsService = new UserWordsService() ;
            $list = $userWordsService->getList($data, $page, $limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError('method不支持');
        }
    }
}