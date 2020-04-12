<?php
namespace Common\Service ;
class CommonWXService {
    protected function getAccessToken($appId,$appSecret){
        return S('WXContentCheckServiceToken'.$appId) ;
    }
}