<?php
namespace Home\Service ;
use Common\Service\BaseService;

class NoticeService implements BaseService{
    private $noticeModel = null ;
    
    public function __construct() {
        $this->noticeModel = M("Notice");
    }
    public function getNewNotice(){
        return $this->noticeModel->where(array(
            'u_public' => 0
        ))->order("u_create_time desc")->find();
    }
    public function getNewHouseNotice($u_house_id){
        return $this->noticeModel->where(array(
            'u_public' => 1 ,
            'u_house_id' => $u_house_id
        ))->order("u_create_time desc")->find();
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        return $this->noticeModel->data($data)->add() ;        
    }
    public function getNoticePushList (){
        return $this->noticeModel->field(array(
            'u_house_id' ,
            'u_id'
        ))->select() ;
    }
    public function getNoticeById ($id){
        return $this->noticeModel->where(array(
            'u_id' => $id
        ))->find() ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->noticeModel->where(array(
            'u_id' => $id
        ))->delete() ;        
    }

    public function getNoticeNumByHouse($data)
    {
        return $this->noticeModel->where($data)->count() ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->noticeModel->where(array(
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
        $list = $this->noticeModel->where($data)->limit($page,$limit)->select() ;
    
        $count = $this->noticeModel->where($data)->count();
        return array('data' => $list,'total' => $count) ;
    }

    
    public function addNoticePush($data)
    {
        $this->noticeModel->startTrans() ;
        try {
            $this->noticeModel->where(array(
                'u_id' => $data['u_id']
            ))->save(array(
                'u_public' => 1
            )) ;
            
            $checks = explode(",", $data['u_house_ids']) ;
            for ($i=0;$i<count($checks);$i++){
                if(!empty($checks[$i])){
                    $tmpData = array(
                        'u_house_id' => $checks[$i]
                    ) ;
                    $id = $this->noticeModel->where(array(
                        'u_id' => $data['u_id']
                    ))->save($tmpData) ;
                }
            }
            return true;
            
            $this->noticeModel->commit() ;
        } catch (\Exception $e) {
            $this->noticeModel->rollback() ;
        }
        return false ;
    }
}