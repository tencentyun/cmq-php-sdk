<?php

class CMQExceptionBase extends RuntimeException
{
    /*
    @type code: int
    @param code: 错误类型

    @type message: string
    @param message: 错误描述

    @type data: array
    @param data: 错误数据
    */

    public $code;
    public $message;
    public $data;

    public function __construct($message, $code=-1, $data=array())
    {
        parent::__construct($message, $code, $previousException);
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public function __toString()
    {
        return "CMQExceptionBase  " .  $this->get_info();
    }

    public function get_info()
    {
        $info = array("code" => $this->code,
                     "data" => json_encode($this->data),
                     "message" => $this->message);
        return json_encode($info);
    }
}

class CMQClientException extends CMQExceptionBase
{
    public function __construct($message, $code=-1, $data=array())
    {
        parent::__construct($message, $code, $data);
    }

    public function __toString()
    {
        return "CMQClientException  " .  $this->get_info();
    }
}

class CMQClientNetworkException extends CMQClientException
{
    /* 网络异常

        @note: 检查endpoint是否正确、本机网络是否正常等;
    */
    public function __construct($message, $code=-1, $data=array())
    {
        parent::__construct($message, $code, $data);
    }

    public function __toString()
    {
        return "CMQClientNetworkException  " .  $this->get_info();
    }
}

class CMQClientParameterException extends CMQClientException
{
    /* 参数格式错误

        @note: 请根据提示修改对应参数;
    */
    public function __construct($message, $code=-1, $data=array())
    {
        parent::__construct($message, $code, $data);
    }

    public function __toString()
    {
        return "CMQClientParameterException  " .  $this->get_info();
    }
}

class CMQServerNetworkException extends CMQExceptionBase
{
    //服务器网络异常

    public $status;
    public $header;
    public $data;

    public function __construct($status = 200, $header = NULL, $data = "")
    {
        if ($header == NULL) {
            $header = array();
        }
        $this->status = $status;
        $this->header = $header;
        $this->data = $data;
    }

    public function __toString()
    {
        $info = array("status" => $this->status,
                     "header" => json_encode($this->header),
                     "data" => $this->data);

        return "CMQServerNetworkException  " . json_encode($info);
    }
}

class CMQServerException extends CMQExceptionBase
{
    /* cmq处理异常

        @note: 根据code进行分类处理，常见错误类型：
             : 4000       参数不合法
             : 4100       鉴权失败:密钥不存在/失效
             : 4300       账户欠费了
             : 4400       消息大小超过队列属性设置的最大值
             : 4410       已达到队列最大的消息堆积数
             : 4420       qps限流
             : 4430       删除消息的句柄不合法或者过期了
             : 4440       队列不存在
             : 4450       队列个数超过限制
             : 4460       队列已经存在
             : 6000       服务器内部错误
             : 6010       批量删除消息失败（具体原因还要看每个消息删除失败的错误码）
             : 7000       空消息，即队列当前没有可用消息
             : 更多错误类型请登录腾讯云消息服务官网进行了解；
    */

    public $request_id;
    public function __construct($message, $request_id, $code=-1, $data=array())
    {
        parent::__construct($message, $code, $data);
        $this->request_id = $request_id;
    }

    public function __toString()
    {
        return "CMQServerException  " .  $this->get_info() . ", RequestID:" . $this->request_id;
    }
}

?>
