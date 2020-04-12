<?php
namespace Home\Service ;
use Common\Service\BaseService;

class CalendarService implements BaseService{
    private $calendarModel = null ;
    
    public function __construct() {
        $this->calendarModel = M("Calendar");
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        $old = $this->calendarModel->where(array(
            'u_take_date'=>$data['u_take_date'] ,
            'u_user_id' => $data['u_user_id']
        ))->select() ;
        if(empty($old)){
            return $this->calendarModel->data($data)->add() ;        
        }
        return $old[0]['u_id'] ;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::getList()
     */
    public function getList($data, $page, $limit)
    {
                
    }
     public function calendarOrder()
    {
        return $this->calendarModel->query("SELECT u.*,tmp.num,(SELECT ur.`u_icon` FROM u_user_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_user_id` = u.u_id) as u_user_vip FROM (
            SELECT COUNT(1) AS num,u_user_id FROM u_calendar GROUP BY u_user_id
            ) tmp LEFT JOIN u_user u ON tmp.u_user_id = u.u_id order by tmp.num desc") ;
    }

    public function geCalendartList($u_user_id)
    {
        return $this->calendarModel->where(array(
            'u_user_id' => $u_user_id 
        ))->order('u_create_time desc')->select() ;
    }
    
}