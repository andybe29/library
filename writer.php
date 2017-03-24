<?php
    $title = ['Сайт - онлайн библиотека'];

    $page = [];

    if (isset($_GET['id']) and ($page['id'] = (int)$_GET['id']) > 0) {
        # создание объекта для работы с БД
        require_once 'db.php';
        require_once 'simpleMySQLi.class.php';
        $sql = new simpleMySQLi($db, pathinfo(__FILE__, PATHINFO_DIRNAME));

        # поиск издательства по id
        $sql->str = 'select * from libWriters where id=' . $page['id'];
        $sql->execute();

        $r = $sql->rows ? $sql->assoc() : false;
        $sql->free();

        if ($r) {
            $page = $r;
            $title[] = $page['name'];

            # список книг
            $sql->str   = [];
            $sql->str[] = 'select b.name, p.id as pid, p.name as publisher, b.year, b.file';
            $sql->str[] = 'from libBooks b left join libPublishers p on p.id=b.publisher';
            $sql->str[] = 'where b.writer=' . $page['id'];
            $sql->execute();

            $page['books'] = $sql->rows ? $sql->all() : false;
            $sql->free();
        } else {
            $page['err'] = 'автор не найден';
        }
    } else {
        $page['err'] = 'не указан идентификатор автора либо он указан неверно';
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
                    <p><b>год рождения:</b> <?php echo $page['born'] ? $page['born'] : '' ?></p>
                    <p><b>год смерти:</b> <?php echo $page['died'] ? $page['died'] : '' ?></p>
                    <p><?php echo $page['info'] ?></p>
                </div>
            </div>
<?php
        if ($page['books']) {
?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>название</th>
                        <th>год&nbsp;выхода</th>
                        <th>издательство</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
<?php
            foreach ($page['books'] as $i => $r) {
?>
                    <tr>
                        <td><?php echo ($i + 1) ?></td>
                        <td><?php echo $r['name'] ?></td>
                        <td><?php echo $page['year'] ? $page['year'] : '' ?></td>
                        <td><a href="publisher.php?id=<?php echo $r['pid'] ?>"><?php echo $r['publisher'] ?></a></td>
                        <td><a href="<?php echo $r['file'] ?>">скачать</a></td>
                    </tr>
<?php
            }
?>
                </tbody>
            </table>
<?php
        }
    }
?>
        </div>
    </body>
</html>