<?php
/*********************************
 *                               *
 *           MAIN FILE           *
 *                               *
 *********************************/

require_once 'router.php';
require_once "./inc/util.inc.php";

// Get the URL to the working directory
$sub_dir = dirname($_SERVER['PHP_SELF']);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

/**** GET ****/
/**
 * Return all the parkings
 */
route('get', $sub_dir . '/parkings', function ($matches, $rxd) {
    $data = getAllParkings();
    
    http_response_code(200);
    echo json_encode($data);
    exit();
});

/**
 * Return a specific parking
 */
route('get', $sub_dir . '/parkings/([0-9]+)', function ($matches, $rxd) {
    $id = $matches[1][0];
    $data = getParkingById($id);
    
    validateData($data, 'get');

    echo json_encode($data);
    exit();
});

/**
 * Return all the sensors in a parking
 */
route('get', $sub_dir . '/parkings/([0-9]+)/sensors', function ($matches, $rxd) {
    $id = $matches[1][0];
    $data = getSensorsByParkingsId($id);
    
    validateData($data, 'get');

    echo json_encode($data);
    exit();
});

/**
 * Return a specific sensor in a parking
 */
route('get', $sub_dir . '/parkings/([0-9]+)/sensors/([0-9]+)', function ($matches, $rxd) {
    $id = $matches[1][0];
    $sensorId = $matches[2][0];
    $data = getSensorsByIdAndParkingsId($id, $sensorId);
    
    validateData($data, 'get');

    echo json_encode($data);
    exit();
});

/**
 * Return the results for a sensor
 */
route('get', $sub_dir . '/sensors/([0-9]+)/results', function ($matches, $rxd) {
    $sensorId = $matches[1][0];

    $data = getResults($sensorId);
    
    validateData($data, 'get');
    
    echo json_encode($data);
    exit();
});

/**
 * Return a result from a sensor
 */
route('get', $sub_dir . '/sensors/([0-9]+)/results/([0-9]+)', function ($matches, $rxd) {
    $sensorId = $matches[1][0];
    $resultId = $matches[2][0];

    $data = getResultsById($sensorId, $resultId);
    
    validateData($data, 'get');
    
    echo json_encode($data);
    exit();
});

/**** POST ****/
/**
 * Add a parking
 */
route('post', $sub_dir . '/parkings', function ($matches, $rxd) {
    $json = file_get_contents('php://input');
    $postData = json_decode($json, true);
    
    $data = addParking($postData['name'], $postData['address'], $postData['city'], $postData['country']);
    
    validateData($data, 'post');
    
    echo json_encode($data);
    exit();
});

/**
 * Add a sensor in a parking
 */
route('post', $sub_dir . '/parkings/([0-9]+)/sensors', function ($matches, $rxd) {
    $parkingId = $matches[1][0];
    $json = file_get_contents('php://input');
    $postData = json_decode($json, true);

    $parkingData = getParkingById($parkingId);
    
    if (!empty($parkingData)) {
        $data = addSensor($postData['deviceId'], $parkingId);
        
        validateData($data, 'post');
    }
    
    echo json_encode($data);
    exit();
});

/**
 * Add a result from a sensor
 */
route('post', $sub_dir . '/parkings/([0-9]+)/sensors/results', function ($matches, $rxd) {
    $parkingId = $matches[1][0];
    $json = file_get_contents('php://input');
    $postData = json_decode($json, true);

    $parkingData = getParkingById($parkingId);
    
    if (!empty($postData) && !empty($parkingData)) {
        $data = addResult($postData['data'], $postData['device'], $postData['timestamp'], $postData['seqNumber'], $parkingId);
        
        validateData($data, 'post');
    }
    
    echo json_encode($data);
    exit();
});

// If the URL isn't correct
$data = [
    "error" => "Unknown route"
];

http_response_code(400);
echo json_encode($data, JSON_FORCE_OBJECT);
