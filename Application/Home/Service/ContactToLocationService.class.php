<?php
namespace Home\Service ;
use Common\Service\BaseService;

class ContactToLocationService implements  BaseService{
    
    private $contactToLocationModel = null ;
    
    public function __construct() {
        $this->contactToLocationModel = M("ContactToLocation");
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        return $this->contactToLocationModel->data($data)->add() ;        
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->contactToLocationModel->where(array(
            'u_id' => $id
        ))->delete() ;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->contactToLocationModel->where(array(
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
        
        $list = $this->contactToLocationModel->where($data)->limit($page,$limit)->select() ;
        $count = $this->contactToLocationModel->where($data)->count();
        return array('data' => $list,'total' => $count) ;
    }

    
}