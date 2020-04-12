<?php
namespace Home\Controller ;



use Common\Controller\ApiBaseController;
use Home\Service\ShopGoodService;
use Home\Service\OrderService;
use Home\Service\TopicService;
use Home\Service\ActivityService;
use Home\Service\UserService;
use Home\Service\RoleService;
use Home\Adapter\CoreAdapter;

class ApiController extends ApiBaseController {
    
    public function addWorkerCate(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_order'] = $_POST['u_order'] ;
            $data['u_code'] = $_POST['u_code'] ;
            
            $state = $this->cateService->add($data) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateWorkerCate(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_order'] = $_POST['u_order'] ;
            $data['u_code'] = $_POST['u_code'] ;
            
            $state = $this->cateService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function deleteWorkerCate(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->cateService->delete($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getWorkerCateById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->cateService->getCateInfoById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getWorkerCateList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            if(!empty($_POST['u_name'])){
                $params['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            
            $list = $this->cateService->getList($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getWorkerCateAllList(){
        if(IS_POST){
            
            $list = $this->cateService->getCateList() ;
            $this->ajaxReturn($list) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    /********************************************************************/
    public function deleteWorker(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->workerService->delete($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getWorkerById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->workerService->getWorkerInfoById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getWorkerList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $params = array() ;
            if(!empty($_POST['u_cate_code'])){
                $params['u_cate_code'] = array('like' ,'%'.$_POST['u_cate_code'].'%' );
            }
            if(!empty($_POST['u_name'])){
                $params['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            $list = $this->workerService->getListByParam($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getWorkerReportList (){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            $u_user_id = $_POST['u_user_id'] ;
            
            $data['u_to_user_id'] = array('eq' ,$u_user_id);
            
            $list = $this->reportService->getList($data, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    /***********************************/
    public function getBBBoxCateAllList(){
        if(IS_POST){
            $list = $this->globalContactService->getBBBoxCateAllList() ;
            $this->ajaxReturn($list) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getBBBoxCateList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            if(!empty($_POST['u_name'])){
                $params['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            $list = $this->globalContactService->getBBBoxCateList($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function addBBBoxCate(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_order'] = $_POST['u_order'] ;
            $data['u_show'] = $_POST['u_show'] ;
            $data['u_code'] = $_POST['u_code'] ;
            
            $state = $this->globalContactService->addCate($data) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateBBBoxCate(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_order'] = $_POST['u_order'] ;
            $data['u_code'] = $_POST['u_code'] ;
            $data['u_show'] = $_POST['u_show'] ;
            
            $state = $this->globalContactService->upateCateById($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function changeBBBoxCateFold(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->globalContactService->changeBBBoxCateFold($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function deleteBBBoxCate(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->globalContactService->deleteCate($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getBBBoxCateById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->globalContactService->getCateById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    /*****************************************************************/
    public function getBBBoxList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            if(!empty($_POST['u_name'])){
                $params['u_global_contact.u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            $list = $this->globalContactService->getBBBoxList($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function addBBBox(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_cate_code'] = $_POST['u_cate_code'] ;
            $data['u_phone'] = $_POST['u_phone'] ;
            $data['u_service_time'] = $_POST['u_service_time'] ;
            $data['u_public'] = $_POST['u_public'] ;
            $data['u_mark'] = $_POST['u_mark'] ;
            $data['u_create_time'] = getCurrentTime() ;
            
            $state = $this->globalContactService->add($data) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateBBBoxPIC(){
        if(IS_POST){
            $data['u_pic'] = $_POST['u_pic'] ;
            
            $state = $this->globalContactService->updateBBBoxPIC($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateBBBoxICON(){
        if(IS_POST){
            $data['u_icon'] = $_POST['u_icon'] ;
            
            $state = $this->globalContactService->updateBBBoxICON($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function updateBBBox(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_cate_code'] = $_POST['u_cate_code'] ;
            $data['u_phone'] = $_POST['u_phone'] ;
            $data['u_service_time'] = $_POST['u_service_time'] ;
            $data['u_public'] = $_POST['u_public'] ;
            $data['u_mark'] = $_POST['u_mark'] ;
            
            $state = $this->globalContactService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function updateBBBoxPublic(){
        if(IS_POST){
            $data['u_public'] = $_POST['u_public'] ;
            $state = $this->globalContactService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function updateNoticePublic(){
        if(IS_POST){
            $data['u_public'] = $_POST['u_public'] ;
            if($data['u_public'] == 0){
                $data['u_house_id'] = '' ;
            }
            $state = $this->noticeService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function deleteBBBox(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->globalContactService->delete($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getBBBoxById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->globalContactService->getBBBoxById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    /**********************************************/
    public function getNewNotice(){
        if(IS_POST){
            $state = $this->noticeService->getNewNotice() ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function addNotice(){
        if(IS_POST){
            $data['u_title'] = $_POST['u_title'] ;
            $data['u_url'] = $_POST['u_url'] ;
            $data['u_public'] = $_POST['u_public'] ;
            $data['u_content'] = $_POST['u_content'] ;
            $data['u_create_time'] = getCurrentTime() ;
            
            $state = $this->noticeService->add($data) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateNotice(){
        if(IS_POST){
            $data['u_title'] = $_POST['u_title'] ;
            $data['u_url'] = $_POST['u_url'] ;
            $data['u_public'] = $_POST['u_public'] ;
            $data['u_content'] = $_POST['u_content'] ;
            
            $state = $this->noticeService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function deleteNotice(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->noticeService->delete($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getNoticeById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->noticeService->getNoticeById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getNoticeList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            if(!empty($_POST['u_name'])){
                $params['u_title'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            $list = $this->noticeService->getList($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    
    /*****************************************************************/
    public function getHouseList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            if(!empty($_POST['u_name'])){
                $params['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            $list = $this->houseService->getList($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getHouseAllList(){
        if(IS_POST){
            $list = $this->houseService->getAllList() ;
            $this->ajaxReturn($list) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function addHouse(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_lat'] = $_POST['u_lat'] ;
            $data['u_lng'] = $_POST['u_lng'] ;
            $data['u_mark'] = $_POST['u_mark'] ;
            
            $state = $this->houseService->add($data) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateHouse(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_lat'] = $_POST['u_lat'] ;
            $data['u_lng'] = $_POST['u_lng'] ;
            $data['u_mark'] = $_POST['u_mark'] ;
            
            $state = $this->houseService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function deleteHouse(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->houseService->delete($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function mergeHouse(){
        if(IS_POST){
            $u_ids = $_POST['u_ids'] ;
            $state = $this->houseService->mergeHouse($u_ids) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getHouseById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->houseService->getHouseById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    
    /*****************************************************************/
    public function getTrendList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params['u_name'] = $_POST['u_name'];
            
            $list = $this->trendService->getListWithOutHouse($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getTrendCommentList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params['u_trend_id'] = $_POST['u_trend_id'];
            
            $list = $this->trendService->getCommentList($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function addTrend(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_lat'] = $_POST['u_lat'] ;
            $data['u_lng'] = $_POST['u_lng'] ;
            $data['u_mark'] = $_POST['u_mark'] ;
            
            $state = $this->trendService->add($data) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateTrend(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_lat'] = $_POST['u_lat'] ;
            $data['u_lng'] = $_POST['u_lng'] ;
            $data['u_mark'] = $_POST['u_mark'] ;
            
            $state = $this->trendService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function deleteTrend(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->trendService->delete($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function deleteTrendComment(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->trendService->deleteComment($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getTrendById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->trendService->getTrendInfoById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    /*****************************************************************/
    public function getBBBoxPushList(){
        if(IS_POST){
            $list = $this->globalContactService->getBBBoxPushList() ;
            $this->ajaxReturn($list) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getNoticePushList(){
        if(IS_POST){
            $list = $this->noticeService->getNoticePushList() ;
            $this->ajaxReturn($list) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function addBBBoxPush (){
        if(IS_POST){
            $data['u_global_contact_id'] = $_POST['u_global_contact_id'] ;
            $data['u_house_ids'] = $_POST['u_house_ids'] ;
            
            $state = $this->globalContactService->addBBBoxPush($data) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function addNoticePush (){
        if(IS_POST){
            $data['u_id'] = $_POST['u_id'] ;
            $data['u_house_ids'] = $_POST['u_house_ids'] ;
            
            $state = $this->noticeService->addNoticePush($data) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    /************************************************/
    public function getTicketList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            if(!empty($_POST['u_name'])){
                $params['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            $list = $this->ticketService->getListWithOut($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function addTicket(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_num'] = $_POST['u_num'] ;
            $data['u_limit_num'] = $_POST['u_limit_num'] ;
            $data['u_limit_time'] = $_POST['u_limit_time'] ;
            
            $state = $this->ticketService->add($data) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateTicket(){
        if(IS_POST){
            if(!empty($_POST['u_name'])){
                $data['u_name'] = $_POST['u_name'] ;
            }
            if(!empty($_POST['u_num'])){
                $data['u_num'] = $_POST['u_num'] ;
            }
            if(!empty($_POST['u_limit_num'])){
                $data['u_limit_num'] = $_POST['u_limit_num'] ;
            }
            if(!empty($_POST['u_limit_time'])){
                $data['u_limit_time'] = $_POST['u_limit_time'] ;
            }
            if(!empty($_POST['u_good_id'])){
                $data['u_good_id'] = $_POST['u_good_id'] ;
            }
            
            
            $state = $this->ticketService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    
    public function deleteTicket(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->ticketService->delete($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getTicketById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->ticketService->getTicketById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    /**********************************************************/
    public function getGoodList(){
        if(IS_POST){
            $goodService = new ShopGoodService() ;
            
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            if(!empty($_POST['u_name'])){
                $params['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            $list = $goodService->getListWhitOut($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getGoodListFromMobile(){
        if(IS_POST){
            $goodService = new ShopGoodService() ;
            
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            if(!empty($_POST['u_name'])){
                $params['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            $list = $goodService->getListWhitOut($params, $page, $limit) ;
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function changeGoodState(){
        if(IS_POST){
            $data['u_ok'] = $_POST['u_ok'] ;
            $good_id = $_POST['u_id'] ;
            $goodService = new ShopGoodService() ;
            
            $state = $goodService->update($data,$good_id) ;

            $good_info = $goodService->getGoodInfoById($good_id) ;
            if($state > 0 && $_POST['u_ok'] == '0' && $good_info['u_source'] == 'user'){
                //商家成功通知闲置发布人
                $user_info = $this->userService->getInfoById($good_info['u_user_id']) ;
                $coreAdapter = new CoreAdapter() ;
                $coreAdapter->pushGoodMsg($user_info['u_open_id'], 'pages/index/shop/produce?u_id='.$good_id, $good_info['u_name'], '你添加的闲置商品通过审核，快去看看吧！') ;
            }
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function changeGoodStateFromMobile(){
        if(IS_POST){
            $data['u_ok'] = $_POST['u_ok'] ;
            $good_id = $_POST['u_id'] ;
            $goodService = new ShopGoodService() ;
            
            $state = $goodService->update($data,$good_id) ;
            
            $good_info = $goodService->getGoodInfoById($good_id) ;
            if($state > 0 && $_POST['u_ok'] == '0' && $good_info['u_source'] == 'user'){
                //商家成功通知闲置发布人
                $user_info = $this->userService->getInfoById($good_info['u_user_id']) ;
                $coreAdapter = new CoreAdapter() ;
                $coreAdapter->pushGoodMsg($user_info['u_open_id'], 'pages/index/shop/produce?u_id='.$good_id, $good_info['u_name'], '你添加的闲置商品通过审核，快去看看吧！') ;
            }
            
            
            $this->ajaxReturn(array(
                'code' => 200 ,
                'data' => $state ,
                'msg' => ''
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function addGood(){
        if(IS_POST){
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_now_price'] = $_POST['u_now_price'] ;
            $data['u_past_price'] = $_POST['u_past_price'] ;
            $data['u_stores'] = $_POST['u_stores'] ;
            $data['u_content'] = $_POST['u_content'] ;
            $data['u_specs'] = $_POST['u_specs'] ;
            $data['u_covers'] = $_POST['u_covers'] ;
            
            $goodService = new ShopGoodService() ;
            
            $state = $goodService->add($data) ;
            
            
           /*  $codePath = $this->getULimitCode($state, 'pages/index/shop/produce') ;
            $user_info = $this->getLoginUserInfo() ;
            if(!empty($codePath)){
                $data['u_id'] = $state ;
                $sharePath = $this->jobCricleInfoPaper($data,$remote_img_arr,$user_info, $codePath) ;
                
                $this->trendService->update(array(
                    'u_share_path' => $sharePath
                ), $state) ;
            }
             */
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateGood(){
        if(IS_POST){
            if(!empty($_POST['u_name'])){
                $data['u_name'] = $_POST['u_name'] ;
            }
            if(!empty($_POST['u_now_price'])){
                $data['u_now_price'] = $_POST['u_now_price'] ;
            }
            if(!empty($_POST['u_past_price'])){
                $data['u_past_price'] = $_POST['u_past_price'] ;
            }
            if(!empty($_POST['u_stores'])){
                $data['u_stores'] = $_POST['u_stores'] ;
            }
            if(!empty($_POST['u_content'])){
                $data['u_content'] = $_POST['u_content'] ;
            }
            if(!empty($_POST['u_specs'])){
                $data['u_specs'] = $_POST['u_specs'] ;
            }
            if(!empty($_POST['u_covers'])){
                $data['u_covers'] = $_POST['u_covers'] ;
            }
            
            $goodService = new ShopGoodService() ;
            $state = $goodService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    
    public function deleteGood(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            $goodService = new ShopGoodService() ;
            $state = $goodService->delete($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getGoodById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            $goodService = new ShopGoodService() ;
            $state = $goodService->getGoodInfoById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getGoodByIdFromMobile (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            $goodService = new ShopGoodService() ;
            $state = $goodService->getGoodInfoById($u_id) ;
            
            $this->ajaxReturn(array(
                'code' => 200 ,
                'data' => $state ,
                'msg' => ''
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    /*****************************************************************/
    public function getOrderList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            if(!empty($_POST['u_name'])){
                $params['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            $orderService = new OrderService() ;
            $list = $orderService->getList($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getOrderWhiteGoodList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            if(!empty($_POST['u_name'])){
                $params['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            $orderService = new OrderService() ;
            $list = $orderService->getOrderWithGoodList($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getOrderWhiteGoodListFromMible(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            if(!empty($_POST['u_name'])){
                $params['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            $orderService = new OrderService() ;
            $list = $orderService->getOrderWithGoodList($params, $page, $limit) ;
            
            $this->returnSuccess(array(
                'count' => $list['total'] ,
                'data' => $list['data'],
                'pages' => ceil($list['total']/$limit) ,
                'hasNext' => count($list['data']) == $limit ? true : false
            )) ;
            
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getOrderById(){
        if(IS_POST){
            $orderService = new OrderService() ;
            $state = $orderService->getOrderById($_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateOrder(){
        if(IS_POST){
            if(!empty($_POST['u_code'])){
                $data['u_code'] = $_POST['u_code'] ;
            }
            if(!empty($_POST['u_good_id'])){
                $data['u_good_id'] = $_POST['u_good_id'] ;
            }
            if(!empty($_POST['u_user_id'])){
                $data['u_user_id'] = $_POST['u_user_id'] ;
            }
            if(!empty($_POST['u_state'])){
                $data['u_state'] = $_POST['u_state'] ;
            }
            if(!empty($_POST['u_total_price'])){
                $data['u_total_price'] = $_POST['u_total_price'] ;
            }
            if(!empty($_POST['u_pay_time'])){
                $data['u_pay_time'] = $_POST['u_pay_time'] ;
            }
            
            $orderService = new OrderService() ;
            $state = $orderService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function deleteOrder(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            $orderService = new OrderService() ;
            $state = $orderService->delete($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    
    /************************************************************/
    public function addTopic(){
        if(IS_POST){
            $topicService = new TopicService() ;
            
            $data['u_name'] = $_POST['u_name'] ;
            $data['u_content'] = $_POST['u_content'] ;
            $data['u_cover'] = $_POST['u_cover'] ;
            
            $state = $topicService->add($data) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateTopic(){
        if(IS_POST){
            if(!empty($_POST['u_name'])){
                $data['u_name'] = $_POST['u_name'] ;
            }
            if(!empty($_POST['u_content'])){
                $data['u_content'] = $_POST['u_content'] ;
            }
            if(!empty($_POST['u_cover'])){
                $data['u_cover'] = $_POST['u_cover'] ;
            }
            if(!empty($_POST['u_views'])){
                $data['u_views'] = $_POST['u_views'] ;
            }
            $topicService = new TopicService() ;
            $state = $topicService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function deleteTopic(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $topicService = new TopicService() ;
            $state = $topicService->delete($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getTopicById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            $topicService = new TopicService() ;
            $state = $topicService->getTopicInfoById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getTopicList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            if(!empty($_POST['u_name'])){
                $params['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            
            $topicService = new TopicService() ;
            $list = $topicService->getList($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    /************************************************************/
    public function addActivity(){
        if(IS_POST){
            $activityService = new ActivityService() ;
            
            $data['u_cover'] = $_POST['u_cover'] ;
            $data['u_url'] = $_POST['u_url'] ;
            
            $state = $activityService->add($data) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateActivity(){
        if(IS_POST){
            if(!empty($_POST['u_cover'])){
                $data['u_cover'] = $_POST['u_cover'] ;
            }
            if(!empty($_POST['u_url'])){
                $data['u_url'] = $_POST['u_url'] ;
            }
            
            $activityService = new ActivityService() ;
            $state = $activityService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function deleteActivity(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $activityService = new ActivityService() ;
            $state = $activityService->delete($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getActivityById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            $activityService = new ActivityService() ;
            $state = $activityService->getActivityById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getActivityList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            $params = array() ;
            $activityService = new ActivityService() ;
            $list = $activityService->getList($params, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    /************************************************************/
    public function setUserRole (){
        if(IS_POST){
            $u_user_id = $_POST['u_user_id'];
            $u_role_ids = $_POST['u_role_ids'];
            
            $state = $this->userService->setUserRole($u_user_id,$u_role_ids);
       
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function updateUser(){
        if(IS_POST){
            if(!empty($_POST['u_name'])){
                $data['u_name'] = $_POST['u_name'] ;
            }
            if(!empty($_POST['u_nick_name'])){
                $data['u_nick_name'] = $_POST['u_nick_name'] ;
            }
            
            if(!empty($_POST['u_gender'])){
                $data['u_gender'] = $_POST['u_gender'] ;
            }
            if(!empty($_POST['u_icon'])){
                $data['u_icon'] = $_POST['u_icon'] ;
            }
            if(!empty($_POST['u_phone'])){
                $data['u_phone'] = $_POST['u_phone'] ;
            }
            if(!empty($_POST['u_mark'])){
                $data['u_mark'] = $_POST['u_mark'] ;
            }
            $state = $this->userService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function deleteUser(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            
            $state = $this->userService->delete($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getUserById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            $state = $this->userService->getInfoById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getUserList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            if(!empty($_POST['u_name'])){
                $data['u_nick_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            if(!empty($_POST['u_open_id'])){
                $data['u_open_id'] = array('like' ,'%'.$_POST['u_open_id'].'%' );
            }
            
            $list = $this->userService->getListByParams($data, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    
    
    /************************************************************/
    public function updateRole(){
        if(IS_POST){
            if(!empty($_POST['u_name'])){
                $data['u_name'] = $_POST['u_name'] ;
            }
            if(!empty($_POST['u_code'])){
                $data['u_code'] = $_POST['u_code'] ;
            }
            if(!empty($_POST['u_icon'])){
                $data['u_icon'] = $_POST['u_icon'] ;
            }
            
            $roleService = new RoleService() ;
            $state = $roleService->update($data,$_POST['u_id']) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function addRole(){
        if(IS_POST){
            if(!empty($_POST['u_name'])){
                $data['u_name'] = $_POST['u_name'] ;
            }
            if(!empty($_POST['u_code'])){
                $data['u_code'] = $_POST['u_code'] ;
            }
            if(!empty($_POST['u_icon'])){
                $data['u_icon'] = $_POST['u_icon'] ;
            }
            
            $roleService = new RoleService() ;
            $state = $roleService->add($data) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function deleteRole(){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            $roleService = new RoleService() ;
            $state = $roleService->delete($u_id) ;
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getRoleById (){
        if(IS_POST){
            $u_id = $_POST['u_id'] ;
            $roleService = new RoleService() ;
            $state = $roleService->getRoleById($u_id) ;
            
            $this->ajaxReturn($state) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    
    public function getUserRoleAllList(){
        if(IS_POST){
            $u_user_id = $_POST['u_user_id'];
            $roleService = new RoleService() ;
            $list = $roleService->getUserRoleAllList($u_user_id) ;
            $this->ajaxReturn($list) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getRoleAllList(){
        if(IS_POST){
            $roleService = new RoleService() ;
            $list = $roleService->getRoleAllList() ;
            $this->ajaxReturn($list) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
    public function getRoleList(){
        if(IS_POST){
            $page = $_POST['page'] ;
            $limit = $_POST['limit'] ;
            
            if(!empty($_POST['u_name'])){
                $data['u_name'] = array('like' ,'%'.$_POST['u_name'].'%' );
            }
            if(!empty($_POST['u_code'])){
                $data['u_code'] = array('like' ,'%'.$_POST['u_code'].'%' );
            }
            $roleService = new RoleService() ;
            $list = $roleService->getList($data, $page, $limit) ;
            $this->ajaxReturn(array(
                'code' => 0,
                'msg' => '',
                'count' => $list['total'] ,
                'data' => $list['data']
            )) ;
        }else{
            $this->returnError("method 不支持") ;
        }
    }
}