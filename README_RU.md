# Yametrika
Скрипт вывода основных данных статистики в текстовом формате.
## Функционал:
1. Берёт api токен, смотрит список счётчиков, добавляет их id и названия в массив;
2. Проходит по циклу массива в другой функции и вызывает информацию о посещаемости;
3. Формирует новый массив, в котором добавляется название, количество посетителей и просмотров за вчера;
4. В index.php вызывается функция, формирующая массив из П.3;
5. Выводится заголовок h1 и таблица со столбцами: сайт (это же и название), посетителей, просмотров.

никакого дизайна нет.

## Перед работой со скриптом:
1. Начинаем регистрацию нового приложения на странице https://oauth.yandex.ru/client/new
2. В «Название» пишем любое удобное для вас название, в раскрывающемся списке «Яндекс.Метрики» ставим галочку на «Получение статистики, чтение параметров своих и доверенных счётчиков». Отмечаем 
Отмечаем "Веб-сервисы" и кликаем по "Подставить URL для разработки".
3. После нажатия по кнопке "Создать приложение" получаем для дальнейших действий «Id приложения» и «Пароль приложения» (нам нужен только id)
4. Введите получившийся код в адрес, заменив "***", и нажмите "разрешить" на появившейся странице:
https://oauth.yandex.ru/authorize?response_type=token&client_id=***

## Установка скрипта
Нужно, чтобы у вас на сервере/хостинге был php.
- Либо перейдите в папку, куда планируете закидывать скрипт, через консоль и введите команду
git clone https://github.com/denis-skripnik/yametrika
либо [скачайте архив](https://github.com/denis-skripnik/yametrika/archive/master.zip) и распакуйте его;
- Перейдите в папку yametrika;
- Откройте functions.php и измените значение переменной $authToken из первой функции: сюда надо вставить вместо "***" ваш токен, полученный после разрешения доступа.
- Сохраняем и открываем страницу сайт.ру/yametrika-master (или без папки, если копировали в корень).
## Для разработчиков
1. Используется функция, позволяющая передать в заголовках http api токен и другую информацию:
```
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
```
2. Вторая получает список счётчиков и возвращает название и id каждого:
```
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
```
3. Третья проходит по циклу со списком id счётчиков и добавляет в массив информацию для вывода на странице:
```
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
```
4. В index.php производится вывод:
```
require_once 'functions.php';
$data = my_counters();
echo '<h1>Статистика за вчерашний день по всем сайтам аккаунта</h1>
<table><thead><tr><th>Сайт</th><th>Посетителей</th><th>Просмотров</th></tr></thead>';
foreach ($data as $yametrika) {
echo '<tr><td><a href="http://'.$yametrika['site'].'" target="_blank">'.$yametrika['site'].'</a><td>'.$yametrika['result'][0].'</td><td>'.$yametrika['result'][1].'</td></tr>';
}
echo '</table>';
```

## Всё

Буду рад, если кто-то из разработчиков захочет развить проект, создав пулл-реквесты. я реализовал то, что мне нужнее всего, но возможно будут идеи, как сделать ещё лучше, не теряя основы, которая заключается в том, что всё отображается текстами.