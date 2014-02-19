<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author      studentdeng 
 * @link        <studentdeng.github.com> 
 * @datetime    Feb 19, 2014 
 */
require APPPATH . '/libraries/CUREST_Controller.php';
require APPPATH . '/third_party/PresenceRedisHelper.php';

define('ONLINE_TIMEOUT', 60 * 40); //在线自动超时时间 40 分钟

class Presence extends CUREST_Controller
{
    public function online_get()
    {
        $inputParam = array('user_id');
        $paramValues = $this->gets($inputParam);
        $userId = $paramValues['user_id'];
        
        $this->online($userId);
        
        $this->responseSuccess('online');
    }
    
    public function offline_get()
    {
        $inputParam = array('user_id');
        $paramValues = $this->gets($inputParam);
        $userId = $paramValues['user_id'];
        
        $this->offline($userId);
        $this->responseSuccess('offline'.$userId);
    }
    
    public function rank_get()
    {
        $inputParam = array('user_id');
        $paramValues = $this->gets($inputParam);
        $userId = $paramValues['user_id'];
        
        $dataList =  $this->onlineDataList();
        $this->responseSuccess(array('number' => count($dataList)));
    }
    
    function online($userId)
    {
        $redisHelper = new PresenceRedisHelper();
        $redis = $redisHelper->connect();

        $key = $redisHelper->onlineListKey();
        $redis->zAdd($key, time(), $userId);

    }
    
    function offline($userId)
    {
        $redisHelper = new PresenceRedisHelper();
        $redis = $redisHelper->connect();

        $key = $redisHelper->onlineListKey();
        $redis->zDelete($key, time(), $userId);
    }
    
    function onlineDataList()
    {
        $redisHelper = new PresenceRedisHelper();
        $redis = $redisHelper->connect();

        $key = $redisHelper->onlineListKey();
        
        //删除过期的请求
        $redis->zRemRangeByScore($key, '-inf', time() - ONLINE_TIMEOUT);
        $list = $redis->zRangeByScore($key, time() - ONLINE_TIMEOUT, '+inf');

        $result_list = array_reverse($list, false);
        
        return $result_list;
    }
}