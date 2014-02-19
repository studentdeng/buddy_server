<?php
/**
 * @author      studentdeng 
 * @link        <studentdeng.github.com> 
 * @datetime    Jul 24, 2013 
 */

class User_model extends CI_Model
{
    /**
     * 查询用户，存在返回相关用户信息，否则返回false
     * 
     * @param string $username  
     * @param string $password  (orginal password)
     * @return array|bool
     */
    function checkUserExist($username, $password)
    {
        $encryptPassword = $password;
        $db = $this->load->database('account', TRUE);
        $db->where('username', $username);
        $db->where('password', $encryptPassword);
        $query = $db->get('user');
        $db->close();
        
        if ($query->num_rows() == 0)
        {
            return FALSE;
        }
        
        return $query->row_array();
    }
}
