<?php
namespace Home\Service ;
use Common\Service\BaseService;

class GlobalContactService implements BaseService{
    private $globalContactModel = null ;
    private $globalCateModel = null ;
    private $contactLocationModel = null ;
    private $contactCommentModel = null ;
    public function __construct() {
        $this->globalContactModel = M("GlobalContact");
        $this->globalCateModel = M("GlobalCate");
        $this->contactLocationModel = M("ContactToLocation") ;
        $this->contactCommentModel = M("GlobalContactComment");
    }
    public function addComment($data){
        return $this->contactCommentModel->data($data)->add() ;
    }
    
    public function setIncBusinessViews($id){
        $model = M('GlobalContactExtra');
        return $model->where(array(
            'u_global_contact_id' => $id
        ))->setInc("u_views",1) ;
    }
    public function getBusinessCommentInfoByID($id){
        return $this->contactCommentModel->field(array(
            'u_global_contact_comment.*',
            'u_user.u_open_id'
        ))->join(" left join u_user on u_user.u_id = u_global_contact_comment.u_user_id ")->where(array(
            'u_global_contact_comment.u_id' => $id
        ))->find() ;
    }
    public function deleteComment($id)
    {
        $info = $this->contactCommentModel->where(array('u_id'=>$id))->find() ;
        $this->contactCommentModel->where(array('u_parent_id'=>$id))->delete() ;
        return $this->contactCommentModel->where(array('u_id'=>$id))->delete() ;
    }
    
