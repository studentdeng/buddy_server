<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CURESTController
 *
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Phil Sturgeon
 * @license         http://philsturgeon.co.uk/code/dbad-license
 * @link			https://github.com/philsturgeon/codeigniter-restserver
 * @version 		2.6.0
 */
require APPPATH . '/libraries/REST_Controller.php';

abstract class CUREST_Controller extends REST_Controller
{

    public $auth_user_password;
    public $auth_user_username;
    public $auth_user_data;
    protected $rest_format = 'json';

    public function gets($inputParam)
    {
        $paramValues = array();

        foreach ($inputParam as $value)
        {
            $item = $this->get($value);
            if ($item === FALSE)
            {
                $this->responseError('400', $value . ' must be set');
            }

            $paramValues[$value] = $this->get($value);
        }

        return $paramValues;
    }

    public function gets_defaults($defaultParams)
    {
        $paramInput = array();
        foreach ($defaultParams as $key => $value)
        {
            if ($this->get($key) !== FALSE)
            {
                $paramInput[$key] = $this->get($key);
            }
        }

        return array_merge($defaultParams, $paramInput);
    }

    public function posts($inputParam, $xss_clean = TRUE)
    {
        $paramValues = array();

        foreach ($inputParam as $value)
        {
            $item = $this->post($value);
            if ($item === FALSE)
            {
                $this->responseError('400', $value . ' must be set');
            }

            $paramValues[$value] = $this->post($value, $xss_clean);
        }

        return $paramValues;
    }

    public function posts_defaults($defaultParams)
    {
        $paramInput = array();
        foreach ($defaultParams as $key => $value)
        {
            if ($this->post($key) !== FALSE)
            {
                $paramInput[$key] = $this->post($key);
            }
        }

        return array_merge($defaultParams, $paramInput);
    }

    public function getWithDefaultValue($key, $defaultValue)
    {
        $value = $this->get($key);
        return $value === FALSE ? $defaultValue : $value;
    }

    public function postWithDefaultValue($key, $defaultValue)
    {
        $value = $this->post($key);
        return $value === FALSE ? $defaultValue : $value;
    }

    public function HTTPBaseAuth()
    {
        if (!$this->basicAuthProcessHeader())
        {
            $this->responseError(401, 'auth info not set');
        }

        return $this->checkAuthFromDB();
    }

    function basicAuthProcessHeader()
    {
        if (isset($_SERVER['PHP_AUTH_USER']))
        {
            $this->auth_user_username = $_SERVER['PHP_AUTH_USER'];
            $this->auth_user_password = $_SERVER['PHP_AUTH_PW'];

            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function checkAuthFromDB()
    {
        $this->load->model('User_model');
        $this->auth_user_data = $this->User_model->checkUserExist(
                $this->auth_user_username, $this->auth_user_password
        );

        if ($this->auth_user_data === FALSE)
        {
            $this->responseError(401, 'username and password not match');
        }
    }

    public function sessionAuth()
    {
        $phpsessid = $this->get('PHPSESSID');
        if (!empty($phpsessid))
        {
            session_id($phpsessid);
        }

        @session_start();
        if (!isset($_SESSION['login']))
        {
            //user is not login in session
            //we user http auth to check
            // if auth failed will response 401
            $this->HTTPBaseAuth();

            $_SESSION['login'] = TRUE;
            $_SESSION['username'] = $this->auth_user_username;
            $_SESSION['password'] = $this->auth_user_password;
        }
        else
        {
            $this->auth_user_username = $_SESSION['username'];
            $this->auth_user_password = $_SESSION['password'];

            $this->checkAuthFromDB();

            $_SESSION['login'] = TRUE;
            $_SESSION['username'] = $this->auth_user_username;
            $_SESSION['password'] = $this->auth_user_password;
        }

        return TRUE;
    }

    public function sessionOut()
    {
        session_start();

        if (isset($_SESSION['login']))
        {
            session_unset($_SESSION['login']);
        }

        if (isset($_SESSION['username']))
        {
            session_unset($_SESSION['username']);
        }

        if (isset($_SESSION['password']))
        {
            session_unset($_SESSION['password']);
        }

        session_unset();
        session_destroy();
    }

    public function clientError($message="")
    {
        $this->responseError(400, $message);
    }

    public function serverError($message)
    {
        $this->responseError(500, $message);
    }

    public function responseError($httpCode, $data = '')
    {
        $this->response(array('result' => 'error', 'data' => $data), $httpCode);
    }

    public function responseSuccess($data = '')
    {
        $this->response(array('result' => 'success', 'data' => $data), 200);
    }

    public function responseArray($array)
    {
        $this->responseSuccess(array('list' => $array));
    }

    public function responseBool($result)
    {
        $result ? $this->responseSuccess() : $this->serverError('data server error');
    }

    public function responseDecorateArray($array, $option = array())
    {
        $result = array();

        foreach ($array as $item)
        {
            $result[] = $this->decorate($item, $option);
        }

        return $this->responseArray($result);
    }

    public function decorate($item, $option)
    {
        return $item;
    }

}