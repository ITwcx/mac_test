<?php 
// .-----------------------------------------------------------------------------------
// | Rewrite By Megustas < 358125651@qq.com >
// |-----------------------------------------------------------------------------------

namespace Com;

class MyWechatAuth {


    /**
     * 微信APP_ID     公众号的唯一标识 
     * @var string
     */
    private static $appId = '';

    /**
     * 第三方用户唯一凭证密钥
     * @var string
     */
    private static $appSecret = '';

    /**
     * 微信api根路径
     * @var string
     */
    const API_URL_PREFIX        = 'https://api.weixin.qq.com/cgi-bin';
    const AUTH_URL              = '/token';
    const GET_TICKET_URL        = '/ticket/getticket';
    const USER_INFO_URL         = '/user/info';

    /**
     * 微信Code根路径
     * @var string
     */
    const OAUTH_PREFIX          = 'https://open.weixin.qq.com/connect/oauth2';
    const OAUTH_AUTHORIZE_URL   = '/authorize';

    /**
     * 微信网页授权根路径
     * @var string
     */
    const API_BASE_URL_PREFIX   = 'https://api.weixin.qq.com';              // 以下API接口URL需要使用此前缀
    const OAUTH_TOKEN_URL       = '/sns/oauth2/access_token';
    const OAUTH_USERINFO_URL    = '/sns/userinfo';
    const OauthApiURL = 'https://api.weixin.qq.com/sns';

    /**
     * 构造方法，调用微信高级接口时实例化SDK
     * @param string $options   微信appid，微信appsecret，
     * @param string $code      
     */
    public function __construct ($options){

        self::$appId            = isset($options['appid'            ]) ? $options['appid'           ] : '';
        self::$appSecret        = isset($options['appsecret'        ]) ? $options['appsecret'       ] : '';

        if (empty(self::$appId)) {
            throw new \Exception('缺少参数AppId！');
        }

        if (empty(self::$appSecret)) {
            throw new \Exception('缺少参数AppSecret！');
        }

    }

    /**
     * 获取access_token，公众号的全局唯一接口调用凭据，用于后续接口访问
     * @return string access_token
     */
    public function requestAccessToken ($type = 'client', $code = null) {

        switch ($type) {

            case 'client':

                $accesstoken = S('MyWechatApi_Access_Token' . self::$appId);

                if (!empty($accesstoken)) {
                    return $accesstoken;
                }

                $param = array(
                    'appid'         => self::$appId,
                    'secret'        => self::$appSecret,
                    'grant_type'    => 'client_credential'
                );
                // https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=1231312&secret=1231231
                $url = self::API_URL_PREFIX . self::AUTH_URL;

                break;

            case 'code':

                $param = array(
                    'appid'         => self::$appId,
                    'secret'        => self::$appSecret,
                    'code'          => $code,
                    'grant_type'    => 'authorization_code'
                );

                // https://api.weixin.qq.com/sns/oauth2/access_token?grant_type=authorization_code&&appid=1231312&secret=1231231&code=123
                $url = self::API_BASE_URL_PREFIX . self::OAUTH_TOKEN_URL;

                break;

            case 'mini':

                $param = array(
                    'appid'         => self::$appId,
                    'secret'        => self::$appSecret,
                    'js_code'       => $code,
                    'grant_type'    => 'authorization_code'
                );
                
                $url = 'https://api.weixin.qq.com/sns/jscode2session';

                break;
            
            default:

                throw new \Exception('不支持的grant_type类型！');
                break;

        }

        $result = json_decode(self::http($url, $param), true);

        if (is_array($result)) {

            if (isset($result['errcode'])) {

                throw new \Exception($result['errmsg']);

            } else {

                if ($type == 'client') {

                    $expire = $result['expires_in'] ? intval($result['expires_in']) - 100 : 6000;
                    S('MyWechatApi_Access_Token' . self::$appId, $result['access_token'], $expire);

                    return $result['access_token'];

                } else if ($type == 'code') {

                    return $result;

                }
                

            }

        } else {

            throw new \Exception('获取微信access_token失败！');

        }

    }

    /**
     * 静默获取当前用户OPENID
     * @param  string $redirect_uri 回调地址
     * @return string 用户openid
     */
    public function requestOpenidByOauth ($redirect_uri) {

        if (empty($_GET['code'])) {

            header('Location: ' . $this->requestCodeURL($redirect_uri, '911'));
            exit;

        }

        $access_token_array = $this->requestAccessToken('code', $_GET['code']);

        return $access_token_array['openid'];

    }

    /**
     * 通过授权获取当前用户的详细信息
     * @param  string $redirect_uri 回调地址
     * @param  string $lang   指定的语言
     * @return array          用户信息数据，具体参见微信文档
     */
    public function requestUserInfoByOauth ($redirect_uri, $lang = 'zh_CN') {

        if (empty($_GET['code'])) {

            header('Location: ' . $this->requestCodeURL($redirect_uri, '731', 'snsapi_userinfo'));
            exit;

        }

        $access_token_array = $this->requestAccessToken('code', $_GET['code']);

        $query = array(
            'access_token' => $access_token_array['access_token'],
            'openid'       => $access_token_array['openid'],
            'lang'         => 'zh_CN',
        );

        $url = self::API_BASE_URL_PREFIX . self::OAUTH_USERINFO_URL;

        $info = self::http($url, $query);
        
        return json_decode($info, true);

    }

