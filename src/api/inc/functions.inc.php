<?php

/*********************************
 *                               *
 *           API KEYS            *
 *                               *
 *********************************/

  /**
   * Verify if a key has been sent
   * @param $headers headers sent by the client
   * @return true or false depending on the presence of an api key
   */
  function checkForKey($headers) {
    return array_key_exists('api-key', $headers) ? (true) : false;
  }

  /**
   * Check if a key is the API Keys file
   * @param $key Key to check
   * @return true if the key is valid or false if it's not
   */
  function isValidKey($key) {
    $file = file_get_contents(API_KEYS);
    $json = json_decode($file, true);
    foreach ($json as $item) {
      if ($item["key"] == $key)
        return true;
    }

    return false;
  }

  /**
   * Check if a key can read a value
   * @param $key Key to check
   * @return true if the key can read the database
   */
  function canRead($key) {
    $file = file_get_contents(API_KEYS);
    $json = json_decode($file, true);
    foreach ($json as $item) {
      if ($item["key"] == $key)
        return $item['can_read'];
    }
  }

  /**
   * Check if a key can add a value
   * @param $key Key to check
   * @return true if the key can add in the database
   */
  function canAdd($key) {
    $file = file_get_contents(API_KEYS);
    $json = json_decode($file, true);
    foreach ($json as $item) {
      if ($item["key"] == $key)
        return $item['can_add'];
    }
  }

  /**
   * Check if a key can update a value
   * @param $key Key to check
   * @return true if the key can update in the database
   */
  function canUpdate($key) {
    $file = file_get_contents(API_KEYS);
    $json = json_decode($file, true);
    foreach ($json as $item) {
      if ($item["key"] == $key)
        return $item['can_update'];
    }
  }

  /**
   * Check if a key can delete a value
   * @param $key Key to check
   * @return true if the key can delete in the database
   */
  function canDelete($key) {
    $file = file_get_contents(API_KEYS);
    $json = json_decode($file, true);
    foreach ($json as $item) {
      if ($item["key"] == $key)
        return $item['can_delete'];
    }
  }

  /**
   * @return all the API keys
   */
  function getAllKeys() {
    $file = file_get_contents(API_KEYS);
    return $file;
  }

  /**
   * Check if a key can be used
   * @param $method the method used by the request who sent the key
   * @return an error message if the key can't be used. Returns "true" when the key can be used
   */
  function processKey($method) {
    $requestHeaders = getallheaders();
    if (checkForKey($requestHeaders)) {
      $apiKey = $requestHeaders['api-key'];

      if (isValidKey($apiKey)) {
        $canDoAction = ($method == 'get') ? canRead($apiKey) : canAdd($apiKey);

        if ($canDoAction) {
          return true;
        } else {
          $data = "The provided key can't do this action";
          http_response_code(400);
        }
      } else {
          $data = "The provided API Key can't be used for this request";
          http_response_code(400);
      }
    } else {
        $data = "No API Key was provided in the header";
        http_response_code(400);
    }

    return $data;
  }

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

    if (http_response_code() != 400) {
      if (empty($data)) {
        http_response_code($error);
        $data = "{}";
      } else {
        http_response_code($ok);
      }
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