<?php
namespace Home\Service ;
use Common\Service\BaseService;

class UserService implements BaseService {
    
    private $userModel = null ;
    private $userHouseModel = null ;
    
    public function __construct(){
        
        $this->userModel = M("User");
        $this->userHouseModel = M("UserHouse");
    }
    public function changeUserHouse($user_id,$u_house_id){
        
        $this->userHouseModel->startTrans();
        try {
            
            $this->userHouseModel->where(array(
                'u_user_id' => $user_id
            ))->delete() ;
            
            $this->addUserHouse($user_id, $u_house_id) ;
            
            $houseModel = M('House');
            $info = $houseModel->where(array(
                'u_id' => $u_house_id
            ))->find() ;
            
            $current_num = (intval($info['u_users']) + 1) ;
            $houseModel->where(array(
                'u_id' => $u_house_id
            ))->save(array('u_users'=>$current_num)) ;
            
            $info['u_users'] = $current_num ;
            
            return $info ;
        }catch (\Exception $e){
            $this->userHouseModel->rollback() ;
        }
        return null ;        
    }
    public function addUserHouse($u_user_id,$house_id){
        if(empty($u_user_id) || empty($house_id)){
            return false ;
        }
        
        $this->userHouseModel->where(array(
            'u_user_id' => $u_user_id ,
            'u_house_id' => $house_id
        ))->delete() ;
        
        return $this->userHouseModel->data(array(
            'u_user_id' => $u_user_id ,
            'u_house_id' => $house_id ,
            'u_create_time' => getCurrentTime() 
        ))->add() ;
    }
    
    public function hasHouse($u_user_id) {
        $list = $this->userHouseModel->where(array(
            'u_user_id' => $u_user_id
        ))->select() ;
        
        $houseModel = M("House");
        $info = $houseModel->where(array('u_id'=>$list[0]['u_house_id']))->find() ;
        
        //包装用户的楼栋房号信息
        $info['u_building'] = $list[0]['u_building'] ;
        $info['u_number'] = $list[0]['u_number'] ;
        
        $count = $this->userHouseModel->where(array('u_house_id'=>$list[0]['u_house_id']))->count();
        $info['u_users'] = $count ;
        
        //获取该社区通知公告数量
        $noticeService = new NoticeService() ;
        $params['u_public'] = 1;
        $params['u_house_id'] = $info['u_id'];
        $num = $noticeService->getNoticeNumByHouse($params) ;
        
        $info['u_notice_num'] = $num ;
        return array(
            'state' => empty($list) ? false :true ,
            'data' => $info
        ) ;
    }
    public function getInfoById($id) {
        $info = S('getInfoById'.$id) ;
        if(empty($info)){ 
            $info = $this->userModel->where(array(
                'u_id' => $id
            ))->find() ;
            S('getInfoById'.$id,$info,600) ;
        }
        return $info ;
    }
    
    public function getInfoByOpenId($open_id) {
        $info = S('getInfoByOpenId'.$open_id) ;
        if(empty($info)){ 
            $info = $this->userModel->where(array(
                'u_open_id' => $open_id
            ))->find() ;
            S('getInfoByOpenId'.$open_id,$info,600) ;
        }
        return $info ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        $user = $this->userModel->where(array('u_open_id'=>$data['u_open_id']))->find() ;
        if(!$user ){
            $state = $this->userModel->data($data)->add() ;
            return $state ;
        }else{
            //已注册
            return $user['u_id'];
        }
    }
    public function addAndReturn($data)
    {
        $user = $this->userModel->where(array('u_open_id'=>$data['u_open_id']))->find() ;
        if(!$user ){
            $state = $this->userModel->data($data)->add() ;
            $data['u_id'] = $state ;
            return $data ;
        }else{
            //已注册
            //更新用户信息
            $this->userModel->where(array(
                'u_id' => $user['u_id']
            ))->save($data) ;
            
            $u_phone = $this->userModel->field('u_phone')->where(array('u_id' => $user['u_id']))->find() ;
            
            $data['u_phone'] = $u_phone['u_phone'] ;
            $data['u_id'] = $user['u_id'] ;
            return $data;
        }
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->userModel->where(array(
            'u_id'=>$id
        ))->delete() ;
    }
    public function setUserRole($u_user_id,$u_role_ids){
        $userRoleModel = M("UserLevel");
        $userRoleModel->where(array(
            'u_user_id' => $u_user_id
        ))->delete() ;
        
        $arr = explode(',', $u_role_ids) ;
        for ($i=0;$i<count($arr);$i++){
            $userRoleModel->data(array(
                'u_user_id' => $u_user_id ,
                'u_role_id' => $arr[$i]
            ))->add() ;
        }
        return true ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->userModel->where(array(
            'u_id' => $id
        ))->save($data) ;
    }
    public function updateByOpenid($data, $open_id)
    {
        return $this->userModel->where(array(
            'u_open_id' => $open_id
        ))->save($data) ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::getList()
     */
    public function getList($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->userModel->query("SELECT u.*,uh.u_building,uh.u_number,(SELECT ur.`u_icon` FROM u_user_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_user_id` = u.u_id) as u_user_vip FROM u_user_house uh LEFT JOIN u_user u ON uh.`u_user_id` = u.`u_id`
            WHERE uh.`u_house_id` = ".$data['u_house_id']." ORDER BY uh.`u_create_time` DESC limit ".$page.','.$limit) ;
        
        $count = $this->userModel->query("SELECT count(1) FROM u_user_house uh LEFT JOIN u_user u ON uh.`u_user_id` = u.`u_id`
            WHERE uh.`u_house_id` = ".$data['u_house_id']." ORDER BY uh.`u_create_time` DESC ") ;
        
        return array('data' => $list,'total' => $count[0]['count(1)']) ;
        
    }
    public function getListByParams($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->userModel->where($data)->order('u_create_time desc ')->limit($page,$limit)->select();
        $count = $this->userModel->where($data)->count() ;
        
        return array('data' => $list,'total' => $count) ;
        
    }
    
}