    /**
     * 通过openid获取指定用户的详细信息
     * @param  string $openid 用户的openid
     * @param  string $lang   需要获取数据的语言
     * @return array          用户信息数据，具体参见微信文档
     */
    public function requestUserInfoByOpenId ($openid, $lang = 'zh_CN') {

        $param = array('openid' => $openid, 'lang' => $lang);
        $info = $this->api(self::USER_INFO_URL, '', 'GET', $param);
        return $info;

    }

    /**
     * 获取card_ext，卡券扩展字段
     * @return array card_ext
     */
    public function requestCardExt ($url = null) {

        $url            = $url ? $url : 'http://' . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
        $jsapiTicket    = $this->requestJsApiTicket();
        $nonceStr       = $this->createNonceStr();
        $timestamp      = time();
        $rawString      = 'jsapi_ticket=' . $jsapiTicket . '&noncestr=' . $nonceStr . '&timestamp=' . $timestamp . '&url=' . $url;
        $signature      = sha1($rawString);

        $signPackage = array (
            'url'       => $url,
            'appId'     => self::$appId,
            'nonceStr'  => $nonceStr,
            'timestamp' => $timestamp,
            'rawString' => $string,
            'signature' => $signature
        );

        return $signPackage; 

    }

    /**
     * 获取api_ticket，卡卷签名
     * @return string api_ticket
     */
    public function requestJsApiTicket () {

        $jsapiticket = S('MyWechatApi_JsApi_Ticket' . self::$appId);

        if (!empty($jsapiticket)) {
            return $jsapiticket;
        }

        $param = array(
            'type'  => 'jsapi',
            'access_token' => $this->requestAccessToken()
        );

        $url = self::API_URL_PREFIX . self::GET_TICKET_URL;

        $result = json_decode(self::http($url, $param), true);

        if (is_array($result)) {

            if (isset($result['ticket'])) {

                $expire = $result['expires_in'] ? intval($result['expires_in']) - 100 : 6000;
                S('MyWechatApi_JsApi_Ticket' . self::$appId, $result['ticket'], 6000);
                return $result['ticket'];

            } else {

                throw new \Exception($result['errmsg']);

            }

        } else {

            throw new \Exception('获取微信ticket失败！');

        }

    }

    /**
     * 产生随机字符串，不长于16位
     * @param int $length
     * @return 产生的随机字符串
     */
    public function createNonceStr ($length = 16) {

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str   = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;

    }

    /**
     * 获取Code授权地址
     * @param  string $redirect_uri 回调地址
     * @param  string $state   状态 可空
     * @param  string $scope   授权方式 snsapi_base, snsapi_userinfo
     * @return array          用户信息数据，具体参见微信文档
     */
    public function requestCodeURL ($redirect_uri, $state = null, $scope = 'snsapi_base') {

        $query = array(
            'appid'         => self::$appId,
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'code',
            'scope'         => $scope
        );

        if(!is_null($state) && preg_match('/[a-zA-Z0-9]+/', $state)){
            $query['state'] = $state;
        }

        $query = http_build_query($query);
        return self::OAUTH_PREFIX . self::OAUTH_AUTHORIZE_URL . '?' . $query . '#wechat_redirect';

    }

    /**
     * 发送HTTP请求方法
     * @param  string $url    请求URL
     * @param  array  $param  GET参数数组
     * @param  array  $data   POST的数据，GET请求时该参数无效
     * @param  string $method 请求方法GET/POST
     * @return array          响应数据
     */
    protected static function http ($url, $param, $data = '', $method = 'GET') {

        $opts = array (
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_SSLVERSION     => CURL_SSLVERSION_TLSv1
        );

        /* 根据请求类型设置特定参数 */
        $opts[CURLOPT_URL] = $url . '?' . http_build_query($param);

        if (strtoupper($method) == 'POST') {
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $data;
            
            if (is_string($data)) { 
                //发送JSON数据
                $opts[CURLOPT_HTTPHEADER] = array(
                    'Content-Type: application/json; charset=utf-8',  
                    'Content-Length: ' . strlen($data),
                );
            }
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        //发生错误，抛出异常
        if($error) throw new \Exception('请求发生错误：' . $error);

        return  $data;

    }

    /**
     * 调用微信api获取响应数据
     * @param  string $name   API名称
     * @param  string $data   POST请求数据
     * @param  string $method 请求方式
     * @param  string $param  GET请求参数
     * @return array          api返回结果
     */
    protected function api ($name, $data = '', $method = 'POST', $param = '', $json = true) {

        $params = array('access_token' => $this->requestAccessToken());

        if (!empty($param) && is_array($param)) {
            $params = array_merge($params, $param);
        }

        $url  = self::API_URL_PREFIX . $name;

        if($json && !empty($data)){
            //保护中文，微信api不支持中文转义的json结构
            array_walk_recursive($data, function(&$value){
                $value = urlencode($value);
            });
            $data = urldecode(json_encode($data));
        }

        $data = self::http($url, $params, $data, $method);

        return json_decode($data, true);

    }

}

 ?>