<?php
namespace Home\Service ;
use Common\Service\BaseService;

class WorkerService implements BaseService{
    
    private $workerModel = null ;
    private $workerCommentModel = null ;
    public function __construct(){
        
        $this->workerModel = M("Worker");
        $this->workerCommentModel = M("WorkerComment");
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        $info = $this->workerModel->where(array(
            'u_cate_code' => $data['u_cate_code'] ,
            'u_phone'=>$data['u_phone'],
            'u_bind_house_id'=>$data['u_bind_house_id']
        ))->select() ;
        if(!empty($info)){
            return 0 ;
        }
        return $this->workerModel->data($data)->add() ;        
    }
    public function getWorkerCommentInfoByID($id){
        return $this->workerCommentModel->field(array(
            'u_worker_comment.*',
            'u_user.u_open_id'
        ))->join(" left join u_user on u_user.u_id = u_worker_comment.u_user_id ")->where(array(
            'u_worker_comment.u_id' => $id
        ))->find() ;
    }
    public function delWorkerComment($id)
    {
        $info = $this->workerCommentModel->where(array('u_id'=>$id))->find() ;
        $this->workerCommentModel->where(array('u_parent_id'=>$id))->delete() ;
        return $this->workerCommentModel->where(array('u_id'=>$id))->delete() ;
    }
    public function addComment($data)
    {
        return $this->workerCommentModel->data($data)->add() ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->workerModel->where(array(
            'u_id' => $id
        ))->delete() ;        
    }
    
    public function getWorkerInfoById($id){
        return $this->workerModel->field(array(
            'u_cate.u_name as u_cate_name' ,
            'u_worker.*' ,
            '(SELECT ur.`u_icon` FROM u_worker_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_worker_id` = u_worker.u_id) as u_user_vip'
        ))->join(" left join u_cate on u_cate.u_code = u_worker.u_cate_code ")->where(array(
            'u_worker.u_id' => $id
        ))->find() ;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->workerModel->where(array(
            'u_id' => $id
        ))->save($data) ;        
    }
    
    public function updateWorkerCard($data, $id)
    {
        return $this->workerModel->where(array(
            'u_id' => $id
        ))->save($data) ;
    }
    private function getSubTree($parent_list) {
        foreach ($parent_list as $key => $value) {
            $chid_list = $this->workerCommentModel->field(array(
                'u_worker_comment.*',
                'u_user.u_nick_name'
            ))->join(" left join u_user on u_worker_comment.u_user_id = u_user.u_id ")->where(array(
                'u_worker_comment.u_parent_id' => $value['u_id']
            ))->select() ;
            $chid_list = convertEMJ($chid_list) ;
            $parent_list[$key]['childs'] = $chid_list ;
        }
        return $parent_list;
    }
    public function getCommentList($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->workerCommentModel->query("SELECT wc.*,u.`u_icon` AS u_user_icon ,u.`u_nick_name`,(SELECT ur.`u_icon` FROM u_user_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_user_id` = u.u_id) as u_user_vip  FROM u_worker_comment wc LEFT JOIN u_user u ON wc.`u_user_id` = u.`u_id`
WHERE wc.u_parent_id = 0 and wc.`u_worker_id` = ".$data['u_worker_id']." ORDER BY wc.`u_create_time` DESC LIMIT ".$page.",".$limit) ;
        
        
        $list = convertEMJ($list) ;
        
        $list = $this->getSubTree($list) ;
        
        
        $count = $this->workerCommentModel->query("SELECT count(1)  FROM u_worker_comment wc LEFT JOIN u_user u ON wc.`u_user_id` = u.`u_id`
WHERE wc.u_parent_id = 0 and wc.`u_worker_id` = ".$data['u_worker_id']." ORDER BY wc.`u_create_time` DESC") ;
        return array('data' => $list,'total' => $count[0]['count(1)']) ;
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
        
        $list = $this->workerModel->field(array(
            'u_worker.*' ,
            '(SELECT ur.`u_icon` FROM u_worker_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_worker_id` = u_worker.u_id) as u_user_vip'
        ))->where(array(
            'u_worker.u_cate_code' => $data['u_cate_code'] ,
            'u_worker.u_bind_house_id' => $data['u_bind_house_id']
        ))->limit($page,$limit)->select() ;
        $count = $this->workerModel->where(array(
            'u_cate_code' => $data['u_cate_code'] ,
            'u_bind_house_id' => $data['u_bind_house_id']
        ))->count(); 
        
        return array('data' => $list,'total' => $count) ;
    }
    
    
    public function getListByParam($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->workerModel->where($data)->limit($page,$limit)->select() ;
        $count = $this->workerModel->where($data)->count();
        return array('data' => $list,'total' => $count) ;
    }
    
}