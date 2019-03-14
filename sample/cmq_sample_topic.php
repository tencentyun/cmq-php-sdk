<?php
require_once '../cmq/cmq_api.php';
require_once CMQAPI_ROOT_PATH . '/account.php';
require_once CMQAPI_ROOT_PATH . '/topic.php';
require_once CMQAPI_ROOT_PATH . '/subscription.php';
require_once CMQAPI_ROOT_PATH . '/cmq_exception.php';
/*
 * CMQ_V1.0.2 PHP Demo
 * 
 *  1 Account类对象不是线程安全的，如果多线程使用，需要每个线程单独初始化Account类对象
 *  2 Topic与Queue使用不同的endpoint, 因此需要需要分别初始化Account
 *  3 创建订阅的时候，需要设置订阅的属性，订阅属性参见SubscriptionMeta的定义
 */



class TopicDemo
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
		try 
		{
			// create account and topic
			$topic_name = "Topic-test-php";
			$my_account = new Account($this->endpoint, $this->secretId, $this->secretKey);
			$my_topic = $my_account->get_topic($topic_name); 	
			$my_topicmeta = new TopicMeta();
			$my_topic->create($my_topicmeta);
			echo "create topic Topic-test-php success\n";


			$my_topicmeta = $my_topic->get_attributes();
			echo "get topic attributes\n";
			echo $my_topicmeta;
			echo "\n";

			$my_topicmeta->maxMsgSize=1024;
			$my_topic->set_attributes($my_topicmeta);

			// list topic
			$topiclist = $my_account->list_topic();
			var_dump($topiclist);

			// create subscription 
			$subscription_name = "subsc-test34324";
			$my_subscription = $my_account->get_subscription($topic_name, $subscription_name);
			$subscriptionmeta = new SubscriptionMeta();

			// get and set subscription meta 
			// please input your endpoint and protocol
			
			$subscriptionmeta->Endpoint="";
			$subscriptionmeta->Protocol="";
			$my_subscription->create($subscriptionmeta);
			echo "create subscription success\n";


			$subscriptionmeta = $my_subscription->get_attributes();
			echo "get subscription meta:\n";
			echo $subscriptionmeta;
			echo "\n";

			sleep(2);
			$my_subscription->set_attributes($subscriptionmeta);

			// publish message and batch publish message without tags 
			$msg = "this is a test message ";
			$msgid = $my_topic->publish_message($msg);
			echo "publish message without tag \n";     

			$vmsg = array();
			for ($i=0; $i<3; $i++) {
				$msg ="I am test message ";
				$vmsg [] = $msg;
			}

			$vmsgid = $my_topic->batch_publish_message($vmsg);
			echo "batch publish message without tags \n" ;

			// publish message  with tags 
			// tag define 
			$vtag = array("test","cmq","york");
			$msg = "this is a test message";
			$msgid = $my_topic->publish_message($msg, $vtag);
			echo "publish message with tag \n" ;
			$vmsg = array();
			for ($i=0; $i<3; $i++) {
				$msg ="I am test message " . $i;
				$vmsg [] = $msg;
			}


			$vmsgid = $my_topic->batch_publish_message($vmsg, $vtag);
			echo "batch publish message with tag \n";

			// delete subscription and topic 
			$my_subscription->delete();
			echo "delete subscription \n";
			$my_topic->delete();
			echo "delete topic \n";
		}
		catch(CMQExceptionBase $e)
		{
			echo $e;

		}


	}
}
// 从腾讯云官网查看云api的密钥信息
$secretId = "";
$secretKey = "";
$endPoint = "";

$instance = new  TopicDemo($secretId, $secretKey, $endPoint);
$instance->run();
