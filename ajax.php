<?php
    # бэкенд
    if (array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    } else die();

    if (ob_get_length()) ob_clean();

    $ret = ['ok' => false, 'err' => 'system error'];

    $func = function($val) {
        return (is_scalar($val)) ? trim(strip_tags($val)) : $val;
    };
    $post = array_map(function($val) { return trim(strip_tags($val)); }, $_POST);

    # проверка входных данных
    $do = true;
    foreach (['book', 'publisher', 'writer'] as $key) {
        # значение поиска
        $do = isset($post[$key]) ? $do : false;
    }
    # если проверка не пройдена
    if (!$do or (empty($post['book']) and empty($post['publisher']) and empty($post['writer']))) goto foo;

    # создание объекта для работы с БД
    require_once 'db.php';
    require_once 'simpleMySQLi.class.php';
    $sql = new simpleMySQLi($db, pathinfo(__FILE__, PATHINFO_DIRNAME));

    # поиск по $post[$key] в таблицах
    $w = [];
    if ($post['book']) {
        $w[] = 'b.name like ' . $sql->varchar('%' . $post['book'] . '%');
    }
    if ($post['publisher']) {
        $w[] = 'p.name like ' . $sql->varchar('%' . $post['publisher'] . '%');
    }
    if ($post['writer']) {
        $w[] = 'w.name like ' . $sql->varchar('%' . $post['writer'] . '%');
    }

    $sql->str = [];
    $sql->str[] = 'select b.name, w.id as wid, w.name as writer, p.id as pid, p.name as publisher, b.year, b.file';
    $sql->str[] = 'from libBooks b left join libWriters w on w.id=b.writer';
    $sql->str[] = 'left join libPublishers p on p.id=b.publisher';
    $sql->str[] = 'where ' . $sql->_and($w);

    $sql->execute();

    $u = $sql->err ? false : $sql->all();
    $sql->free();

    if ($u === false) {
        $ret['err'] = 'ошибка выполнения запроса';
    } else if (empty($u)) {
        $ret['err'] = 'ничего не найдено';
    } else {
        # массив найденных книг
        $ret['books'] = $u;
        $ret['ok'] = true;
    }

    foo:
    if ($ret['ok']) unset($ret['err']);

    header('Content-Type: application/json');
    echo json_encode($ret);