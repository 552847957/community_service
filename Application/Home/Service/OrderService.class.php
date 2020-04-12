<?php
namespace Home\Service ;
use Common\Service\BaseService;

class OrderService implements BaseService {
    
    private $orderModel = null ;
    
    public function __construct() {
        $this->orderModel = M("Order");
    }
    
    public function updateOrderLocation($order_location_id,$u_order_id){
        $locationModel = M('OrderLocation');
        $locationModel->where(array(
            'u_order_id' => $u_order_id
        ))->delete() ;
        
        return $locationModel->data(array(
            'u_order_id' => $u_order_id ,
            'u_user_order_location_id' => $order_location_id
        ))->add() ;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        $uuid = $this->orderModel->query("SELECT REPLACE(UUID(), '-', '')") ;
        $data['u_code'] = $uuid[0]["replace(uuid(), '-', '')"] ;
        return $this->orderModel->data($data)->add() ;
    }
    public function getOrderById($u_id)
    {
        $info = $this->orderModel->where(array(
            'u_id' => $u_id
        ))->find() ;
        return $info ;
    }
    public function getOrderByCode($u_code)
    {
        return $this->orderModel->where(array(
            'u_code' => $u_code
        ))->find() ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->orderModel->where(array(
            'u_id' => $id
        ))->delete() ;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->orderModel->where(array(
            'u_id' => $id
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
        $list = $this->orderModel->where($data)->order('u_create_time desc')->limit($page,$limit)->select() ;
        $count = $this->orderModel->where($data)->count();
        
        return array('data' => $list,'total' => $count) ;
    }
    public function getMySaledOrderList($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        $list = $this->orderModel->field(array(
            'u_order.*',
            'u_shop_good.u_name as u_good_name' ,
            'u_shop_good.u_covers as u_good_covers',
            'u_shop_good.u_now_price',
            'u_user_order_location.u_address'
        ))->join(' left join u_shop_good on u_shop_good.u_id = u_order.u_good_id left join u_order_location on u_order_location.u_order_id = u_order.u_id 
            left join u_user_order_location on u_user_order_location.u_id = u_order_location.u_user_order_location_id ')->where($data)->order('u_order.u_create_time desc')->limit($page,$limit)->select() ;
        
        
        for ($i=0;$i<count($list);$i++){
            $list[$i]['u_good_covers'] = explode(',', $list[$i]['u_good_covers']) ;
        }
        
        $count = $this->orderModel->join(' left join u_shop_good on u_shop_good.u_id = u_order.u_good_id ')->where($data)->count();
        
        return array('data' => $list,'total' => $count) ;
    }
    public function getOrderWithGoodList($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        $list = $this->orderModel->field(array(
            'u_order.*',
            'u_shop_good.u_name as u_good_name' ,
            'u_shop_good.u_covers as u_good_covers'
        ))->join(' left join u_shop_good on u_shop_good.u_id = u_order.u_good_id ')->where($data)->order('u_order.u_create_time desc')->limit($page,$limit)->select() ;
        
        for ($i=0;$i<count($list);$i++){
            $list[$i]['u_good_covers'] = explode(',', $list[$i]['u_good_covers']) ;
        }
        
        $count = $this->orderModel->join(' left join u_shop_good on u_shop_good.u_id = u_order.u_good_id ')->where($data)->count();
        
        return array('data' => $list,'total' => $count) ;
    }
}