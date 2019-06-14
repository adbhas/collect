<?php

$action = @$_GET["action"];
__checkEmpty($action,"缺少参数action");
if ($action == 'getjs') {
	$uniq_id = @$_GET["uniq_id"];//抖音号

	__checkEmpty($uniq_id,"缺少参数uniq_id");

	$userData = getUserId($uniq_id);
	getjs($userData["data"]["uid"]);
}else if($action == 'getlist'){
	set_time_limit(0);
	$uid = @$_GET["uid"];//抖音号
	$dytk = @$_GET["dytk"];//抖音号
	$signature = @$_GET["signature"];//抖音号
	__checkEmpty($uid,"缺少参数uid");
	__checkEmpty($dytk,"缺少参数dytk");
	__checkEmpty($signature,"缺少参数signature");
	$data = getShareData($uid,$signature,$dytk);
	exit(json_encode($data));
}else if($action == 'getzUrl'){
	$url = @$_GET["url"];
	__checkEmpty($url,"缺少参数url");
	$data = ["desc"=>@$_GET["desc"],"img"=>@$_GET["img"]];
	$data["url"] = getzUrl($url);

	exit(json_encode($data));
}



function getjs($uid=''){
	__checkEmpty($uid,"缺少参数");
	$data= get_curl('https://www.douyin.com/share/user/'.$uid);
	$js = file_get_contents("./douyin_fuck.js");
	$tac = getSubstr($data,"tac='","'</sc");
	$data = str_replace(array("\n","\r\n","  "),'',$data);
	$dytk = getSubstr($data,"dytk: '","'}");
	$file = str_replace('{$UA}',$_SERVER['HTTP_USER_AGENT'],file_get_contents('douyin_fuck.js'));
	$file = str_replace('{$uid}',$uid,$file);
	$file = str_replace('{$tac}',$tac,$file);
	$file = str_replace('{$dytk}',$dytk,$file);

	exit($file);
}

//替换
function getSubstr($str, $leftStr, $rightStr){
	$left = strpos($str, $leftStr);
	$right = strpos($str, $rightStr,$left);
	if($left < 0 or $right < $left) return '';
	return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
}


/**
 * 获取用户信息
 * @param int $uniq_id 抖音号
 * @return int
 */
function getUserId($uniq_id=''){
	__checkEmpty($uniq_id,"抖音号不能为空");
	$url = "https://www.douyin.com/aweme/v1/wallet/userinfo/?uniq_id={$uniq_id}&open_id=1&callback=__jp2";
	$data = json_decode(substr(get_curl($url), 6,-1),true);
	__checkEmpty($data["data"]["uid"],"解析用户id错误");

	return $data;
}

/**
 * 获取用户 作品、喜欢
 * @param int $uid 用户id
 * @param string $signature 类似token
 * @param string $dytk 验证用
 * @param int $t 获取类型(1:作品,2:喜欢)
 * @return int
 */
function getShareData($uid='',$signature='',$dytk='',$max_cursor=0,$t=1){
	__checkEmpty($uid,"用户id为空");
	__checkEmpty($signature,"signature为空");
	__checkEmpty($dytk,"dytk为空");
	if ($t==1) $url = 'https://www.douyin.com/aweme/v1/aweme/post/?user_id='.$uid.'&count=80&max_cursor='.$max_cursor.'&aid=1128&_signature='.$signature.'&dytk='.$dytk;//作品
	else 'https://www.douyin.com/web/api/v2/aweme/like/?user_id='.$uid.'&count=80&max_cursor='.$max_cursor.'&aid=1128&_signature='.$signature.'&dytk='.$dytk;//喜欢
	$shareData = json_decode(get_curl($url),true);
	__checkEmpty($shareData["aweme_list"],"解析数据错误");
	
	return $shareData;
}

/**
 * 解析真实url
 * @param string $play_addr 视频id
 * @return int
 */
function getzUrl($play_addr=''){
	__checkEmpty($play_addr,"用户id为空");
	$url = "https://aweme.snssdk.com/aweme/v1/play/?video_id=".$play_addr."&line=0";
	$zUrl = get_curl($url,1,0);
	__checkEmpty($zUrl,"解析数据错误");
	return $zUrl;
}


/**
 * 获取用户信息
 * @param string $url 采集url
 * @param int $timeout 请求时间
 * @param int $t 获取类型(1:返回数据,0:302跳转地址)
 * @return mixed
 */
function get_curl($url,$timeout=5,$t=1){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$httpheader[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8";
	if (!$t) $httpheader[] = "Accept-Encoding: gzip, deflate, br";
	$httpheader[] = "Accept-Language: zh-CN,zh;q=0.9";
	$httpheader[] = "pragma: no-cache";
	$httpheader[] = "upgrade-insecure-requests: 1";
	$httpheader[] = "content_type: text/html; charset=UTF-8";
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; U; Android 5.1.1; zh-cn; MI 4S Build/LMY47V) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/53.0.2785.146 Mobile Safari/537.36 XiaoMi/MiuiBrowser/9.1.3');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,2);//302跳转
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	
	if ($t)curl_setopt($ch, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
		
	$ret = curl_exec($ch);
	if (!$t) $ret = curl_getinfo($ch)["url"];

	curl_close($ch);
	return $ret;
}


/**
 * 数据为空
 * @param array $data
 * @param string $message
 * @return mixed
 */
function __checkEmpty($data,$message)
{
	if (empty($data)) {
		exit($message);
	}
}