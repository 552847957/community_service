<?php
namespace Home\Service ;
use Common\Service\CommonWXService;

class WXContentCheckService extends CommonWXService {
    
    private $appId;
    private $appSecret;
    
    private $error;
    
    /**
     * 构造方法
     * WxUser constructor.
     * @param $appId
     * @param $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }
    /**
     * 文本检查
     * @param unknown $content
     * @return boolean|mixed
     */
    public function msgSecCheck($content)
    {
        $token = $this->getAccessToken($this->appId,$this->appSecret) ;
        $url = 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token='.$token;
        
        $result = json_decode(curl_post($url,array(
            'content' => $content
        )), true);
        if (isset($result['errcode']) && intval($result['errcode']) == 87014) {
            $this->error = $result['errmsg'];
            return false;
        }
        return true;
    }
    public function imgSecCheck($content)
    {
        $token = $this->getAccessToken($this->appId,$this->appSecret) ;
        $url = 'https://api.weixin.qq.com/wxa/img_sec_check?access_token='.$token;
        $result = json_decode(http_request($url,array(
            'media' => $content
        )), true);
        if (isset($result['errcode'])&& intval($result['errcode']) == 87014) {
            $this->error = $result['errmsg'];
            return false;
        }
        return true;
    }
    
    public function getError()
    {
        return $this->error;
    }
}