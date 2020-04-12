<?php
namespace Home\Controller ;
use Home\Service\OrderService;
use Home\Service\ShopGoodService;
use Home\Service\TicketService;
use Think\Controller;
use Think\Log;
use Home\Service\MessageService;
use Home\Service\RoleService;
use Home\Adapter\CoreAdapter;
use Home\Service\UserService;
use Home\Service\UserOrderLocationService;

class NoticeController extends Controller {
    public function __construct(){
        parent::__construct() ;
        
    }
    public function main(){
        $rr = '2020-1-4' ;
        $tmp_arr = explode('-', $rr) ;
        if(!empty($tmp_arr[1]) && strlen($tmp_arr[1]) ==1){
            $tmp_arr[1] = '0'.$tmp_arr[1] ;
        }
        if(!empty($tmp_arr[2]) && strlen($tmp_arr[2]) == 1){
            $tmp_arr[2] = '0'.$tmp_arr[2] ;
        }
        
        var_dump(join('-', $tmp_arr));
        echo "认证失败，不可访问！";
    }
    /**
     * 微信支付的异步回调通知
     */
    public function index(){
        $postXml = empty($GLOBALS["HTTP_RAW_POST_DATA"])?file_get_contents("php://input"):$GLOBALS["HTTP_RAW_POST_DATA"]; //接收微信参数
        if (empty($postXml)) {
            echo 'error';
        }else{
            $attr = xmlToArray($postXml);
            
            Log::record("微信支付异步回调参数".json_encode($attr)) ;
            Log::record("微信支付异步回调参数".$attr) ;
            $total_fee = $attr['total_fee'];
            $open_id = $attr['openid'];
            $out_trade_no = $attr['out_trade_no'];
            
            $orderService = new OrderService() ;
            $order_info = $orderService->getOrderByCode($out_trade_no) ;
            if(empty($order_info)){
               Log::record("回调中获取订单数据失败".$out_trade_no.',结果：'.json_encode($order_info)) ;
               
               echo 'error';
            }
            if ($attr['return_code'] == 'SUCCESS'){ //通过二次签名认证
               
                $orderService->update(array(
                    'u_state' => 1 ,
                    'u_pay_time' => getCurrentTime()
                ), $order_info['u_id']) ;
                
                $goodService = new ShopGoodService();
                $goodService->setInc($order_info['u_good_id'],$order_info['u_number']) ;
                $good_info = $goodService->getGoodInfoById($order_info['u_good_id']) ;
                
                
                $ticketService = new TicketService() ;
                $updateTicketList = $ticketService->getUpdateTicketList($order_info['u_good_id'],$order_info['u_user_id']) ;
                for ($i=0;$i<count($updateTicketList);$i++){
                    $ticketService->updateUserTicket(array(
                        'u_used' => 1
                    ),$updateTicketList[$i]['u_id']) ;
                }
                
                
                
                
              /*   if(!empty($order_info['u_ticket_id'])){
                    //有优惠券的话，则将该优惠券失效
                    $ticketService->updateUserTicketByUserIDTicketID($order_info['u_user_id'], $order_info['u_ticket_id']) ;
                }
                 */
                //发送一个消息给用户
                $messageService = new MessageService() ;
                $messageService->add(array(
                    'u_user_id' => $order_info['u_user_id'] ,
                    'u_content' => '您购买的'.$good_info['u_name'].'已经成功付款'.$order_info['u_total_price'].'元！' 
                )) ;
                
                //调用java模板消息接口
                try {
                    $userService = new UserService() ;
                    $user_info = $userService->getInfoById($order_info['u_user_id']) ;
                    $coreAdapter = new CoreAdapter() ;
                    
                    //判断是否为闲置接口，如果是，则发送给闲置商品上架人
                    if($good_info['u_source'] == 'user' && !empty($good_info['u_user_id'])){
                        $admin_user_info = $userService->getInfoById($good_info['u_user_id']) ;
                        $coreAdapter->pushUserOrderMsg($admin_user_info['u_open_id'], 'pages/index/shop/mySaleGoodOrderList', $user_info['u_nick_name'], $order_info['u_total_price'], date("Ymd H:i:s",time()), $order_info['u_code']) ;
                    }else{ //直营商品，直接发送给管理员
                        $admin_opens = C('ADMIN_USER_OPEN');
                        for ($i=0;$i<count($admin_opens);$i++){
                            $coreAdapter->pushOrderMsg('pages/index/index', $admin_opens[$i], date("Y-m-d H:i",time()), $order_info['u_number'],$order_info['u_total_price'],$order_info['u_mark']) ;
                        }
                    }
                    $userLocationService = new UserOrderLocationService() ;
                    $orderLocation = $userLocationService->getLocationInfoByOrderID($order_info['u_id']) ;
                    $coreAdapter->pushPayMsg($user_info['u_open_id'], "pages/index/shop/orderList", $good_info['u_name'], $order_info['u_total_price'], date("Y-m-d H:i",time()), $orderLocation['u_address'], $order_info['u_mark']) ;
                
                   
                }catch (\Exception $e){
                    Log::record("发送模板消息接口调用失败".$e->getMessage()) ;   
                }
                
                echo 'success';
            }else{
                
                $goodService = new ShopGoodService();
                $good_info = $goodService->getGoodInfoById($order_info['u_good_id']) ;
                
                //发送一个消息给用户
                $messageService = new MessageService() ;
                $messageService->add(array(
                    'u_user_id' => $order_info['u_user_id'] ,
                    'u_content' => '您购买的'.$good_info['u_name'].'付款失败，在我的订单中重新发起付款！' 
                )) ;
                
                
                $orderService->update(array(
                    'u_state' => 3 ,
                    'u_pay_time' => getCurrentTime()
                ), $order_info['u_id']) ;
                echo 'error';
            }
        }
    }
    
    
    /**
     * 超级VIP微信支付的异步回调通知
     */
    public function vipIndex(){
        $postXml = empty($GLOBALS["HTTP_RAW_POST_DATA"])?file_get_contents("php://input"):$GLOBALS["HTTP_RAW_POST_DATA"]; //接收微信参数
        if (empty($postXml)) {
            echo 'error';
        }else{
            $attr = xmlToArray($postXml);
            
            Log::record("微信支付异步回调参数".json_encode($attr)) ;
            Log::record("微信支付异步回调参数".$attr) ;
            $total_fee = $attr['total_fee'];
            $open_id = $attr['openid'];
            $out_trade_no = $attr['out_trade_no'];
            
            $orderService = new OrderService() ;
            $order_info = $orderService->getOrderByCode($out_trade_no) ;
            if(empty($order_info)){
                Log::record("回调中获取订单数据失败".$out_trade_no.',结果：'.json_encode($order_info)) ;
                
                echo 'error';
            }
            if ($attr['return_code'] == 'SUCCESS'){ //通过二次签名认证
                
                $orderService->update(array(
                    'u_state' => 1 ,
                    'u_pay_time' => getCurrentTime()
                ), $order_info['u_id']) ;
                
                
                
                if(!empty($order_info['u_ticket_id'])){
                    //有优惠券的话，则将该优惠券失效
                    $ticketService = new TicketService() ;
                    $ticketService->updateUserTicketByUserIDTicketID($order_info['u_user_id'], $order_info['u_ticket_id']) ;
                }
                
                //发送一个消息给用户
                $messageService = new MessageService() ;
                $messageService->add(array(
                    'u_user_id' => $order_info['u_user_id'] ,
                    'u_content' => '您购买的超级VIP已经成功付款'.$order_info['u_total_price'].'元！'
                )) ;
                
                
                //更新用户VIP状态
                $roleService = new RoleService() ;
                $roleInfo = $roleService->getRoleByCate('2') ;
                $roleService->addUserVip(array(
                    'u_user_id' => $order_info['u_user_id'] ,
                    'u_role_id' => $roleInfo['u_id']
                )) ;
                
                
                echo 'success';
            }else{
                //发送一个消息给用户
                $messageService = new MessageService() ;
                $messageService->add(array(
                    'u_user_id' => $order_info['u_user_id'] ,
                    'u_content' => '您购买的超级VIP付款失败，在我的订单中重新发起付款！'
                )) ;
                
                $orderService->update(array(
                    'u_state' => 3 ,
                    'u_pay_time' => getCurrentTime()
                ), $order_info['u_id']) ;
                echo 'error';
            }
        }
    }
}