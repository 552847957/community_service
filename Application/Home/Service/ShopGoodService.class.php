<?php
namespace Home\Service ;
use Common\Service\BaseService;

class ShopGoodService implements BaseService {
    private $goodModel = null ;
    
    public function __construct() {
        $this->goodModel = M("ShopGood");
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        return $this->goodModel->data($data)->add() ;
    }
    public function setInc($u_id,$number){
        $this->goodModel->where(array('u_id'=>$u_id))->setDec('u_stores',$number) ;
        return $this->goodModel->where(array('u_id'=>$u_id))->setInc('u_sales',$number) ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->goodModel->where(array(
            'u_id' => $id
        ))->delete() ;
    }
    public function updateGoodView($field, $id)
    {
        return $this->goodModel->where(array(
            'u_id' => $id
        ))->setInc($field,1) ;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->goodModel->where(array(
            'u_id' => $id
        ))->save($data) ;
    }
    public function getGoodInfoById($u_id){
        $info = $this->goodModel->where(array('u_id'=>$u_id))->find() ;
        if(empty($info)){
            return null ;
        }
        $u_covers = explode(',', $info['u_covers']) ;
        $info['u_covers'] = $u_covers ;
        $info['u_main_cover'] = $u_covers[0] ;
        
        //获取商品的促销政策
        $ticketService = new TicketService() ;
        $ticketList = $ticketService->getTicketByGoodId($u_id) ;
        $arr = array() ;
        for ($i=0;$i<count($ticketList);$i++){
            array_push($arr, $ticketList[$i]['u_name']) ;
        }
        $info['ticket_names'] = $arr ;
        return $info ;
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
        
        $list = $this->goodModel->field(array(
            'u_name','u_now_price','u_sales','u_covers','u_id','u_specs'
        ))->where($data)->order('u_create_time desc')->limit($page,$limit)->select();
        for ($i=0;$i<count($list);$i++){
            $u_covers = $list[$i]['u_covers'] ;
            $u_covers = explode(',', $u_covers) ;
            $list[$i]['u_main_cover'] = $u_covers[0] ;
        }
        $count = $this->goodModel->where($data)->count();
        return array('data' => $list,'total' => $count) ;
    }
    public function getUserGoodList($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->goodModel->query("SELECT sg.*,u.u_nick_name FROM u_shop_good sg left join u_user u on u.u_id = sg.u_user_id WHERE sg.`u_source` = 'user' AND sg.`u_user_id` 
        = ".$data['u_user_id']." order by sg.u_create_time desc limit ".$page.','.$limit) ;
        for ($i=0;$i<count($list);$i++){
            $u_covers = $list[$i]['u_covers'] ;
            $u_covers = explode(',', $u_covers) ;
            $list[$i]['u_main_cover'] = $u_covers[0] ;
        }
        $count = $this->goodModel->query("SELECT count(1)  FROM u_shop_good sg left join u_user u on u.u_id = sg.u_user_id WHERE sg.`u_source` = 'user' AND sg.`u_user_id` 
        = ".$data['u_user_id']);
        
        return array('data' => $list,'total' => $count[0]['count(1)']) ;
    }
    public function getUserGoodWithHouseList($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->goodModel->query("SELECT sg.*,u.u_nick_name FROM u_shop_good sg left join u_user u on u.u_id = sg.u_user_id WHERE sg.`u_ok` = '0' AND sg.`u_source` = 'user' AND sg.`u_user_id` IN (
        SELECT u_user_id FROM u_user_house uh WHERE uh.`u_house_id` = ".$data['u_house_id'].") order by sg.u_create_time desc limit ".$page.','.$limit) ;
        
        
        for ($i=0;$i<count($list);$i++){
            $u_covers = $list[$i]['u_covers'] ;
            $u_covers = explode(',', $u_covers) ;
            $list[$i]['u_main_cover'] = $u_covers[0] ;
        }
        $count = $this->goodModel->query("SELECT count(1) FROM u_shop_good sg WHERE sg.`u_ok` = '0' AND sg.`u_source` = 'user' AND sg.`u_user_id` IN (
            SELECT u_user_id FROM u_user_house uh WHERE uh.`u_house_id` = ".$data['u_house_id'].")");
        return array('data' => $list,'total' => $count[0]['count(1)']) ;
    }
    public function getListWhitOut($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->goodModel->where($data)->order('u_create_time desc')->limit($page,$limit)->select();
        for ($i=0;$i<count($list);$i++){
            $u_covers = $list[$i]['u_covers'] ;
            $u_covers = explode(',', $u_covers) ;
            $list[$i]['u_main_cover'] = $u_covers[0] ;
        }
        
        $count = $this->goodModel->where($data)->count();
        return array('data' => $list,'total' => $count) ;
    }
    
}