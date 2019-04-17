<?php
// .-----------------------------------------------------------------------------------
// | Rewrite By Megustas < 358125651@qq.com >
// |-----------------------------------------------------------------------------------

namespace Com;
use Com\WechatCrypt;

//支持在非ThinkPHP环境下使用
defined('NOW_TIME') || define('NOW_TIME', $_SERVER['REQUEST_TIME']);
defined('IS_GET')   || define('IS_GET',   $_SERVER['REQUEST_METHOD'] == 'GET');

class MyWechat {
    /**
     * 消息类型常量
     */
    const MSG_TYPE_NEWS             = 'news';           // 图文消息
    const MSG_TYPE_TEXT             = 'text';           // 文本消息
    const MSG_TYPE_LINK             = 'link';           // 链接消息
    const MSG_TYPE_IMAGE            = 'image';          // 图片消息
    const MSG_TYPE_EVENT            = 'event';          // 事件推送消息
    const MSG_TYPE_MUSIC            = 'music';          // 音乐消息
    const MSG_TYPE_VOICE            = 'voice';          // 语音消息
    const MSG_TYPE_VIDEO            = 'video';          // 视频消息
    const MSG_TYPE_SHORTVIDEO       = 'shortvideo';     // 小视频消息
    const MSG_TYPE_LOCATION         = 'location';       // 地理位置消息

    /**
     * 事件类型常量
     */
    const EVENT_SUBSCRIBE           = 'subscribe';          // 关注事件
    const EVENT_UNSUBSCRIBE         = 'unsubscribe';        // 取消关注事件
    const EVENT_SCAN                = 'SCAN';               // 扫描带参数二维码事件
    const EVENT_LOCATION            = 'LOCATION';           // 上报地理位置事件

    const EVENT_MENU_CLICK          = 'CLICK';              // 菜单 - 点击菜单跳转链接
    const EVENT_MENU_VIEW           = 'VIEW';               // 菜单 - 点击菜单拉取消息
    const EVENT_MENU_SCAN_PUSH      = 'scancode_push';      // 菜单 - 扫码推事件(客户端跳URL)
    const EVENT_MENU_SCAN_WAITMSG   = 'scancode_waitmsg';   // 菜单 - 扫码推事件(客户端不跳URL)
    const EVENT_MENU_PIC_SYS        = 'pic_sysphoto';       // 菜单 - 弹出系统拍照发图
    const EVENT_MENU_PIC_PHOTO      = 'pic_photo_or_album'; // 菜单 - 弹出拍照或者相册发图
    const EVENT_MENU_PIC_WEIXIN     = 'pic_weixin';         // 菜单 - 弹出微信相册发图器
    const EVENT_MENU_LOCATION       = 'location_select';    // 菜单 - 弹出地理位置选择器
    
    const API_URL_PREFIX                = 'https://api.weixin.qq.com/cgi-bin';
    const MENU_CREATE_URL               = '/menu/create';
    const USER_INFO_URL                 = '/user/info';
    const AUTH_URL                      = '/token';
    const MEDIA_GET_URL                 = '/media/get';
    const CUSTOM_SEND_URL               = '/message/custom/send';

    // 多客服相关地址
    const API_BASE_URL_PREFIX               = 'https://api.weixin.qq.com';              // 以下API接口URL需要使用此前缀
    const OAUTH_TOKEN_URL                   = '/sns/oauth2/access_token';

    /**
     * 微信推送过来的数据
     * @var array
     */
    private $data = array();

    /**
     * 微信TOKEN      用作生成签名
     * @var string
     */
    private static $token = '';

    /**
     * 微信APP_ID     公众号的唯一标识 
     * @var string
     */
    private static $appId = '';

    /**
     * 消息加密KEY  消息体加解密密钥
     * @var string
     */
    private static $encodingAESKey = '';

    /**
     * 第三方用户唯一凭证密钥
     * @var string
     */
    private static $appSecret = '';

    /**
     * 是否使用安全模式
     * @var boolean
     */
    private static $msgSafeMode = false;

