<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$cacheFile = 'cache.json';
$cacheTime = 10; // 缓存时间，单位：秒

function getItunesAlbumCover($songName, $artistName) {
    $searchUrl = 'https://itunes.apple.com/search';
    $params = [
        'term' => $songName . ' ' . $artistName,
        'media' => 'music',
        'limit' => 1
    ];

    $query = http_build_query($params);
    $url = $searchUrl . '?' . $query;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $results = json_decode($response, true);

    if ($results['resultCount'] > 0) {
        $track = $results['results'][0];
        $albumCoverUrl = $track['artworkUrl100']; // 100x100像素的专辑封面
        // 换成 500x500 像素的封面
        $albumCoverUrl = str_replace('100x100bb.jpg', '500x500bb.jpg', $albumCoverUrl);
        return $albumCoverUrl;
    } else {
        return '';
    }
}

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
    $first = $json['recenttracks']['track'][0];

    $songName = $first['name'];
    $artistName = $first['artist']['#text'];

    $albumCoverUrl = getItunesAlbumCover($songName, $artistName);
    $first['albumCover'] = $albumCoverUrl;

    $content = json_encode($first);

    // 更新缓存文件
    file_put_contents($cacheFile, $content);
}

echo $content;
