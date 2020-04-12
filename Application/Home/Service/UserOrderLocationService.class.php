<?php
namespace Home\Service ;
use Common\Service\BaseService;

class UserOrderLocationService implements BaseService{
    private $userOrderLocationModel = null ;
    
    public function __construct() {
        $this->userOrderLocationModel = M("UserOrderLocation");
    }
    public function getLocationInfoByOrderID($order_id){
        return $this->userOrderLocationModel->query("SELECT uuol.* FROM u_order_location uol LEFT JOIN u_user_order_location uuol ON uol.`u_user_order_location_id` = uuol.`u_id`
            WHERE uol.`u_order_id` = ".$order_id) ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        if(!empty($data['u_default']) && $data['u_default'] == '1'){
            $this->userOrderLocationModel->where(array(
                'u_user_id' => $data['u_user_id']
            ))->save(array(
                'u_default' => 0
            )) ;
        }
        return $this->userOrderLocationModel->data($data)->add() ;
        
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->userOrderLocationModel->where(array(
            'u_id' => $id
        ))->delete() ;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        if(!empty($data['u_default']) && $data['u_default'] == '1'){
            $this->userOrderLocationModel->where(array(
                'u_user_id' => $data['u_user_id']
            ))->save(array(
                'u_default' => 0
            )) ;
        }
        return $this->userOrderLocationModel->where(array(
            'u_id' => $id
        ))->save($data) ;
    }
    public function getUserOrderLocationById($id)
    {
        return $this->userOrderLocationModel->where(array(
            'u_id' => $id
        ))->find() ;
    }
    
    public function getUserOrderDefaultLocation($id)
    {
        return $this->userOrderLocationModel->where(array(
            'u_user_id' => $id ,
            'u_default' => 1
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
        $list = $this->userOrderLocationModel->where($data)->order('u_default desc')->limit($page,$limit)->select() ;
        $count = $this->userOrderLocationModel->where($data)->count();
        
        return array('data' => $list,'total' => $count) ;
    }

    
}