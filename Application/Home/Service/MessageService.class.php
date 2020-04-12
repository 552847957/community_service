<?php
namespace Home\Service ;
use Common\Service\BaseService;

class MessageService implements BaseService {
    private $msgModel = null ;
    
    public function __construct() {
        $this->msgModel = M("Message");
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        return $this->msgModel->data($data)->add() ;        
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        // TODO Auto-generated method stub
        
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
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        $list = $this->msgModel->where($data)->limit($page,$limit)->select() ;
        $count = $this->msgModel->where($data)->count();
        
        return array('data' => $list,'total' => $count) ;
    }

    
}