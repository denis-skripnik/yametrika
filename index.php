<?php
require_once 'functions.php';
$data = my_counters();
echo '<h1>Статистика за вчерашний день по всем сайтам аккаунта</h1>
<table><thead><tr><th>Сайт</th><th>Посетителей</th><th>Просмотров</th></tr></thead>';
foreach ($data as $yametrika) {
echo '<tr><td><a href="http://'.$yametrika['site'].'" target="_blank">'.$yametrika['site'].'</a><td>'.$yametrika['result'][0].'</td><td>'.$yametrika['result'][1].'</td></tr>';
}
echo '</table>';
?>