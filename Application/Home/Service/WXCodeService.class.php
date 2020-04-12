<?php
namespace Home\Service ;
use Common\Service\CommonWXService;
use Qcloud\Cos\Client ;
use Think\Log;
class WXCodeService extends CommonWXService{
    private $appId;
    private $appSecret;
    
    private $error;
    
    private $num_limit = 0 ;
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        
        
    }
    private function UploadImageQrCode($img){
        try {
            $saveimgfile_1 = C('UPLOAD_PATH_ROOT').'ecode/';
            $fileimgname = time()."-".rand(1000,9999).".png";
            $filecachs = $saveimgfile_1."/".$fileimgname;
            $fanhuistr = file_put_contents( $filecachs,$img);
            
            $secretId = C("COS_SECRETID"); //"云 API 密钥 SecretId";
            $secretKey = C("COS_SECRETKEY"); //"云 API 密钥 SecretKey";
            $region = "ap-shanghai"; //设置一个默认的存储桶地域
            $cosClient = new Client(
                array(
                    'region' => $region,
                    'schema' => 'http', //协议头部，默认为http
                    'credentials'=> array(
                        'secretId'  => $secretId ,
                        'secretKey' => $secretKey)
                )
            );
        
            $key = $fileimgname;
            $srcPath = dirname(dirname(dirname(dirname(__FILE__)))).$filecachs;//本地文件绝对路径
           
            $srcPath = str_replace('./Uploads', '/Uploads', $srcPath) ;
            $file = fopen($srcPath, "rb");
            if ($file) {
                $result = $cosClient->putObject(array(
                    'Bucket' => C('COS_BUCKET'),
                    'Key' => $key,
                    'Body' => $file));
                
                //删除文件
                unlink($srcPath) ;
                
                return 'https://'.$result['Location'] ;
                
            }else{
               return null ;
            }
        } catch (\Exception $e) {
            Log::record("云存储错误：".$e->getMessage()) ;
        }
        
        return null;
    }
    
    public function getUnlimited($scene,$page)
    {
        $token = $this->getAccessToken($this->appId,$this->appSecret) ;
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$token;
        $result = curl_post($url,array(
            'scene' => $scene,
            'page' => $page,
            'is_hyaline' => true 
        ));
        Log::record("token:".$token) ;
        Log::record("errcode:".$result['errcode']) ;
        if(strpos($result,'errcode') !==false){
            Log::record("进来了".$result) ;
            return '' ;
        }else{
            $imgPath = $this->UploadImageQrCode($result) ;
            return $imgPath ;
        }
    }
}