    public function getCommentList($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->contactCommentModel->query("SELECT tc.*,u.u_icon as u_user_icon,u.u_nick_name,(SELECT ur.`u_icon` FROM u_user_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_user_id` = u.u_id) as u_user_vip  
            FROM u_global_contact_comment tc LEFT JOIN u_user u ON tc.`u_user_id` = u.`u_id`
            WHERE tc.u_parent_id = 0 and tc.`u_global_contact_id` = ".$data['u_global_contact_id']." ORDER BY tc.`u_create_time` DESC LIMIT ".$page.",".$limit) ;
        
        $list = convertEMJ($list) ;
        
        $list = $this->getSubTree($list) ;
        
        $count = $this->contactCommentModel->query("SELECT count(1) 
            FROM u_global_contact_comment tc LEFT JOIN u_user u ON tc.`u_user_id` = u.`u_id`
            WHERE tc.u_parent_id = 0 and tc.`u_global_contact_id` = ".$data['u_global_contact_id']." ORDER BY tc.`u_create_time` DESC");
        return array('data' => $list,'total' => $count[0]['count(1)']) ;
    }
    
    private function getSubTree($parent_list) {
        foreach ($parent_list as $key => $value) {
            $chid_list = $this->contactCommentModel->field(array(
                'u_global_contact_comment.*',
                'u_user.u_nick_name'
            ))->join(" left join u_user on u_global_contact_comment.u_user_id = u_user.u_id ")->where(array(
                'u_global_contact_comment.u_parent_id' => $value['u_id']
            ))->select() ;
            $chid_list = convertEMJ($chid_list) ;
            $parent_list[$key]['childs'] = $chid_list ;
        }
        return $parent_list;
    }
    private function convertEMJ($list){
        for ($i=0;$i<count($list);$i++){
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
        return $list ;
    }
    
    public function getBBBoxCateAllList()
    {
        return $this->globalCateModel->order("u_order asc")->select() ;
    }
    public function getBBBoxCateList($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $cate_list = $this->globalCateModel->where($data)->select() ;
        $count = $this->globalCateModel->where($data)->count();
        
        return array('data' => $cate_list,'total' => $count) ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        $model = M("GlobalContactExtra");
        $id =  $this->globalContactModel->data($data)->add() ;
        $old = $model->where(array(
            'u_global_contact_id' => $id 
        ))->find() ;
        if(empty($old)){
            $model->data(array(
                'u_global_contact_id' => $id 
            ))->add() ;
        }
        return $id ;
    }
    public function addCate($data)
    {
        return $this->globalCateModel->data($data)->add() ;        
    }
    public function changeBBBoxCateFold($u_id)
    {
        $info = $this->getCateById($u_id) ;
        $is_show = $info['u_show'];
        return $this->globalCateModel->where(array(
            'u_id' => $u_id
        ))->save(array(
            'u_show' => $is_show == '1' ? 0:1 
        )) ;
    }
    
    public function deleteCate($id)
    {
        return $this->globalCateModel->where(array(
            'u_id' => $id
        ))->delete() ;
    }
    public function getCateById($id)
    {
        return $this->globalCateModel->where(array(
            'u_id' => $id
        ))->find() ;
    }
    
    
    
    public function getBBBoxById($id)
    {
        $info = $this->globalContactModel->field(array(
            'u_global_contact.*',
            'u_global_contact_extra.u_icon' ,
            'u_global_contact_extra.u_lng',
            'u_global_contact_extra.u_lat',
            'u_global_contact_extra.u_views',
            'u_global_contact_extra.u_pic'
        ))->join(" left join u_global_contact_extra on u_global_contact_extra.u_global_contact_id = u_global_contact.u_id ")->where(array(
            'u_global_contact.u_id' => $id
        ))->find() ;
        $info['u_pic'] = explode(",", $info['u_pic']) ;
        return $info ;
    }
    public function upateCateById($data,$id)
    {
        return $this->globalCateModel->where(array(
            'u_id' => $id
        ))->save($data) ;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->globalContactModel->where(array(
            'u_id' => $id
        ))->delete() ;
    }
    
    public function updateBBBoxPublic($data, $id)
    {
        $info = $this->globalContactModel->where(array(
            'u_id' => $id
        ))->find() ;
        if($info['u_public'] == 1){
            $this->contactLocationModel->where(array(
                'u_global_contact_id' => $id
            ))->delete() ;
        }
        return $this->globalContactModel->where(array(
            'u_id' => $id
        ))->save($data) ;
    }
    public function updateBBBoxPIC($data, $id)
    {
        return $this->updateBBBoxICON($data, $id) ;
    }
    public function updateBBBoxICON($data, $id)
    {
        $model = M("GlobalContactExtra");
        $old = $model->where(array( 'u_global_contact_id' => $id))->find() ;
        if(empty($old)){
            $data['u_global_contact_id'] = $id ;
            return $model->data($data)->add();
        }else{
            return $model->where(array(
                'u_global_contact_id' => $id
            ))->save($data) ;
        }
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->globalContactModel->where(array(
            'u_id' => $id
        ))->save($data) ;        
    }
    public function getCommonContactList(){
        $cate_list = $this->globalContactModel->query("SELECT * FROM (
            SELECT gc.`u_cate_code`,ugc.`u_name`,ugc.`u_show` FROM u_global_contact gc LEFT JOIN u_global_cate ugc ON gc.`u_cate_code` = ugc.`u_code`
            WHERE gc.`u_public` = 0 
            ORDER BY ugc.`u_order` ASC
            ) tmp GROUP BY tmp.`u_cate_code`") ;
        
        for ($i = 0; $i <count($cate_list);$i++){
            $list = $this->globalContactModel->where(array(
                'u_cate_code' => $cate_list[$i]['u_cate_code'],
                'u_public' => 0
            ))->select() ;
            $cate_list[$i]['childs'] = $list ;
        }
        
        return $cate_list ;
        
    }
    
    
    public function getAboutContactList($u_house_id){
        return $this->contactLocationModel->query(" SELECT gc.* FROM u_contact_to_location ctl LEFT JOIN u_global_contact gc ON ctl.`u_global_contact_id` = gc.`u_id`
            WHERE ctl.`u_house_id` = ".$u_house_id) ;
    }
    public function getContactListByHouseAndCate($data, $page, $limit){
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->contactLocationModel->query(" SELECT gc.*,ugce.u_icon,ugce.u_views FROM u_contact_to_location ctl 
            LEFT JOIN u_global_contact gc ON ctl.`u_global_contact_id` = gc.`u_id`
            left join u_global_contact_extra ugce on ugce.u_global_contact_id = gc.u_id
            WHERE gc.u_cate_code = '".$data['cate_code']."' and ctl.`u_house_id` = ".$data['u_house_id']." order by gc.u_create_time asc limit ".$page.",".$limit) ;
   
        $count = $this->contactLocationModel->query(" SELECT count(1) FROM u_contact_to_location ctl 
            LEFT JOIN u_global_contact gc ON ctl.`u_global_contact_id` = gc.`u_id`
            left join u_global_contact_extra ugce on ugce.u_global_contact_id = gc.u_id
            WHERE gc.u_cate_code = '".$data['cate_code']."' and ctl.`u_house_id` = ".$data['u_house_id']) ;
        
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
        
        $cate_list = $this->globalContactModel->query("SELECT * FROM (
            SELECT gc.`u_cate_code`FROM u_global_contact gc LEFT JOIN u_global_cate ugc ON gc.`u_cate_code` = ugc.`u_code`
            ORDER BY ugc.`u_order` ASC
            ) tmp GROUP BY tmp.`u_cate_code`") ;
        
        for ($i = 0; $i <count($cate_list);$i++){
            $list = $this->globalContactModel->where(array(
                'u_cate_code' => $cate_list[$i]['u_cate_code']
            )) ;
            $cate_list[$i]['childs'] = $list ;
        }
        
        return array('data' => $cate_list,'total' => count($cate_list)) ;
    }

    
    
    public function getBBBoxList($data, $page, $limit)
    {
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->globalContactModel->field(array(
            'u_global_contact.*',
            'u_global_contact_extra.u_icon',
            'u_global_contact_extra.u_lng',
            'u_global_contact_extra.u_lat',
            'u_global_contact_extra.u_pic'
        ))->join(' left join u_global_contact_extra on u_global_contact_extra.u_global_contact_id = u_global_contact.u_id ')->where($data)->limit($page,$limit)->select() ;
        
        $count = $this->globalContactModel->join(' left join u_global_contact_extra on u_global_contact_extra.u_global_contact_id = u_global_contact.u_id ')->where($data)->count();
        
        return array('data' => $list,'total' => $count) ;
    }
    public function getBBBoxPushList()
    {
        $list = $this->contactLocationModel->field(array(
            "u_contact_to_location.u_house_id",
            'u_contact_to_location.u_global_contact_id'
        ))->select() ;
        
        return $list;
    }
    public function addBBBoxPush($data)
    {
        $this->contactLocationModel->startTrans() ;
        try {
            $this->globalContactModel->where(array(
                'u_id' => $data['u_global_contact_id']
            ))->save(array(
                'u_public' => 1
            )) ;
            
            $this->contactLocationModel->where(array(
                'u_global_contact_id' => $data['u_global_contact_id']
            ))->delete() ;
            
            $checks = explode(",", $data['u_house_ids']) ;
            for ($i=0;$i<count($checks);$i++){
                if(!empty($checks[$i])){
                    $tmpData = array(
                        'u_house_id' => $checks[$i],
                        'u_global_contact_id' => $data['u_global_contact_id']
                    ) ;
                    $id = $this->contactLocationModel->data($tmpData)->add() ;
                }
            }
            return true;
            
            $this->contactLocationModel->commit() ;
        } catch (\Exception $e) {
            $this->contactLocationModel->rollback() ;
        }
        return false ;
    }
}