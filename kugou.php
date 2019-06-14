<?php 
/**
 * 获取列表
 * @param  string  $keyword  关键字
 * @param  integer $page     当前页
 * @param  integer $pagesize 每页获取数量
 * @return array         
 */
function getList($keyword, $page = 1, $pagesize = 10){
    $url = "https://songsearch.kugou.com/song_search_v2?&keyword={$keyword}&page={$page}&pagesize={$pagesize}";
    $resp = json_decode(file_get_contents($url), true);
    return $resp;
}



/**
 * 获取音乐地址
 * @param  string $hash 音乐hash码
 * @return array      
 */
function getMusic($hash){
    $url  = "http://m.kugou.com/app/i/getSongInfo.php?hash={$hash}&cmd=playInfo";
    $resp = json_decode(file_get_contents($url), true);
    return $resp;
}

$data = getList("默")["data"]["lists"];//获取搜索列表
$url = getMusic($data[0]["FileHash"])["url"];
echo $url;