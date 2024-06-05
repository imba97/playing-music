<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$cacheFile = 'cache.json';
$cacheTime = 10; // 缓存时间，单位：秒

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    // 从缓存文件读取数据
    $content = file_get_contents($cacheFile);
} else {
    // 从API获取数据
    // 接口文档：https://www.last.fm/api/show/user.getRecentTracks
    $url = "https://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user=rj&api_key=YOUR_API_KEY&format=json";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    
    $json = json_decode($output, true);
    $content = json_encode($json['recenttracks']['track'][0]);

    // 更新缓存文件
    file_put_contents($cacheFile, $content);
}

echo $content;

