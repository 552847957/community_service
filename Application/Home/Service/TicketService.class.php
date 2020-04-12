<?php
namespace Home\Service ;
use Common\Service\BaseService;

class TicketService implements BaseService {
    private $ticketModel = null ;
    
    private $userTicketModel = null ;
    
    public function __construct() {
        $this->ticketModel = M("Ticket");
        $this->userTicketModel = M("UserTicket");
    }
    
    public function getUpdateTicketList($u_good_id,$u_user_id){
        return $this->userTicketModel->query("SELECT tu.`u_id` FROM u_user_ticket tu LEFT JOIN u_ticket t ON tu.`u_ticket_id` = t.`u_id` WHERE t.`u_good_id` LIKE '%".$u_good_id."%' AND tu.`u_user_id` = ".$u_user_id) ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        $uuid = $this->ticketModel->query("SELECT REPLACE(UUID(), '-', '')") ;
        $data['u_code'] = $uuid[0]["replace(uuid(), '-', '')"] ;
        return $this->ticketModel->data($data)->add() ;        
    }
    public function addUserTicket($data)
    {
        $info = $this->userTicketModel->where($data)->select() ;
        if(!empty($info)){
            return true ;
        }
        
        return $this->userTicketModel->data($data)->add() ;
    }
    
    public function getTicketById($id)
    {
        return $this->ticketModel->where(array(
            'u_id' => $id
        ))->find() ;
    }
    public function getTicketByGoodId($u_good_id)
    {
        return $this->ticketModel->where(array(
            'u_good_id' => array('like' ,'%'.$u_good_id.'%' )
        ))->select() ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->ticketModel->where(array(
            'u_id' => $id
        ))->delete() ;     
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->ticketModel->where(array(
            'u_id' => $id
        ))->save($data) ;  
        
    }
    public function updateUserTicket($data, $id)
    {
        return $this->userTicketModel->where(array(
            'u_id' => $id
        ))->save($data) ;
        
    }
    public function updateUserTicketByUserIDTicketID($u_user_id,$ticket_id)
    {
        return $this->userTicketModel->where(array(
            'u_user_id' => $u_user_id ,
            'u_ticket_id' => $ticket_id
        ))->save(array(
            'u_used' => 1
        )) ;
        
    }
    
    public function updateUserTicketByUserIDUID($u_user_id,$u_id)
    {
        return $this->userTicketModel->where(array(
            'u_user_id' => $u_user_id ,
            'u_id' => $u_id
        ))->save(array(
            'u_used' => 1
        )) ;
        
    }
    public function getListWithSession($user_id,$good_id)
    {
        $list = $this->userTicketModel->field(array(
            'u_user_ticket.*',
            'u_ticket.u_name' ,
            'u_ticket.u_limit_time' ,
            'u_ticket.u_num' ,
            'u_ticket.u_code',
            'u_ticket.u_limit_num'
        ))->join(' join u_ticket on u_ticket.u_id = u_user_ticket.u_ticket_id ')->where(array(
            'u_user_ticket.u_user_id' => $user_id ,
            'u_ticket.u_good_id' => array('like' ,'%'.$good_id.'%' )
        ))->order('u_user_ticket.u_create_time desc')->select() ;
        
        
        for ($i=0;$i<count($list);$i++){
            $limit_time_arr = explode(' ~ ', $list[$i]['u_limit_time']) ;
            if(strtotime(getCurrentDate()) - strtotime($limit_time_arr[1]) <= 0){ //当前时间小于截止日期
                $list[$i]['u_state'] = 0 ;
            }else{
                $list[$i]['u_state'] = 1 ;
            }
        }
        return $list ;
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
        $list = $this->ticketModel->field(array(
            'u_ticket.*',
            '(SELECT COUNT(1) FROM u_user_ticket WHERE u_ticket_id = u_ticket.u_id AND u_user_id = '.$data['u_user_ticket.u_user_id'].') as is_get '
        ))->order('u_ticket.u_create_time desc')->limit($page,$limit)->select() ;
        $count = $this->ticketModel->count();
        
        return array('data' => $list,'total' => $count) ;
    }
    public function getListWithOut($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        $list = $this->ticketModel->where($data)->order('u_create_time desc')->limit($page,$limit)->select() ;
        $count = $this->ticketModel->where($data)->count();
        
        return array('data' => $list,'total' => $count) ;
    }
    
    public function getUserTicketList($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        $list = $this->userTicketModel->field(array(
            'u_user_ticket.*',
            'u_ticket.u_name' ,
            'u_ticket.u_limit_time' ,
            'u_ticket.u_num' ,
            'u_ticket.u_code' ,
            'u_ticket.u_good_id' 
        ))->join(' join u_ticket on u_ticket.u_id = u_user_ticket.u_ticket_id ')->where($data)->order('u_user_ticket.u_create_time desc')->limit($page,$limit)->select() ;
      
        
        for ($i=0;$i<count($list);$i++){
            $limit_time_arr = explode(' ~ ', $list[$i]['u_limit_time']) ;
            if(strtotime(getCurrentDate()) - strtotime($limit_time_arr[1]) <= 0){ //当前时间小于截止日期
                $list[$i]['u_state'] = 0 ;
            }else{
                $list[$i]['u_state'] = 1 ;
            }
        }
        
        
        $count = $this->userTicketModel->join(' join u_ticket on u_ticket.u_id = u_user_ticket.u_ticket_id ')->where($data)->count();
        
        return array('data' => $list,'total' => $count) ;
    }
}