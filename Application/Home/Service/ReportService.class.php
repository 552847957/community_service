<?php
namespace Home\Service ;
use Common\Service\BaseService;

class ReportService implements BaseService{
    private $reportModel = null ;
    
    public function __construct() {
        
        $this->reportModel = M("Report");
    }
    
    public function getReportReasonList(){
        return C("REPORT_REASON_LIST");
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        return $this->reportModel->data($data)->add() ;        
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
                
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
        
        $list = $this->reportModel->where($data)->limit($page,$limit)->select() ;
        $count = $this->reportModel->where($data)->count();
        return array('data' => $list,'total' => $count) ;
    }

}