<?php
namespace Home\Service ;


class UserWordsService {
    private $wordsModel = null ;
    
    public function __construct() {
        $this->wordsModel = M("UserWords");
    }
    
    
    public function add($data){
        return $this->wordsModel->data($data)->add() ;
    }
    public function getList($data, $page, $limit){
        $limit = empty($limit) ? C("PAGE_LIMIT") : $limit;
        if(empty($page)){
            $page = 1 ;
        }
        $page = ($page -1 )*$limit ;
        
        $list = $this->wordsModel->field(array(
            'u_user_words.*',
            '(SELECT u_user.u_icon FROM u_user WHERE u_user.u_id = u_user_words.u_to_user_id) AS u_to_user_icon',
            '(SELECT u_user.u_icon FROM u_user WHERE u_user.u_id = u_user_words.u_from_user_id) AS u_from_user_icon ',
            '(SELECT ur.`u_icon` FROM u_user_level ul LEFT JOIN u_role ur ON ul.`u_role_id` = ur.`u_id` WHERE ul.`u_user_id` = u_user_words.u_from_user_id) as u_from_user_vip'
        ))->where($data)->order('u_create_time asc')->limit($page,$limit)->select();
        
        $list = convertEMJ($list) ;
        $count = $this->wordsModel->where($data)->count();
        return array('data' => $list,'total' => $count) ;
    }
}