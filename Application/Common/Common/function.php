<?php
function randFloat($min=0, $max=0.5){
    return $min + mt_rand()/mt_getrandmax() * ($max-$min);
}
function xmlToArray($xml) {
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    $val = json_decode(json_encode($xmlstring), true);
    return $val;
}
//作用：生成签名
function getSign($Obj) {
    foreach ($Obj as $k => $v) {
        $Parameters[$k] = $v;
    }
    //签名步骤一：按字典序排序参数
    ksort($Parameters);
    $String = formatBizQueryParaMap($Parameters, false);
    //签名步骤二：在string后加入KEY
    $String = $String . "&key=" . C('MCH_KEY');
    //签名步骤三：MD5加密
    $String = md5($String);
    //签名步骤四：所有字符转为大写
    $result_ = strtoupper($String);
    return $result_;
}


///作用：格式化参数，签名过程需要使用
function formatBizQueryParaMap($paraMap, $urlencode) {
    $buff = "";
    ksort($paraMap);
    foreach ($paraMap as $k => $v) {
        if ($urlencode) {
            $v = urlencode($v);
        }
        $buff .= $k . "=" . $v . "&";
    }
    $reqPar = '';
    if (strlen($buff) > 0) {
        $reqPar = substr($buff, 0, strlen($buff) - 1);
    }
    return $reqPar;
}

function convertEMJ($list){
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

function http_request($url, $data = null){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch); //发送请求获取结果
    if($output == false){
        echo 'Curl error: ' . curl_error($ch);
    }
    curl_close($ch); //关闭会话
    return $output;
    
}
function curl_post($url, $data = null){
    $curl = curl_init();
  //  $headers = ['Content-Type'=>'application/json'];
    $headers[]  =  "Content-Type:application/json";
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); 
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE)); 
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
function curl($url, $data = [])
{
    // 处理get数据
    if (!empty($data)) {
        $url = $url . '?' . http_build_query($data);
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}
function getCurrentTime () {
    return  date("Y-m-d H:i:s",time()) ;
}
function getCurrentDate () {
    return  date("Y-m-d",time()) ;
}