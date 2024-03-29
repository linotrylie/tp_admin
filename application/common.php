<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 模拟tab产生空格
 * @param int $step
 * @param string $string
 * @param int $size
 * @return string
 */
function tab($step = 1, $string = ' ', $size = 4)
{
    return str_repeat($string, $size * $step);
}


/**
 * 日志
 */
function _log($file, $txt)
{
    $dir_name = './log/'.date('Ymd');
    $file_name = basename($file);
    if (!is_dir($dir_name)) {
        //第三个参数是“true”表示能创建多级目录，iconv防止中文目录乱码
        mkdir(iconv("UTF-8", "GBK", $dir_name), 0777, true);
    }
    $file = $dir_name . '/' . $file_name;
    $fp = fopen($file, 'ab+');
    fwrite($fp, "# " . date('Y-m-d H:i:s') . ' : ');
    fwrite($fp, $txt);
    fwrite($fp, "\r\n");
    fclose($fp);
}

/**
 * 调接口并记录日志
 */
function get_contents($url, $get_arr=false)
{
    $res = file_get_contents($url);
    $logName = 'get_contents.log';
    _log($logName, $url);
    _log($logName, $res);
    if ($get_arr) {
        return json_decode($res, true);
    } else {
        return $res;
    }
}
function curlPost($url, $array=[], $isHttps = 1)
{
    $post_data = json_encode($array);
    // var_dump($url);var_dump($post_data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_TIMEOUT, 500);
    if ($isHttps == 0) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    $output = curl_exec($ch);
    // var_dump($output);
    curl_close($ch);

    // 日志
    $logName = 'curlPost.log';
    _log($logName, $url);
    _log($logName, $post_data);
    _log($logName, $output);
    $output_array = json_decode($output, true);

    return $output_array;
}
function curlGet($url, $array=[], $isHttps = 1, $return_arr = 1)
{
    if (!empty($array)) {
        $url .= '?';
        foreach ($array as $key => $val) {
            $url .= $key.'='.$val.'&';
        }
        $url = substr($url, 0, -1);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 500);
    if ($isHttps == 0) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    } else if ($isHttps == 2) {
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    }
    $output = curl_exec($ch);
    // var_dump($output);
    curl_close($ch);

    // 日志
    $logName = 'curlGet.log';
    _log($logName, $url);
    _log($logName, $output);
    if ($return_arr) $output = json_decode($output, true);

    return $output;
}

/**
 * 异步请求 v3.0
 */
function doRequest($url, $param=[], $cookie=''){
    // 生成token
    $time = time();
    $salt = md5(uniqid(mt_rand()).'HPFJ');
    $token = md5($time.$salt.'JLHP');
    $param['_time'] = $time;
    $param['_salt'] = $salt;
    $param['_token'] = $token;

    // 处理cookie
    if (is_array($cookie)) {
        $cookieStr = '';
        foreach ($cookie as $key=>$val) {
            $cookieStr .= $key . '=' . $val . '; ';
        }
        $cookie = substr($cookieStr, 0, -2);
    }

    $urlinfo = parse_url($url); 

    $host = $urlinfo['host']; 
    $path = $urlinfo['path']; 
    $query = isset($param)? http_build_query($param) : ''; 
        
    $port = 80; 
    $errno = 0; 
    $errstr = ''; 
    $timeout = 10; 
        
    $fp = fsockopen($host, $port, $errno, $errstr, $timeout); 

    $out = "POST ".$path." HTTP/1.1\r\n"; 
    $out .= "host:".$host."\r\n"; 
    $out .= "content-length:".strlen($query)."\r\n"; 
    $out .= "content-type:application/x-www-form-urlencoded\r\n";
    if ($cookie) $out .= "cookie: {$cookie}\r\n"; 
    $out .= "connection:close\r\n\r\n"; 
    $out .= $query; 
        
    fputs($fp, $out); 
    fclose($fp); 
}

// 截取字符串
function strsub($str, $num=150) {
    $len = mb_strlen(strip_tags($str,'utf-8'));
    if ($len > $num) {
        return mb_strimwidth($str, 0, $num, '...', 'utf-8');
    } else {
        return $str;
    }
}

// 补零或截取字符串
function formatnum($str, $num=8)
{
    $len = strlen($str);
    if ($len >= $num) {
        return substr($str, $len-$num);
    } else {
        return str_pad($str, $num ,"0", STR_PAD_LEFT); 
    }
}

function _ajax_return_success($msg, $data, $code='0')
{
    echo json_encode(['data'=>$data, 'msg'=>$msg, 'code'=>$code]);
    exit;
}
function _ajax_return_error($msg, $data='', $code='1')
{
    echo json_encode(['data'=>$data, 'msg'=>$msg, 'code'=>$code]);
    exit;
}

// v3.0
function get_http_host($end='') {
    return 'http://' . $_SERVER['HTTP_HOST'] . $end;
}

// 过滤掉emoji表情
function filterEmoji($str)
{
    $str = preg_replace_callback(    //执行一个正则表达式搜索并且使用一个回调进行替换
        '/./u',
        function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },
        $str);

    return $str;
}