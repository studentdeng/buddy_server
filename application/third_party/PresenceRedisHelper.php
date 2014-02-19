<?php

class PresenceRedisHelper
{
    function connect()
    {
        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        
        return $redis;
    }
    
    /**
     * 在线列表 (zset)
     * 
     * @return string
     */
    function onlineListKey()
    {
        return 'presence:online_list';
    }
    
    /**
     * 指定玩家在游戏大厅被其他玩家邀请的列表 (list)
     * 
     * @param type $userid
     * @return type
     */
    function hallInvitingListKeyForUserid($userid)
    {
        return 'hall:inviting_list:'.$userid;
    }
    
    /**
     * 指定玩家收到的其他玩家的邀请 (zset)
     * 
     * @param type $userid
     */
    function requestInvitingListKeyForUserid($userid)
    {
        return 'request:inviting_list:'.$userid;
    }
    
    /**
     * 游戏room等待 （list）
     * 
     * @param type $gameId
     */
    function requestResultKeyForGameId($gameId)
    {
        return 'request:gameroom:'.$gameId;
    }
    
    /**
     * 指定的gameid下的已经出来的游戏结果 (set)
     * 
     * @param type $gameId
     * @return type
     */
    function requestResultSavedForGameId($gameId)
    {
        return 'request:gameresult:'.$gameId;
    }
    
    /**
     * 指定用户获胜场次
     * 
     * @param type $userid
     * @return type
     */
    function accountWinCount($userid)
    {
        return 'account:'.$userid.'winCount';
    }
    
    /**
     * 指定用户失败场次
     * 
     * @param type $userid
     * @return type
     */
    function accountloseCount($userid)
    {
        return 'account:'.$userid.'loseCount';
    }
}