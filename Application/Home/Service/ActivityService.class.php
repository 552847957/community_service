<?php
namespace Home\Service ;
use Common\Service\BaseService;

class ActivityService implements BaseService {
    private $activityModel = null ;
    
    public function __construct() {
        $this->activityModel = M("Activity");
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        return $this->activityModel->data($data)->add() ;        
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->activityModel->where(array(
            'u_id' => $id
        ))->delete() ;        
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->activityModel->where(array(
            'u_id' => $id
        ))->save($data) ;  
    }
    public function getNewActivity (){
      /*   $list = S('getNewActivity') ;
        if($list){ */
            $list = $this->activityModel->order('u_create_time desc')->limit(3)->select();
          /*   S('getNewActivity',$list,1000) ;
        } */
        return  $list ;
    }
    public function getActivityById ($id){
        return $this->activityModel->where(array(
            'u_id' => $id
        ))->find() ;
    }
    
    public function setIncView($field,$id){
        return $this->activityModel->where(array(
            'u_id' => $id
        ))->setInc($field,1) ;
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
        $list = $this->activityModel->where($data)->order('u_create_time desc')->limit($page,$limit)->select() ;
        $count = $this->activityModel->where($data)->count();
        
        return array('data' => $list,'total' => $count) ;
    }

    
}