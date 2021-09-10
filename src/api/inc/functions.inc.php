<?php
/*********************************
 *                               *
 *           FUNCTIONS           *
 *                               *
 *********************************/

  /**
   * Convert an epoch time to a DateTime
   * @param $time epoch time to convert
   * @param $addHour add one or multiple hours to whe final time (default to false)
   * @param $hoursToAdd number of hours to add (default to 1)
   * @return the converted value
   */
  function convertEpoch($time, $addHour = false, $hoursToAdd = 1) {
    $formatedTime = new DateTime("@$time");

    // Add one hour to correct time timezone problems
    if ($addHour)
      date_add($formatedTime, date_interval_create_from_date_string("$hoursToAdd hour"));

    return $formatedTime->format('Y-m-d H:i:s');
  }

  /**
   * Check data validity and convert empty data to JSON
   * @param $data the data to check
   * @param $method the method used by the API request
   */
  function validateData(&$data, $method) {
    $error = ($method == 'get') ? (404) : (400);
    $ok = ($method == 'get') ? (200) : (201);

    if (empty($data)) {
      http_response_code($error);
      $data = "{}";
    } else {
      http_response_code($ok);
    }
  }

  /**
   * Convert the values given by the Sigfox sensor to human-readable data
   * @param $data the values sent by the sensor
   * @return a json array containing the converted data
   */
  function convertSensorData($data) {
    $temperature = substr($data, 0, 2);
    $idleVoltage = substr($data, 2, 2);
    $state = substr($data, 4, 2);
    $transmissionVoltage = substr($data, 6, 2);
    $firmwareVersion = substr($data, 8, 2);
    $magnetometerX = substr($data, 10, 4);
    $magnetometerY = substr($data, 14, 4);
    $magnetometerZ = substr($data, 18, 4);
    $messageType = substr($data, 22, 2);

    $arr = array("temperature" => hexdec($temperature),
                 "idleVoltage" => (hexdec($idleVoltage) / 10),
                 "state" => hexdec($state),
                 "transmissionVoltage" => (hexdec($transmissionVoltage) / 10),
                 "firmwareVersion" => (hexdec($firmwareVersion) / 10),
                 "magnetometerX" => hexdec($magnetometerX),
                 "magnetometerY" => hexdec($magnetometerY),
                 "magnetometerZ" => hexdec($magnetometerZ),
                 "messageType" => hexdec($messageType));
    $json = json_encode($arr);
    return $json;
  }