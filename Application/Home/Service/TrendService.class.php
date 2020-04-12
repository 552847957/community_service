<?php
namespace Home\Service ;
use Common\Service\BaseService;

class TrendService implements BaseService{
    
    private $trendModel = null ;
    private $trendCommentModel = null ;
    public function __construct(){
        
        $this->trendModel = M("Trend");
        $this->trendCommentModel = M("TrendComment");
    }

    public function getCommentsByTopic($topic_id){
        $count = $this->trendCommentModel->query("SELECT COUNT(1) FROM u_trend_comment tc LEFT JOIN u_trend e ON tc.`u_trend_id` = e.`u_id`
            WHERE e.`u_topic_id` = ".$topic_id) ;
        return $count[0]['count(1)'];
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        return $this->trendModel->data($data)->add() ;        
    }
    public function getTrendCommentInfoByID($id){
        return $this->trendCommentModel->field(array(
            'u_trend_comment.*',
            'u_user.u_open_id'
        ))->join(" left join u_user on u_user.u_id = u_trend_comment.u_user_id ")->where(array(
            'u_trend_comment.u_id' => $id
        ))->find() ;
    }
    public function addComment($data)
    {
        return $this->trendCommentModel->data($data)->add() ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->trendModel->where(array('u_id'=>$id))->delete() ;        
    }
    public function deleteComment($id)
    {
        $info = $this->trendCommentModel->where(array('u_id'=>$id))->find() ;
        $this->trendCommentModel->where(array('u_parent_id'=>$id))->delete() ;
        return $this->trendCommentModel->where(array('u_id'=>$id))->delete() ;
    }
    public function getTrendInfoById($id)
    {
        $info = $this->trendModel->query("SELECT p.u_name as u_topic_name,e.*,u.`u_icon` AS u_user_icon ,u.`u_nick_name`,(SELECT ur.`u_icon` FROM u_user_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_user_id` = u.u_id) as u_user_vip  ,(SELECT COUNT(1) FROM u_trend_comment tc WHERE tc.u_trend_id = e.u_id) AS u_comments  
            FROM u_trend e LEFT JOIN u_user u ON e.`u_user_id` = u.`u_id`
            left join u_topic p on e.u_topic_id = p.u_id
            WHERE e.u_id = ".$id)[0] ;
        $info['u_imgs'] = explode(",", $info['u_imgs']) ;
        
        
        $content = $info['u_content'] ;
        if(strpos($content,'[em_') !==false){ //包含
            $arr = explode("[em_", $content) ;
            for ($j=0;$j<count($arr);$j++){
                if(strpos($arr[$j],']') !==false){
                    $arrSub = explode("]", $arr[$j]) ;
                    $arrSub[0] = '<img src="'.C('COS_ACCESS_BASE_URL').'bkhumor-emoji/'.$arrSub[0].'.gif" align="middle">' ;
                    $arr[$j] = $arrSub[0].$arrSub[1] ;
                }else{
                    continue ;
                }
            }
            $info['u_content'] = implode('', $arr) ;
        }
        
        return $info ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->trendModel->where(array('u_id'=>$id))->save($data) ;        
    }
    public function updateView( $id)
    {
        $info = $this->getTrendInfoById($id) ;
        if(!empty($info['u_topic_id'])){
            $topicService = new TopicService() ;
            $topicService->incField('u_views', $info['u_topic_id']);
        }
        return $this->trendModel->where(array(
            'u_id' => $id
        ))->setInc('u_views',1) ;        
    }
    public function getMyTrendList($data, $page, $limit){
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->trendModel->query("SELECT p.u_name as u_topic_name,e.*,u.`u_icon` AS u_user_icon ,u.`u_nick_name`,(SELECT ur.`u_icon` FROM u_user_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_user_id` = u.u_id) as u_user_vip ,(SELECT COUNT(1) FROM u_trend_comment tc WHERE tc.u_trend_id = e.u_id) AS u_comments  
            FROM u_trend e LEFT JOIN u_user u ON e.`u_user_id` = u.`u_id`
            left join u_topic p on e.u_topic_id = p.u_id
            WHERE e.`u_user_id` = ".$data['u_user_id']." ORDER BY e.`u_create_time` DESC limit ".$page.",".$limit);
        
        
        for ($i=0;$i<count($list);$i++){
            $list[$i]['u_imgs'] = explode(",", $list[$i]['u_imgs']) ;
            $content = $list[$i]['u_content'] ;
            if(strpos($content,'[em_') !==false){ //包含
                $arr = explode("[em_", $content) ;
                for ($j=0;$j<count($arr);$j++){
                    if(strpos($arr[$j],']') !==false){
                        $arrSub = explode("]", $arr[$j]) ;
                        $arrSub[0] = '<img src="'.C('COS_ACCESS_BASE_URL').'bkhumor-emoji/'.$arrSub[0].'.gif" align="middle">' ;
                        $arr[$j] = $arrSub[0].$arrSub[1] ;
                    }else{
                        continue ;
                    }
                }
                $list[$i]['u_content'] = implode('', $arr) ;
            }else{
                continue ;
            }
        }
        
        
        $count = $this->trendModel->query("SELECT count(1)  FROM u_trend e LEFT JOIN u_user u ON e.`u_user_id` = u.`u_id`
WHERE e.`u_user_id` = ".$data['u_user_id']." ORDER BY e.`u_create_time` DESC ");
        
        return array('data' => $list,'total' => $count[0]['count(1)']) ;
    }
    /**
     * 模糊查询挂链话题的帖子
     * @return unknown[]|array[]
     */
    public function getTopicTrendList($data, $page, $limit){
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->trendModel->query("SELECT e.*,u.`u_icon` AS u_user_icon ,u.`u_nick_name` ,(SELECT ur.`u_icon` FROM u_user_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_user_id` = u.u_id) as u_user_vip ,(SELECT COUNT(1) FROM u_trend_comment tc WHERE tc.u_trend_id = e.u_id) AS u_comments  FROM u_trend e LEFT JOIN u_user u ON e.`u_user_id` = u.`u_id`
            WHERE e.`u_content` like '%".$data['u_content']."%' ORDER BY e.`u_create_time` DESC limit ".$page.",".$limit);
        
        
        for ($i=0;$i<count($list);$i++){
            $list[$i]['u_imgs'] = explode(",", $list[$i]['u_imgs']) ;
            $content = $list[$i]['u_content'] ;
            if(strpos($content,'[em_') !==false){ //包含
                $arr = explode("[em_", $content) ;
                for ($j=0;$j<count($arr);$j++){
                    if(strpos($arr[$j],']') !==false){
                        $arrSub = explode("]", $arr[$j]) ;
                        $arrSub[0] = '<img src="'.C('COS_ACCESS_BASE_URL').'bkhumor-emoji/'.$arrSub[0].'.gif" align="middle">' ;
                        $arr[$j] = $arrSub[0].$arrSub[1] ;
                    }else{
                        continue ;
                    }
                }
                $list[$i]['u_content'] = implode('', $arr) ;
            }else{
                continue ;
            }
        }
        
        $count = $this->trendModel->query("SELECT count(1)  FROM u_trend e LEFT JOIN u_user u ON e.`u_user_id` = u.`u_id`
            WHERE e.`u_content` like '%".$data['u_content']."%' ORDER BY e.`u_create_time` DESC ");
        
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
        
       $list = $this->trendModel->query("SELECT p.u_name as u_topic_name,e.*,u.`u_icon` AS u_user_icon ,u.`u_nick_name` ,(SELECT ur.`u_icon` FROM u_user_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_user_id` = u.u_id) as u_user_vip ,(SELECT COUNT(1) FROM u_trend_comment tc WHERE tc.u_trend_id = e.u_id) AS u_comments  
            FROM u_trend e LEFT JOIN u_user u ON e.`u_user_id` = u.`u_id`
            left join u_topic p on e.u_topic_id = p.u_id
            WHERE e.`u_house_id` = ".$data['u_house_id']." ORDER BY e.`u_create_time` DESC limit ".$page.",".$limit);
       
        
        for ($i=0;$i<count($list);$i++){
            $list[$i]['u_imgs'] = explode(",", $list[$i]['u_imgs']) ;
            $content = $list[$i]['u_content'] ;
            if(strpos($content,'[em_') !==false){ //包含
                $arr = explode("[em_", $content) ;
                for ($j=0;$j<count($arr);$j++){
                    if(strpos($arr[$j],']') !==false){
                        $arrSub = explode("]", $arr[$j]) ;
                        $arrSub[0] = '<img src="'.C('COS_ACCESS_BASE_URL').'bkhumor-emoji/'.$arrSub[0].'.gif" align="middle">' ;
                        $arr[$j] = $arrSub[0].$arrSub[1] ;
                    }else{
                        continue ;
                    }
                }
                $list[$i]['u_content'] = implode('', $arr) ;
            }else{
                continue ;
            }
        }
        
        
        $count = $this->trendModel->query("SELECT count(1)  FROM u_trend e LEFT JOIN u_user u ON e.`u_user_id` = u.`u_id`
            left join u_topic p on e.u_topic_id = p.u_id
            WHERE e.`u_house_id` = ".$data['u_house_id']." ORDER BY e.`u_create_time` DESC "); 
        
        
        
        return array('data' => $list,'total' => $count[0]['count(1)']) ;
    }
    
    public function getListWithOutHouse ($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->trendModel->query("SELECT h.u_name as u_house_name,e.*,u.`u_icon` AS u_user_icon ,u.`u_nick_name`,u.u_name as u_user_real_name,(SELECT ur.`u_icon` FROM u_user_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_user_id` = u.u_id) as u_user_vip  ,(SELECT COUNT(1) FROM u_trend_comment tc WHERE tc.u_trend_id = e.u_id) AS u_comments  FROM u_trend e LEFT JOIN u_user u ON e.`u_user_id` = u.`u_id`
            left join u_house h on e.u_house_id = h.u_id
            where e.u_content like '%".$data['u_name']."%'
            ORDER BY e.`u_create_time` DESC limit ".$page.",".$limit);
        
        
        for ($i=0;$i<count($list);$i++){
            $list[$i]['u_imgs'] = explode(",", $list[$i]['u_imgs']) ;
            $content = $list[$i]['u_content'] ;
            if(strpos($content,'[em_') !==false){ //包含
                $arr = explode("[em_", $content) ;
                for ($j=0;$j<count($arr);$j++){
                    if(strpos($arr[$j],']') !==false){
                        $arrSub = explode("]", $arr[$j]) ;
                        $arrSub[0] = '<img src="'.C('COS_ACCESS_BASE_URL').'bkhumor-emoji/'.$arrSub[0].'.gif" align="middle">' ;
                        $arr[$j] = $arrSub[0].$arrSub[1] ;
                    }else{
                        continue ;
                    }
                }
                $list[$i]['u_content'] = implode('', $arr) ;
            }else{
                continue ;
            }
        }
        
        
        $count = $this->trendModel->query("SELECT count(1)  FROM u_trend e LEFT JOIN u_user u ON e.`u_user_id` = u.`u_id`
            left join u_house h on e.u_house_id = h.u_id   
             where e.u_content like '%".$data['u_name']."%'
            ORDER BY e.`u_create_time` DESC ");
        
        
        
        return array('data' => $list,'total' => $count[0]['count(1)']) ;
    }
    
    /**
     * 获取关联话题的帖子评论数排行版
     * @param unknown $data
     * @param unknown $page
     * @param unknown $limit
     * @return mixed[]
     */
    public function getTrendUserOrderList($data, $page, $limit){
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->trendModel->query("SELECT * FROM (
            SELECT * FROM (
            SELECT u.*,t.u_id as u_trend_id ,(
            SELECT COUNT(1) FROM u_trend_comment WHERE u_trend_id = t.`u_id`
            ) AS comments,(SELECT ur.`u_icon` FROM u_user_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_user_id` = u.u_id) as u_user_vip FROM u_trend t
            LEFT JOIN u_user u ON t.`u_user_id` = u.u_id
             WHERE t.`u_content` LIKE '%".$data['u_content']."%'  GROUP BY t.u_id
            ) tt ORDER BY tt.comments DESC
            ) ttt GROUP BY ttt.u_id limit ".$page.','.$limit) ;
         
        $count = $this->trendModel->query("SELECT count(1) FROM (
            SELECT * FROM (
            SELECT u.* ,(
            SELECT COUNT(1) FROM u_trend_comment WHERE u_trend_id = t.`u_id`
            ) AS comments FROM u_trend t
            LEFT JOIN u_user u ON t.`u_user_id` = u.u_id
             WHERE t.`u_content` LIKE '%".$data['u_content']."%'  GROUP BY t.u_id
            ) tt ORDER BY tt.comments DESC
            ) ttt GROUP BY ttt.u_id ") ;
        return array('data' => $list,'total' => $count[0]['count(1)']) ;
    }
    
    /**
     * @param $data array  数据
     * @param $parent  string 父级元素的名称 如 parent_id
     * @param $son     string 子级元素的名称 如 comm_id
     * @param $pid     int    父级元素的id 实际上传递元素的主键
     * @return array
     */
    private function getSubTree($parent_list) {
        foreach ($parent_list as $key => $value) {
            $chid_list = $this->trendCommentModel->field(array(
                'u_trend_comment.*',
                'u_user.u_nick_name'
            ))->join(" left join u_user on u_trend_comment.u_user_id = u_user.u_id ")->where(array(
                'u_trend_comment.u_parent_id' => $value['u_id']
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
        
        $list = $this->trendCommentModel->query("SELECT tc.*,u.u_icon as u_user_icon,u.u_nick_name,(SELECT ur.`u_icon` FROM u_user_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_user_id` = u.u_id) as u_user_vip  FROM u_trend_comment tc LEFT JOIN u_user u ON tc.`u_user_id` = u.`u_id`
            WHERE tc.u_parent_id = 0 and tc.`u_trend_id` = ".$data['u_trend_id']." ORDER BY tc.`u_create_time` DESC LIMIT ".$page.",".$limit) ;
        
        $list = convertEMJ($list) ;
        
        $list = $this->getSubTree($list) ;
        
        $count = $this->trendModel->query("SELECT count(1) FROM u_trend_comment tc LEFT JOIN u_user u ON tc.`u_user_id` = u.`u_id`
            WHERE tc.u_parent_id = 0 and tc.`u_trend_id` = ".$data['u_trend_id']." ORDER BY tc.`u_create_time` DESC");
        return array('data' => $list,'total' => $count[0]['count(1)']) ;
    }
    
}