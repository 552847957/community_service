<?php
namespace Home\Service ;
use Common\Service\BaseService;

class TopicService implements BaseService {
    private $topicModel = null ;
    
    public function __construct() {
        $this->topicModel = M("Topic");
    }
    
    public function getTopicInfoById($u_id)
    {
        $trendService = new TrendService() ;
        $count = $trendService->getCommentsByTopic($u_id) ;
        $info = $this->topicModel->where(array(
            'u_id' => $u_id
        ))->find() ;
        $info['u_comments'] = $count ;
        return $info ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        return $this->topicModel->data($data)->add() ;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->topicModel->where(array(
            'u_id' => $id
        ))->delete();
    }
    public function incField($field, $id)
    {
        return $this->topicModel->where(array(
            'u_id' => $id
        ))->setInc($field,1) ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->topicModel->where(array(
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
        $list = $this->topicModel->where($data)->order('u_create_time desc')->limit($page,$limit)->select();
        $count = $this->topicModel->where($data)->order('u_create_time desc')->count();
        return array('data' => $list,'total' => $count) ;
    }
}