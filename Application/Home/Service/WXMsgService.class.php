<?php
namespace Home\Service ;
use Common\Service\CommonWXService;
use Common\Service\BaseService;

class WXMsgService extends CommonWXService implements BaseService{
    private $appId;
    private $appSecret;
    
    private $error;
    
    private $wxmsgModel = null ;
    
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        
        $this->wxmsgModel = M("WxMsg");
    }
    
    public function noticeCommentResponse($page,$b_open_id,$b_from_id,$data){
        $tmp_id = C("RESPONSE_TEMP_ID");
        $this->sendMsg($tmp_id,$page,$b_open_id,$b_from_id,$data) ;
    }
    
    private function sendMsg($template_id,$page,$open_id,$form_id,$param){
        $appid  = C("APP_ID");
        $secret = C("APP_SECRITE");
        $ACCESS_TOKEN = $this->getAccessToken($appid ,$secret);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=$ACCESS_TOKEN";
        $data=array('touser'=>$open_id,
            'template_id'=>$template_id,
            'page'=> $page,
            'form_id'=>$form_id,
            'data'=>$param
        );
        $data = json_encode($data);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch))
        {
            return curl_error($ch);
        }
        curl_close($ch);
        
        return $tmpInfo;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Service\BaseService::add()
     */
    public function add($data)
    {
        $this->wxmsgModel->where(array(
            'u_user_id' => $data['u_user_id']
        ))->delete() ;
        return $this->wxmsgModel->data($data)->add() ;        
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
        
    }

    
}