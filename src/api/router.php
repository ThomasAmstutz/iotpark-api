<?php

/**
 * @param $method method used by the request
 * @param $regex route model to evaluate
 * @param $cb callback function
 * @return int
 */
function route ($method, $regex, $cb) {

    if(strtoupper($method) !== $_SERVER['REQUEST_METHOD'])
        return 0;

    $recieved_datas = [];

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'PUT':
            parse_str(file_get_contents("php://input"),$recieved_datas);
            break;

        case 'POST':
            $recieved_datas = $_POST;
            break;
    }

    $regex = str_replace('/', '\/', $regex);

    $is_match = preg_match('/^' . ($regex) . '$/', $_SERVER['REQUEST_URI'], $matches, PREG_OFFSET_CAPTURE);

    if ($is_match)
        $cb($matches, $recieved_datas);
}

