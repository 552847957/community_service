<?php
namespace Common\Controller ;
use Home\Service\CateService;
use Home\Service\GlobalContactService;
use Home\Service\HouseService;
use Home\Service\ReportService;
use Home\Service\TrendService;
use Home\Service\UserService;
use Home\Service\WorkerService;
use Think\Controller;
use Home\Service\NoticeService;
use Home\Service\TicketService;
class ApiBaseController extends Controller {
    
    protected $userService = null ;
    protected $cateService = null ;
    protected $houseService = null;
    protected $trendService = null ;
    protected $workerService = null ;
    protected $reportService = null ;
    protected $globalContactService = null ;
    protected $noticeService = null ;
    protected $ticketService = null ;
    
    protected function initService(){
        $this->userService = new UserService() ;
        $this->cateService = new CateService() ;
        $this->houseService = new HouseService() ;
        $this->trendService = new TrendService() ;
        $this->workerService = new WorkerService() ;
        $this->reportService = new ReportService() ;
        $this->globalContactService = new GlobalContactService() ;
        $this->noticeService = new NoticeService() ;
        $this->ticketService = new TicketService() ;
    }
    public function __construct(){
        parent::__construct();
        
        $this->corsConfig() ;
        
        $this->initService() ;
        
    }
    private function corsConfig() {
        //处理跨域问题
        header('Content-Type:application/json; charset=utf-8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Max-Age:1728000'); // 允许访问的有效期
        header('Access-Control-Allow-Headers:*');
        header('Access-Control-Allow-Methods:OPTIONS, GET, POST, DELETE');
    }

  
    protected function returnSuccess($data,$msg='操作成功'){
        $this->ajaxReturn(array(
            'code' => 200 ,
            'data' => $data ,
            'msg' => $msg
        )) ;
    }
    protected function returnError($msg='操作失败'){
        $this->ajaxReturn(array(
            'code' => 400 ,
            'msg' => $msg
        )) ;
    }
    protected function returnJson($code ,$data,$msg='操作成功'){
        $this->ajaxReturn(array(
            'code' => $code ,
            'data' => $data ,
            'msg' => $msg
        )) ;
    }
}