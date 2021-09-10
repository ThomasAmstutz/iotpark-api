<?php
/*********************************
 *                               *
 *       DATABSE FUNCTIONS       *
 *                               *
 *********************************/

  /**
   * Connect to the database
   * @return the connection to the database 
   */
  function conn_db() {
    $dsn = 'mysql:host=' . DB_URL . ';dbname=' . DB_NAME . ';charset=UTF8';
    try {
      $dbh = new PDO($dsn, DB_USER, DB_PWD);
      $dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $dbh;
    } catch(PDOException $e) {
      print "error ! :" . $e -> getMessage() . "<br>";
      die();
    }
  }

  /**
   * Select all the parkings in the database
   * @return the parkings found
   */
  function getAllParkings() {
    try {
      $dbh = conn_db();

      $sql = "SELECT *
              FROM parkings;";

      $stmt = $dbh->prepare($sql);
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      $stmt->execute();
    } catch (PDOException $e) {
      echo $e->getMessage();
      die();
    }

    return $stmt->fetchAll();
  }

  /**
   * Select a parking in the database
   * @param $id The id of the wanted parking
   * @return the parkings found
   */
  function getParkingById($id) {
    try {
      $dbh = conn_db();

      $sql = "SELECT *
              FROM parkings
              WHERE id = :id";

      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      $stmt->execute();
    } catch(PDOException $e) {
      echo $e->getMessage();
      die();
    }

    return $stmt->fetch();
  }

  /**
   * Select all the sensors in a parking
   * @param $parkingId The id of the parking where the sensors are
   * @return the found sensors
   */
  function getSensorsByParkingsId($parkingId) {
    try {
      $dbh = conn_db();
      
      $sql = "SELECT sensors.id, sigfox_id
              FROM sensors
              INNER JOIN parkings ON sensors.fk_parking = parkings.id
              WHERE sensors.fk_parking = :id;";

      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':id', $parkingId, PDO::PARAM_INT);
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      $stmt->execute();
    } catch (PDOException $e) {
      echo $e->getMessage();
      die();
    }

    return $stmt->fetchAll();
  }

  /**
   * Select a sensor by it's Sigfox Id
   * @param $sigfoxId The id of the sensor in the Sigfox Dashboard
   * @return the found sensor
   */
  function getSensorBySigfoxId($sigfoxId) {
    try {
      $dbh = conn_db();

      $sql = "SELECT id
              FROM sensors
              WHERE sigfox_id = :id";

      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':id', $sigfoxId);
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      $stmt->execute();
    } catch(PDOException $e) {
      echo $e->getMessage();
      die();
    }

    return (int)$stmt->fetch()['id'];
  }

  /**
   * Select a sensor in a parking
   * @param $parkingId The id of the parking where the sensor is
   * @param $sensorId  The id of the wanted sensor
   * @return the found sensor
   */
  function getSensorsByIdAndParkingsId($parkingId, $sensorId) {
    try {
      $dbh = conn_db();
      
      $sql = "SELECT sensors.id, sigfox_id
              FROM sensors
              INNER JOIN parkings ON sensors.fk_parking = parkings.id
              WHERE sensors.fk_parking = :id AND sensors.id = :sensorId;";

      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':id', $parkingId, PDO::PARAM_INT);
      $stmt->bindParam(':sensorId', $sensorId, PDO::PARAM_INT);
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      $stmt->execute();
    } catch (PDOException $e) {
      echo $e->getMessage();
      die();
    }

    return $stmt->fetchAll();
  }

  /**
   * Add a new parking in the database
   * @param $name The name of the parking
   * @param $address The address of the parking
   * @param $city The city of the parking
   * @param $country The 2 letters country code of the parking
   * @return the id of the inserted element
   */
  function addParking($name, $address, $city, $country) {
    try {
      $dbh = conn_db();

      $sql = "INSERT INTO parkings (name, address, city, country)
              VALUES (:name, :address, :city, :country);";

      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':address', $address);
      $stmt->bindParam(':city', $city);
      $stmt->bindParam(':country', $country);

      $stmt->execute();
    } catch(PDOException $e) {
      echo $e->getMessage();
      die();
    }

    return $dbh->lastInsertId();
  }

  /**
   * Add a new sensor in the database
   * @param $device_id The id of the sensor in the Sigfox Dashboard
   * @param $parkingId The id of the parking where the sensor is located
   * @return the id of the inserted element
   */
  function addSensor($device_id, $parkingId) {
    try {
      $dbh = conn_db();

      $sql = "INSERT INTO sensors (sigfox_id, fk_parking)
              VALUES (:sigfox_id, :fk_parking);";

      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':sigfox_id', $device_id);
      $stmt->bindParam(':fk_parking', $parkingId, PDO::PARAM_INT);

      $stmt->execute();
    } catch(PDOException $e) {
      echo $e->getMessage();
      die();
    }

    return $dbh->lastInsertId();
  }

  /**
   * Add a new sensor result in the database
   * @param $sensorData The data sent by the sensor
   * @param $sensorSigfoxId The id of the sensor in the Sigfox Dashboard
   * @param $epochDate The epoch time when the result was produced
   * @param $sequence The sequence number of the result
   * @param $parkingId The id of the parking where the sensor is located
   * @return the id of the inserted element
   */
  function addResult($sensorData, $sensorSigfoxId, $epochDate, $sequence, $parkingId) {
    try {
      $dbh = conn_db();
      $data = json_decode(convertSensorData($sensorData), true);

      $date = convertEpoch($epochDate);
      $sensorId = getSensorBySigfoxId($sensorSigfoxId);

      if ($sensorId == 0) {
        addSensor($sensorSigfoxId, $parkingId);
      }

      $sql = "INSERT INTO results (occupation, date, sequence, temperature, idleVoltage, transmissionVoltage, firmwareVersion, magnetometerX, magnetometerY, magnetometerZ, messageType, fk_sensor)
              VALUES (:occupation, :date, :sequence, :temperature, :idleVoltage, :transmissionVoltage, :firmwareVersion, :magnetometerX, :magnetometerY, :magnetometerZ, :messageType, :fk_sensor);";
      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':occupation', $data['state']);
      $stmt->bindParam(':date', $date);
      $stmt->bindParam(':sequence', $sequence, PDO::PARAM_INT);
      $stmt->bindParam(':temperature', $data['temperature'], PDO::PARAM_INT);
      $stmt->bindParam(':idleVoltage', $data['idleVoltage']);
      $stmt->bindParam(':transmissionVoltage', $data['transmissionVoltage']);
      $stmt->bindParam(':firmwareVersion', $data['firmwareVersion']);
      $stmt->bindParam(':magnetometerX', $data['magnetometerX'], PDO::PARAM_INT);
      $stmt->bindParam(':magnetometerY', $data['magnetometerY'], PDO::PARAM_INT);
      $stmt->bindParam(':magnetometerZ', $data['magnetometerZ'], PDO::PARAM_INT);
      $stmt->bindParam(':messageType', $data['messageType'], PDO::PARAM_INT);
      $stmt->bindParam(':fk_sensor', $sensorId, PDO::PARAM_INT);

      $stmt->execute();
    } catch(PDOException $e) {
      echo $e->getMessage();
      die();
    }

    return $dbh->lastInsertId();
  }
