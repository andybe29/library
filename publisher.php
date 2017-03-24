<?php
    $title = ['Сайт - онлайн библиотека'];

    $page = [];

    if (isset($_GET['id']) and ($page['id'] = (int)$_GET['id']) > 0) {
        # создание объекта для работы с БД
        require_once 'db.php';
        require_once 'simpleMySQLi.class.php';
        $sql = new simpleMySQLi($db, pathinfo(__FILE__, PATHINFO_DIRNAME));

        # поиск издательства по id
        $sql->str = 'select * from libPublishers where id=' . $page['id'];
        $sql->execute();

        $r = $sql->rows ? $sql->assoc() : false;
        $sql->free();

        if ($r) {
            $page = $r;
            $title[] = $page['name'];
        } else {
            $page['err'] = 'издательство не найдено';
        }
    } else {
        $page['err'] = 'не указан идентификатор издательства либо он указан неверно';
    }
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo implode(' :: ', array_reverse($title)) ?></title>
        <link rel="stylesheet" href="spectre.min.css">
    </head>
    <body>
        <div class="container" style="width: 128rem">
<?php
    if (isset($page['err'])) {
?>
            <div class="columns">
                <div class="column col-12">
                    <h4 class="text-bold">Сайт - онлайн библиотека</h4>
                </div>
            </div>
            <div class="columns">
                <div class="toast toast-danger"><?php echo $page['err'] ?></div>
            </div>
<?php
    } else {
?>
            <div class="columns">
                <div class="column col-12">
                    <h4 class="text-bold"><?php echo $page['name'] ?></h4>
                    <p><b>год основания:</b> <?php echo $page['founded'] ? $page['founded'] : '' ?></p>
                    <p><b>адрес:</b> <?php echo $page['address'] ?></p>
                </div>
            </div>
<?php
    }
?>
        </div>
    </body>
</html>