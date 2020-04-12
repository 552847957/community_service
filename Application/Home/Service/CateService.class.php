<?php
namespace Home\Service ;
use Common\Service\BaseService;

class CateService implements BaseService {
    private $cateModel = null ;
    
    public function __construct() {
        $this->cateModel = M("Cate");    
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        return $this->cateModel->data($data)->add() ;
    }
    
    public function getCateInfoById($id)
    {
        return $this->cateModel->where(array(
            'u_id' => $id
        ))->find() ;
    }
    public function getCateInfoByCode($code)
    {
        return $this->cateModel->where(array(
            'u_code' => $code
        ))->find() ;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->cateModel->where(array(
            'u_id' => $id
        ))->delete() ;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->cateModel->where(array(
            'u_id' => $id
        ))->save($data) ;        
    }
    
    public function getCateList(){
        return $this->cateModel->order("u_order")->select() ;
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
        $list = $this->cateModel->where($data)->limit($page,$limit)->select() ;
        $count = $this->cateModel->where($data)->count();
        
        return array('data' => $list,'total' => $count) ;
    }

}