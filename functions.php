<?php
function curl_file_get_contents($url)
{
$authToken = '***';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-yametrika+json', 'Authorization: OAuth' . $authToken]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$obj = curl_exec($ch);
curl_close($ch);
return $obj;
}
function counters_list() {
$url = 'https://api-metrika.yandex.net/management/v1/counters';
$params = array(
'sort' => 'Hits',
'status' => 'Active'
);
$obj = curl_file_get_contents($url . '?' . http_build_query($params));
$obj = json_decode($obj, true);
$counters = $obj['counters'];
$list = [];
foreach ($counters as $num => $counter) {
$list[$num] = ['id' => $counter['id'], 'site' => $counter['name']];
}
return $list;
}

function my_counters() {
    $list_counters = counters_list();
$my_counters = [];
    foreach ($list_counters as $num => $counter) {
        $url = 'https://api-metrika.yandex.ru/stat/v1/data';
$params = array(
'ids' => $counter['id'],
'metrics' => 'ym:pv:users,ym:pv:pageviews',
'code_status' => 'CS_OK',
'date1' => 'yesterday',
'date2' => 'yesterday'
);
$obj = curl_file_get_contents($url . '?' . http_build_query($params));
$obj = json_decode($obj, true);
$my_counters[$num] = ['site' => $counter['site'], 'result' => $obj['data'][0]['metrics']];
}
return $my_counters;
}
?>