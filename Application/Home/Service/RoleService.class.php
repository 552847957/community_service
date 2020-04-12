<?php
namespace Home\Service ;
use Common\Service\BaseService;

class RoleService implements BaseService{
    private $roleModel = null ;
    
    public function __construct() {
        $this->roleModel = M("Role");
    }
    public function getRoleByCate ($cate){
        return $this->roleModel->where(array(
            'u_icon' => $cate 
        ))->find() ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        return $this->roleModel->data($data)->add() ;        
    }
    public function getUserVIPInfo($user_id){
        $userRoleModel = M("UserLevel");
        
        return $userRoleModel->field(array(
            'u_role.*' ,
            'u_user_level.u_user_id' ,
            'u_user_level.u_create_time'
        ))->join(" left join u_role on u_role.u_id = u_user_level.u_role_id ")->where(array(
            'u_user_level.u_user_id' => $user_id
        ))->select() ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        $userRoleModel = M("UserLevel");
        $list = $userRoleModel->where(array(
            'u_role_id' => $id
        ))->select() ;
        if(!empty($list)){
            return false ;
        }
        return $this->roleModel->where(array(
            'u_id' => $id
        ))->delete() ;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->roleModel->where(array(
            'u_id' => $id
        ))->save($data) ;
    }
    public function addUserVip($data)
    { //根据表注释，用户vip标示u_icon=2
        $userRoleModel = M("UserLevel");
        
        $userRoleModel->where(array(
            'u_user_id' => $data['u_user_id']
        ))->delete() ;
        return $userRoleModel->data($data)->add() ;
    }
    public function getRoleById($id)
    {
        return $this->roleModel->where(array(
            'u_id' => $id
        ))->find() ;
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
        $list = $this->roleModel->where($data)->limit($page,$limit)->select() ;
        $count = $this->roleModel->where($data)->count();
        
        return array('data' => $list,'total' => $count) ;
    }
    
    public function getUserRoleAllList($u_user_id)
    {
        $userRoleModel = M("UserLevel");
        $list = $userRoleModel->where(array(
            'u_user_id' => $u_user_id 
        ))->select() ;
        return $list ;
    }
    public function getRoleAllList()
    {
        $list = $this->roleModel->select() ;
        
        return $list ;
    }
    
}