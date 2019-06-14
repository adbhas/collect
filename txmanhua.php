<?php 
/**
 * 获取分类
 * @return array         
 */
function type(){
    $url = "https://wx.ac.qq.com/1.0.0/Classify/conf";
    $resp = json_decode(file_get_contents($url), true);
    return $resp;
}

/**
 * 获取分类列表
 * @param  integer $id    分类id
 * @param  integer $page  页码
 * @return array         
 */
function typeList($id=1,$page=1){
    $url = "https://wx.ac.qq.com/1.0.0/Classify/list?id={$id}&page={$page}";
    $resp = json_decode(file_get_contents($url), true);
    return $resp;
}

/**
 * 获取漫画章节
 * @param  integer $id  分类id
 * @return array         
 */
function manhuaList($id=1){
    $url = "https://wx.ac.qq.com/1.0.0/Detail/comic?id={$id}";
    $resp = json_decode(file_get_contents($url), true);
    return $resp;
}

/**
 * 获取漫画图片
 * @param  integer $id   漫画id
 * @param  integer $cid   章节id
 * @return array         
 */
function manhuaData($id=1,$cid=1){
    $url = "https://wx.ac.qq.com/1.0.0/View/comic?id={$id}&cid={$cid}&detail=1";
    $resp = json_decode(file_get_contents($url), true);
    return $resp;
}

/**
 * 搜索
 * @param  integer $id   漫画id
 * @param  integer $cid   章节id
 * @return array         
 */
function result($word='',$page=1){
    $url = "https://wx.ac.qq.com/1.0.0/Search/result?word={$word}&page={$page}";
    $resp = json_decode(file_get_contents($url), true);
    return $resp;
}


/*分类查找*/
$type = type();
$typeList = typeList($type["data"][1]["id"],1);
$manhuaList = manhuaList($typeList["data"][0]["comic_id"]);
$manhuaData = manhuaData($manhuaList["data"]["comic_id"],$manhuaList["data"]["catalog"][0]["chapter_id"])["data"]["catalog"];


echo "当前位置：{$type['data'][1]['name']}->{$typeList['data'][0]['title']}->{$manhuaList['data']['catalog'][0]['seq_no']}-{$manhuaList['data']['catalog'][0]['title']}";
echo "<br><br>";
echo "图片地址：".json_encode(array_column($manhuaData, "pic"));


/*搜索查找*/
$resultList = result("一人之下");
$manhuaList = manhuaList($resultList["data"][0]["comic_id"]);
$manhuaData = manhuaData($manhuaList["data"]["comic_id"],$manhuaList["data"]["catalog"][0]["chapter_id"])["data"]["catalog"];

echo "<br><br><br><br>";
echo "当前位置：搜索->{$resultList['data'][0]['title']}->{$manhuaList['data']['catalog'][0]['seq_no']}-{$manhuaList['data']['catalog'][0]['title']}";
echo "<br><br>";
echo "图片地址：".json_encode(array_column($manhuaData, "pic"));