    /**
     * 构造方法，用于实例化微信SDK
     * 自动回复消息时实例化该SDK
     * @param string $token 微信后台填写的TOKEN
     * @param string $appid 微信APPID (安全模式和兼容模式有效)
     * @param string $key   消息加密KEY (EncodingAESKey)
     */
    public function __construct ($options) {

        self::$token            = isset($options['token'            ]) ? $options['token'           ] : '';
        self::$encodingAESKey   = isset($options['encodingaeskey'   ]) ? $options['encodingaeskey'  ] : '';
        self::$appId            = isset($options['appid'            ]) ? $options['appid'           ] : '';
        self::$appSecret        = isset($options['appsecret'        ]) ? $options['appsecret'       ] : '';

        if (empty(self::$appSecret)) {
            throw new \Exception('缺少参数AppSecret！');
        }

        if (empty(self::$token)) {
            throw new \Exception('缺少参数Token！');
        } 

        if (empty(self::$appId)) {
            throw new \Exception('缺少参数EncodingAESKey或AppId！');
        }

    }

    public function valid () {

        // 设置安全模式
        if (isset($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
            self::$msgSafeMode = true;
        }

        // 参数验证
        if (self::$msgSafeMode) {

            if (empty(self::$encodingAESKey)) {
                throw new \Exception('缺少参数EncodingAESKey或AppId！');
            }

            self::$appId          = $appid;
            self::$encodingAESKey = $key;
        }

        self::auth(self::$token) || exit;

        if (IS_GET) {
            ob_clean();
            exit($_GET['echostr']);
        } else {
            self::$token = self::$token;
            $this->init();
        }

    }

    /**
     * 初始化微信推送的数据
     */
    private function init () {

        $xml  = file_get_contents("php://input");
        $data = self::xml2data($xml);

        //安全模式 或兼容模式
        if (self::$msgSafeMode) {

            if (isset($data['MsgType'])) {
                //兼容模式追加解密后的消息内容
                $data['Decrypt'] = self::extract($data['Encrypt']);
            } else { 
                //安全模式
                $data = self::extract($data['Encrypt']);
            }
        }

        $this->data = $data;

    }

    /**
     * 对数据进行签名认证，确保是微信发送的数据
     * @param  string $token 微信开放平台设置的TOKEN
     * @return boolean       true-签名正确，false-签名错误
     */
    protected static function auth ($token) {

        /* 获取数据 */
        $data = array($_GET['timestamp'], $_GET['nonce'], $token);
        $sign = $_GET['signature'];
        
        /* 对数据进行字典排序 */
        sort($data, SORT_STRING);

        /* 生成签名 */
        $signature = sha1(implode($data));
        return $signature === $sign;

    }

    /**
     * 获取微信推送的数据
     * @return array 转换为数组后的数据
     */
    public function request () {

        return $this->data;

    }

    /**
     * 获取消息发送者openid
     * @return any 消息发送者openid 或者 fail
     */
    public function requestFrom () {

        if (isset($this->data['FromUserName'])) {
            return $this->data['FromUserName'];
        } else {
            return false;
        }

    }

    /**
     * 获取接收消息的类型
     * @return any 消息的类型 或者 fail
     */
    public function requestType () {

        if (isset($this->data['MsgType'])) {
            return $this->data['MsgType'];
        } else {
            return false;
        }

    }

    /**
     * 获取接收消息内容正文
     * @return any 消息内容正文 或者 fail
     */
    public function requestContent(){

        if (isset($this->data['Content'])) {
            return $this->data['Content'];
        } else if (isset($this->data['Recognition'])) {
            //获取语音识别文字内容，需申请开通
            return $this->data['Recognition'];
        } else {
            return false;
        }

    }

    /**
     * 获取接收事件推送
     * @return any 消息事件推送 或者 fail
     */
    public function requestEvent () {

        if (isset($this->data['Event'])) {
            $array['event'] = $this->data['Event'];
        }
        if (isset($this->data['EventKey'])) {
            $array['key'] = $this->data['EventKey'];
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }

    }

    /**
    * 获取二维码的场景值
     * @return any 二维码的场景值 或者 fail
    */
    public function requestSceneId () {

        if (isset($this->data['EventKey'])) {
            return str_replace('qrscene_', '', $this->data['EventKey']);
        } else {
            return false;
        }

    }

    /**
    * 获取永久二维码的场景值
     * @return any 二维码的场景值 或者 fail
    */
    public function requestSceneStr () {

        if (isset($this->data['EventKey'])) {
            return str_replace('qrscene_', '', $this->data['EventKey']);
        } else {
            return false;
        }

    }

    /**
     * 获取关注者详细信息
     * @param string $openid
     * @return array {subscribe,openid,nickname,sex,city,province,country,language,headimgurl,subscribe_time,[unionid]}
     * 注意：unionid字段 只有在用户将公众号绑定到微信开放平台账号后，才会出现。建议调用前用isset()检测一下
     */
    public function requestUserInfo ($openid) {

        $params = array (
            'access_token'  => $this->requestAccessToken(),
            'openid'        => $openid
        );
        // https://api.weixin.qq.com/cgi-bin/user/info?access_token=21321254&openid=$openid

        $url = self::API_URL_PREFIX . self::USER_INFO_URL;

        $result = self::http($url, $params, '', 'POST');

        if ($result) {

            $json = json_decode($result, true);

            if (isset($json['errcode'])) {

                throw new \Exception($json['errmsg']);

            }

            return $json;
        }

        return false;

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
                    S('MyWechatApi_Access_Token' . $this->AppId, $result['access_token'], $expire);

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
     * 发送客服消息
     * @param array $data 消息结构{"touser":"OPENID","msgtype":"news","news":{...}}
     * @return boolean|array
     */
    public function sendCustomMessage ($data) {

        // https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN
        $url = self::API_URL_PREFIX . self::CUSTOM_SEND_URL;

        $params = array (
            'access_token'  => $this->requestAccessToken()
        );

        $result = self::http($url, $params, self::json_encode($data), 'POST');

        if ($result) {

            $json = json_decode($result, true);

            if (!$json || !empty($json['errcode'])) {

                throw new \Exception($json['errcode'] . $json['errmsg']);

            }

            return true;

        }

        return false;

    }

    /**
     * 回复文本消息
     * @param  string $text   回复的文字
     */
    public function replyText ($text) {

        return $this->response($text, self::MSG_TYPE_TEXT);

    }

    /**
     * 回复图文消息，一个参数代表一条信息
     * @param  array  $news   图文内容 [标题，描述，URL，缩略图]
     * @param  array  $news1  图文内容 [标题，描述，URL，缩略图]
     * @param  array  $news2  图文内容 [标题，描述，URL，缩略图]
     *                ...     ...
     * @param  array  $news9  图文内容 [标题，描述，URL，缩略图]
     */
    public function replyNews ($news, $news1, $news2, $news3) {
        return $this->response(func_get_args(), self::MSG_TYPE_NEWS);
    }

    /**
     * 构造图文信息
     * @param  array $news 要回复的图文内容
     * [    
     *      0 => 第一条图文信息[标题，说明，图片链接，全文连接]，
     *      1 => 第二条图文信息[标题，说明，图片链接，全文连接]，
     *      2 => 第三条图文信息[标题，说明，图片链接，全文连接]， 
     * ]
     */
    private static function news ($news) {
        $articles = array();
        foreach ($news as $key => $value) {
            list(
                $articles[$key]['Title'],
                $articles[$key]['Description'],
                $articles[$key]['Url'],
                $articles[$key]['PicUrl']
            ) = $value;

            if($key >= 9) break; //最多只允许10条图文信息
        }
        $data['ArticleCount'] = count($articles);
        $data['Articles']     = $articles;

        return $data;
    }

    /**
     * * 响应微信发送的信息（自动回复）
     * @param  array  $content 回复信息，文本信息为string类型
     * @param  string $type    消息类型
     */
    public function response ($content, $type = self::MSG_TYPE_TEXT) {

        // 基础数据
        $data = array(
            'ToUserName'   => $this->data['FromUserName'],
            'FromUserName' => $this->data['ToUserName'],
            'CreateTime'   => NOW_TIME,
            'MsgType'      => $type,
        );

        // 按类型添加额外数据
        $content = call_user_func(array(self, $type), $content);

        if ($type == self::MSG_TYPE_TEXT || $type == self::MSG_TYPE_NEWS) {
            $data = array_merge($data, $content);
        } else {
            $data[ucfirst($type)] = $content;
        }

        // 安全模式，加密消息内容
        if (self::$msgSafeMode) {
            $data = self::generate($data);
        }
        // 转换数据为XML
        $xml = new \SimpleXMLElement('<xml></xml>');
        self::data2xml($xml, $data);
        // file_put_contents('./WechatLog/wechatdata.json', json_encode($data));
        exit($xml->asXML());

    }

    /**
     * 构造文本信息
     * @param  string $content 要回复的文本
     */
    private static function text ($content) {
        $data['Content'] = $content;
        return $data;
    }

    /**
     * 获取临时素材(认证后的订阅号可用)
     * @param string $media_id 媒体文件id
     * @param boolean $is_video 是否为视频文件，默认为否
     * @return raw data
     */
    public function requestMedia ($media_id, $is_video = false) {

        // 如果要获取的素材是视频文件时，不能使用https协议，必须更换成http协议
        $url_prefix = $is_video ? str_replace('https', 'http', self::API_URL_PREFIX) : self::API_URL_PREFIX;

        $url = $url_prefix . self::MEDIA_GET_URL;

        $params = array (
            'access_token'  => $this->requestAccessToken(),
            'media_id'      => $media_id
        );

        $result = self::http($url, $params);

        if ($result) {

            if (is_string($result)) {

                $json = json_decode($result, true);

                if (isset($json['errcode'])) {

                    S('MyWechatApi_Access_Token' . self::$appId, null);
                    throw new \Exception($json['errcode'] . $json['errmsg']);

                }

            }

            return $result;

        }

        return false;

    }

    /**
     * 创建菜单(认证后的订阅号可用)
     * @param array $data 菜单数组数据
     * example:
     *  array (
     *      'button' => array (
     *        0 => array (
     *          'name' => '扫码',
     *          'sub_button' => array (
     *              0 => array (
     *                'type' => 'scancode_waitmsg',
     *                'name' => '扫码带提示',
     *                'key' => 'rselfmenu_0_0',
     *              ),
     *              1 => array (
     *                'type' => 'scancode_push',
     *                'name' => '扫码推事件',
     *                'key' => 'rselfmenu_0_1',
     *              ),
     *          ),
     *        ),
     *        1 => array (
     *          'name' => '发图',
     *          'sub_button' => array (
     *              0 => array (
     *                'type' => 'pic_sysphoto',
     *                'name' => '系统拍照发图',
     *                'key' => 'rselfmenu_1_0',
     *              ),
     *              1 => array (
     *                'type' => 'pic_photo_or_album',
     *                'name' => '拍照或者相册发图',
     *                'key' => 'rselfmenu_1_1',
     *              )
     *          ),
     *        ),
     *        2 => array (
     *          'type' => 'location_select',
     *          'name' => '发送位置',
     *          'key' => 'rselfmenu_2_0'
     *        ),
     *      ),
     *  )
     * type可以选择为以下几种，其中5-8除了收到菜单事件以外，还会单独收到对应类型的信息。
     * 1、click：点击推事件
     * 2、view：跳转URL
     * 3、scancode_push：扫码推事件
     * 4、scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框
     * 5、pic_sysphoto：弹出系统拍照发图
     * 6、pic_photo_or_album：弹出拍照或者相册发图
     * 7、pic_weixin：弹出微信相册发图器
     * 8、location_select：弹出地理位置选择器
     */
    public function createMenu ($data) {

        $params = array (
            'access_token'  => $this->requestAccessToken()
        );

        // https://api.weixin.qq.com/cgi-bin/menu/create?access_token=123
        $url = self::API_URL_PREFIX . self::MENU_CREATE_URL;

        $result = self::http($url, $params, self::json_encode($data), 'POST');

        if ($result) {

            $json = json_decode($result, true);

            if (!$json || !empty($json['errcode'])) {

                throw new \Exception($json['errcode'] . $json['errmsg']);

            }

            return true;

        }

        return false;

    }

    /**
     * 验证并解密密文数据
     * @param  string $encrypt 密文
     * @return array           解密后的数据
     */
    private static function extract ($encrypt) {

        // 验证数据签名
        $signature = self::sign($_GET['timestamp'], $_GET['nonce'], $encrypt);

        if ($signature != $_GET['msg_signature']) {
            throw new \Exception('数据签名错误！');
        }

        // 消息解密对象
        $WechatCrypt = new WechatCrypt(self::$encodingAESKey, self::$appId);

        // 解密得到回明文消息
        $decrypt = $WechatCrypt->decrypt($encrypt);
        
        // 返回解密的数据
        return self::xml2data($decrypt);

    }

    /**
     * 加密并生成密文消息数据
     * @param  array $data 获取到的加密的消息数据
     * @return array       生成的加密消息结构
     */
    private static function generate ($data) {

        /* 转换数据为XML */
        $xml = new \SimpleXMLElement('<xml></xml>');
        self::data2xml($xml, $data);
        $xml = $xml->asXML();

        //消息加密对象
        $WechatCrypt = new WechatCrypt(self::$encodingAESKey, self::$appId);

        //加密得到密文消息
        $encrypt = $WechatCrypt->encrypt($xml);

        //签名
        $nonce     = mt_rand(0, 9999999999);
        $signature = self::sign(NOW_TIME, $nonce, $encrypt);

        /* 加密消息基础数据 */
        $data = array(
            'Encrypt'      => $encrypt,
            'MsgSignature' => $signature,
            'TimeStamp'    => NOW_TIME,
            'Nonce'        => $nonce,
        );

        return $data;

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
     * 数据XML编码
     * @param  object $xml  XML对象
     * @param  mixed  $data 数据
     * @param  string $item 数字索引时的节点名称
     * @return string
     */
    protected static function data2xml ($xml, $data, $item = 'item') {

        foreach ($data as $key => $value) {

            /* 指定默认的数字key */
            is_numeric($key) && $key = $item;

            /* 添加子元素 */
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                self::data2xml($child, $value, $item);
            } else {
                if (is_numeric($value)) {
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node  = dom_import_simplexml($child);
                    $cdata = $node->ownerDocument->createCDATASection($value);
                    $node->appendChild($cdata);
                }
            }
        }

    }

    /**
     * XML数据解码
     * @param  string $xml 原始XML字符串
     * @return array       解码后的数组
     */
    protected static function xml2data ($xml) {

        $xml = new \SimpleXMLElement($xml);
        
        if(!$xml){
            throw new \Exception('非法XXML');
        }

        $data = array();
        foreach ($xml as $key => $value) {
            $data[$key] = strval($value);
        }

        return $data;

    }

    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     */
    protected static function json_encode ($arr) {

        if (count($arr) == 0) return '[]';

        $parts = array();

        $is_list = false;

        $keys = array_keys($arr);

        $max_length = count ($arr) - 1;

        if (($keys [0] === 0) && ($keys[$max_length] === $max_length)) {

            $is_list = true;

            for($i = 0; $i < count ( $keys ); $i ++) {
                if ($i != $keys[$i]) {
                    $is_list = false;
                    break;
                }
            }

        }
        foreach ($arr as $key => $value) {

            if (is_array ($value)) {

                if ($is_list) {
                    $parts[] = self::json_encode($value);
                } else {
                    $parts[] = '"' . $key . '":' . self::json_encode($value);
                }

            } else {

                $str = '';

                if (!$is_list) {
                    $str = '"' . $key . '":';
                }

                if (!is_string($value) && is_numeric($value) && $value < 2000000000) {
                    $str .= $value;
                } elseif ($value === false) {
                    $str .= 'false'; //The booleans
                } elseif ($value === true) {
                    $str .= 'true';
                } else {
                    $str .= '"' . addslashes ($value) . '"';
                }

                $parts [] = $str;

            }

        }

        $json = implode (',', $parts);

        if ($is_list) {
            return '[' . $json . ']';
        }

        return '{' . $json . '}';

    }

}