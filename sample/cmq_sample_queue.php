<?php
require_once '../cmq/cmq_api.php';
require_once CMQAPI_ROOT_PATH . '/account.php';
require_once CMQAPI_ROOT_PATH . '/queue.php';
require_once CMQAPI_ROOT_PATH . '/cmq_exception.php';
/*
 * CMQ_V1.0.2 PHP Demo
 * 
 *  1 Account类对象不是线程安全的，如果多线程使用，需要每个线程单独初始化Account类对象
 *  2 Topic与Queue使用不同的endpoint, 因此需要需要分别初始化Account
 *  3 创建订阅的时候，需要设置订阅的属性，订阅属性参见SubscriptionMeta的定义
 */



class QueueDemo
{
    private $secretId;
    private $secretKey;
    private $endpoint;

    public function __construct($secretId, $secretKey, $endpoint)
    {
        $this->secretId = $secretId;
        $this->secretKey = $secretKey;
        $this->endpoint = $endpoint;
    }

    public function run()
    {        

            $queue_name = "MySampleQueue1";
            $my_account = new Account($this->endpoint, $this->secretId, $this->secretKey);

            $my_queue = $my_account->get_queue($queue_name);

            $queue_meta = new QueueMeta();
            $queue_meta->queueName = $queue_name;
            $queue_meta->pollingWaitSeconds = 10;
            $queue_meta->visibilityTimeout = 10;
            $queue_meta->maxMsgSize = 1024;
            $queue_meta->msgRetentionSeconds = 3600;

            try
            { 
                $my_queue->create($queue_meta);
                echo "Create Queue Succeed! \n" . $queue_meta . "\n";

                $my_queue->set_attributes($queue_meta);
                echo "Set Queue Attributes Succeed! QueueMeta:" . $queue_meta . "\n";

                $result = $my_account->list_queue();
                echo "List Queue Succeed! result: " . json_encode($result) . "\n";

                $queue_meta = $my_queue->get_attributes();
                echo "Get Queue Attributes Succeed! QueueMeta:" . $queue_meta . "\n";

                $msg_body = "I am test message.";
                $msg = new Message($msg_body);
                $re_msg = $my_queue->send_message($msg);
                echo "Send Message Succeed! MessageBody:" . $msg_body . " MessageID:" . $re_msg->msgId . "\n";

                $recv_msg = $my_queue->receive_message(3);
                echo "Receive Message Succeed! " . $recv_msg . "\n";

                $messages = array();
                for ($i=0; $i<3; $i++) {
                    $msg_body = "I am test message " . $i;
                    $msg = new Message($msg_body);
                    $messages [] = $msg;
                                                    
                }
                $re_msg_list = $my_queue->batch_send_message($messages);
                echo "Batch Send Message Succeed! " . json_encode($re_msg_list);

                $wait_seconds = 3;
                $num_of_msg = 3;
                $recv_msg_list = $my_queue->batch_receive_message($num_of_msg, $wait_seconds);
                echo "Batch Receive Message Succeed! " . json_encode($recv_msg_list) . "\n";

                $my_queue->delete();
                                                                            
                }
                catch (CMQExceptionBase $e)
                {
                echo "Create Queue Fail! Exception: " . $e;
                return;
                                                                            
                }
    }
}
// 从腾讯云官网查看云api的密钥信息
$secretId = "";
$secretKey = "";
$endPoint = "";

$instance = new  QueueDemo($secretId, $secretKey, $endPoint);
$instance->run();
