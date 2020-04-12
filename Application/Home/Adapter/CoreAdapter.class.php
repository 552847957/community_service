<?php
namespace Home\Adapter ;
class CoreAdapter {
    public function pushOrderMsg($page,$touser,$orderTime,$orderNum,$orderPrice,$mark){
        $result = curl_post(C("CORE_SERVICE_URL")."msg/pushOrderMsg",array(
            "touser"=>$touser,
            "page"=>$page,
            "data"=>array(
                "character_string1"=>$orderCode,
                "time3"=>$orderTime,
                "number11"=>$orderNum,
                "amount14"=>$orderPrice."元",
                "thing12"=>$mark
            )
        ));
        return $result;
    }
    public function pushCommentMsg($touser,$page,$title,$username,$content,$time){
        $result = curl_post(C("CORE_SERVICE_URL")."msg/pushCommentMsg",array(
            "touser"=>$touser,
            "page"=>$page,
            "data"=>array(
                "thing1"=>$title,
                "thing5"=>$username,
                "thing2"=>$content,
                "time3"=>$time
            )
        ));
        return $result;
    }
    public function pushPayMsg($touser,$page,$goodname,$price,$time,$address,$mark){
        $result = curl_post(C("CORE_SERVICE_URL")."msg/pushPayMsg",array(
            "touser"=>$touser,
            "page"=>$page,
            "data"=>array(
                "thing3"=>$goodname,
                "amount1"=>$price,
                "date5"=>$time,
                "thing9"=>$address,
                "thing11"=>$mark
            )
        ));
        return $result;
    }
    public function pushSignMsg($touser,$page,$title,$time,$total,$mark){
        $result = curl_post(C("CORE_SERVICE_URL")."msg/pushSignMsg",array(
            "touser"=>$touser,
            "page"=>$page,
            "data"=>array(
                "thing1"=>$title,
                "time5"=>$time,
                "number3"=>$total,
                "thing4"=>$mark
            )
        ));
        return $result;
    }
    public function pushMoneyMsg($touser,$page,$username,$goodname,$price,$mark){
        $result = curl_post(C("CORE_SERVICE_URL")."msg/pushMoneyMsg",array(
            "touser"=>$touser,
            "page"=>$page,
            "data"=>array(
                "name4"=>$username,
                "thing1"=>$goodname,
                "amount3"=>$price,
                "thing5"=>$mark
            )
        ));
        return $result;
    }
    
    public function pushGoodMsg($touser,$page,$goodname,$mark){
        $result = curl_post(C("CORE_SERVICE_URL")."msg/pushGoodMsg",array(
            "touser"=>$touser,
            "page"=>$page,
            "data"=>array(
                "thing1"=>$goodname,
                "thing2"=>$mark
            )
        ));
        return $result;
    }
    
    public function pushUserOrderMsg($touser,$page,$username,$price,$time,$ordercode){
        $result = curl_post(C("CORE_SERVICE_URL")."msg/pushUserOrderMsg",array(
            "touser"=>$touser,
            "page"=>$page,
            "data"=>array(
                "name1"=>$username,
                "amount2"=>$price.'元',
                "date3"=>$time,
                "character_string4"=>'订单号码：'.$ordercode
            )
        ));
        return $result;
    }
    
    public function pushCheckGoodMsg($touser,$page,$username,$goodstate,$time,$mark){
        $result = curl_post(C("CORE_SERVICE_URL")."msg/pushCheckGoodMsg",array(
            "touser"=>$touser,
            "page"=>$page,
            "data"=>array(
                "thing1"=>$username,
                "thing2"=>'申请上架闲置',
                "phrase3"=>$goodstate,
                "date4"=>$time,
                "thing6" => $mark
            )
        ));
        return $result;
    }
}