<?php
namespace Home\Service ;

use Common\Service\BaseService;
use Think\Log;

class HouseService implements BaseService{
    
    private $houseModel = null ;
    
    public function __construct() {
        
        $this->houseModel = M("House");
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        /* $info = $this->houseModel->where(array(
            'u_name' => $data['u_name']
        ))->select() ;  */
        $info = $this->houseModel->query("SELECT * FROM u_house h WHERE h.`u_name` LIKE '%".$data['u_name']."%' ") ;
        if(empty($info)){
            $id = $this->houseModel->data($data)->add() ;
            $data['u_id'] = $id ;
            return $data ;
        }
        return $info[0];
    }

    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::delete()
     */
    public function delete($id)
    {
        return $this->houseModel->where(array(
            'u_id' => $id
        ))->delete() ;
    }
    /**
     * 合并社区信息
     * @param unknown $ids
     * @return boolean
     */
    public function mergeHouse($ids){
        $idstring = $ids ;
        $ids = json_decode($ids) ;
        $this->houseModel->startTrans() ;
        $userHouseModel = M("UserHouse");
        try {
            $list = $userHouseModel->query("SELECT * FROM(
            SELECT (SELECT COUNT(1) AS users FROM u_user_house uhh WHERE uhh.u_house_id = uh.`u_house_id`)
            AS total_users,uh.* FROM u_user_house uh
            where uh.`u_house_id` IN (".$idstring.")
            GROUP BY uh.`u_house_id` ) AS tt ORDER BY tt.total_users DESC ") ;
            
            if(empty($list)){ //没有用户绑定这个社区，那么直接删除多余的社区信息
                for ($i=0;$i<count($ids);$i++){
                    if($i == 0){
                        continue ;
                    }else{
                        $this->houseModel->where(array(
                            'u_id' => $ids[$i]
                        ))->delete() ;
                    }
                }
            }else{ //说明社区已经有人用户绑定了，那么就要根据绑定数据来删除，原则上是绑定多的用户社区保留，少的删除并自动重新绑定
                $maxHouseId = 0 ;
                for ($i=0;$i<count($list);$i++){
                    if($i == 0){
                        $maxHouseId = $list[$i]['u_house_id'] ;
                    }else{
                        if($maxHouseId > 0){
                            $state = $this->houseModel->where(array(
                                'u_id' => $list[$i]['u_house_id']
                            ))->delete() ; 
                            Log::record("删除合并的社区信息：".$list[$i]['u_house_id']) ;
                            
                            $delList = $userHouseModel->where(array(
                                'u_house_id' => $list[$i]['u_house_id']
                            ))->select() ;
                            for ($j=0;$j<count($delList);$j++){
                                $userHouseModel->where(array(
                                    'u_id' => $delList[$j]['u_id']
                                ))->save(array(
                                    'u_house_id' => $maxHouseId ,
                                )) ;
                            }
                        }
                    }
                    
                }
            }
            
            $this->houseModel->commit() ;
            return true ;
        }catch (\Exception $e){
            $this->houseModel->rollback() ;
        }
        return false ;
    }
    
    public function getHouseById ($id){
        return $this->houseModel->where(array(
            'u_id' => $id
        ))->find() ;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::update()
     */
    public function update($data, $id)
    {
        return $this->houseModel->where(array('u_id'=>$id))->save($data) ;        
    }
    public function updateUserBind($data, $where)
    {
        $userHouseModel = M("UserHouse");
        return $userHouseModel->where($where)->save($data) ;
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
        
        $list = $this->houseModel->where($data)->limit($page,$limit)->select() ;
        $count = $this->houseModel->where($data)->count();
        return array('data' => $list,'total' => $count) ;
    }
    public function getAllList()
    {
        $list = $this->houseModel->select() ;
        return $list ;
    }
    public function getHouseFriendAllList($u_user_id)
    {
        $list = $this->houseModel->query("SELECT u.* FROM u_user_house uu LEFT JOIN u_user u ON uu.`u_user_id` = u.`u_id`
            WHERE uu.`u_house_id` IN (
            SELECT u_house_id FROM u_user_house WHERE u_user_id = ".$u_user_id."
            ) ORDER BY uu.`u_create_time` DESC") ;
        return $list ;
    }
    
    
}