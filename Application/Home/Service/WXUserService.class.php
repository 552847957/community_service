<?php
namespace Home\Service ;
use Home\Utils\WXBizDataCryptUtils;

class WXUserService {
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
    
    public function getMobile($sessionKey,$encryptedData,$iv){
        $pc = new WXBizDataCryptUtils($this->appId, $sessionKey); //注意使用\进行转义
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
       $data = json_decode($data) ;
        if ($errCode == 0) {
            return $data->phoneNumber ;
        } else {
            return '';
        }
        
        
      /*   $aesKey = base64_decode($sessionKey);
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $result = json_decode($result,true);
        if($result){
            return $result['phoneNumber'];
        }
        return ''; */
    }
    /**
     * 获取session_key
     * @param $code
     * @return array|mixed
     */
    public function sessionKey($code)
    {
        /**
         * code 换取 session_key
         * ​这是一个 HTTPS 接口，开发者服务器使用登录凭证 code 获取 session_key 和 openid。
         * 其中 session_key 是对用户数据进行加密签名的密钥。为了自身应用安全，session_key 不应该在网络上传输。
         */
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $result = json_decode(curl($url, [
            'appid' => $this->appId,
            'secret' => $this->appSecret,
            'grant_type' => 'authorization_code',
            'js_code' => $code
        ]), true);
        if (isset($result['errcode'])) {
            $this->error = $result['errmsg'];
            return false;
        }
        return $result;
    }
    
    public function getError()
    {
        return $this->error;
    }
    
}