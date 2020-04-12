<?php
namespace Home\Service ;
use Common\Service\BaseService;

class UserWalletService implements BaseService{
    private $userWalletModel = null ;
    
    public function __construct() {
        $this->userWalletModel = M("UserWallet");
    }
    
    public function getTodayNumber($cate,$user_id){
        return $this->userWalletModel->where(array(
            'u_user_id' => $user_id ,
            'u_cate' => $cate ,
            'u_create_time' => array('like' ,'%'.getCurrentDate().'%' )
        ))->count();
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        return $this->userWalletModel->data($data)->add() ;
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
        $list = $this->userWalletModel->where($data)->order('u_create_time desc')->limit($page,$limit)->select() ;
        $count = $this->userWalletModel->where($data)->count();
        
        return array('data' => $list,'total' => $count) ;
    }

    
    
}