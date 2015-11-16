<?php

/**
* pullword分词API请求SDK，默认为调试模式，可以通过setMode(0)设置模式为正常模式
* 原网址：http://www.pullword.com/
* @author 陈奇波 <826802085@qq.com>
* @date   2015-11-16
*/
class PullWord
{   
    protected $apiUrl = 'http://api.pullword.com/post.php';
    private $curl;
    private $param1 = 0;
    private $param2 = 1;
    private $source;

    function __construct()
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $this->apiUrl);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_POST, 1);
    }

    /**
     * 设置模式，0为正常模式，1或者非零为调试模式，带有概率
     * @param integer $mode  0或者非零， 1：调试模式， 0：正常模式     
     */
    public function setMode($mode)
    {
        if($mode){
            $this->param1 = 0;
            $this->param2 = 1;
        }else{
            $this->param1 = 0;
            $this->param2 = 0;
        }
        return $this;
    }

    /**
     * 获取模式
     * @return integer 1：调试模式， 0：正常模式
     */
    public function getMode()
    {
        if($this->param2 == 1){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * 设置要抽词的内容
     * @param string $text 
     */
    public function setSource($text)
    {
        $this->source = $text;
        return $this;
    }

    /**
     * 获取要抽取的字符串
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * 组织post 参数
     * @return array 
     */
    private function _postData(){
        return $data = array('param1' => $this->param1,
                             'param2' => $this->param2,
                             'source' => $this->source,
                        );

    }

    /**
     * 接卸结果，返回数组
     * @param  string $data api调用返回的结果
     * @return array       
     */
    private function _parse($data)
    {
        $result = [];
        $parseData = trim(substr($data, strpos($data, ')')));
        if($this->getMode()){
            //调试模式，显示概率
            $tempArr = explode("\r\n", $parseData);
            foreach ($tempArr as $arr) {
                $exArr = explode(":", $arr);
                $result[$exArr[0]] =  $exArr[1];
            }
        }else{
            //正常模式
           $result = explode("\r\n", $parseData);
        }

        return $result;
    }

    /**
     * 发起post请求
     * @param  string $text 要抽词的字符串
     * @return array       返回的词组
     */
    public function pull($text)
    {
        $this->setSource($text);
        $postData = $this->_postData();
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postData);
        $output  = curl_exec($this->curl);
        $dataArray = $this->_parse($output);
        return $dataArray;
    }

    /**
     * 与pull同名
     * @param  string $text 要抽词的字符串
     * @return array       返回的数组
     */
    public function pullArray($text)
    {
        return $this->pull($text);
    }

    /**
     * 发起抽词请求，并将结果以json返回
     * @param  string $text 要抽词的字符串
     * @return json       返回json
     */
    public function pullJson($text){
        return json_encode($this->pull($text));
    }

    function __destruct(){
        curl_close($this->curl);
    }

}