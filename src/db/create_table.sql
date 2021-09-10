CREATE TABLE parkings (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(40) NOT NULL,
  address VARCHAR(50) NOT NULL,
  city VARCHAR(50) NOT NULL,
  country VARCHAR(3) NOT NULL
);

CREATE TABLE sensors (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  sigfox_id VARCHAR(40) NOT NULL UNIQUE,
  fk_parking INT NOT NULL
);

ALTER TABLE sensors
  ADD CONSTRAINT pk_fk_sens_park
  FOREIGN KEY (fk_parking) 
  REFERENCES parkings (id);

CREATE TABLE results (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  occupation BIT NOT NULL,
  date DATETIME NOT NULL,
  sequence INT NOT NULL,
  temperature INT NOT NULL,
  idleVoltage FLOAT NOT NULL,
  transmissionVoltage FLOAT NOT NULL,
  firmwareVersion FLOAT NOT NULL,
  magnetometerX INT NOT NULL,
  magnetometerY INT NOT NULL,
  magnetometerZ INT NOT NULL,
  messageType INT NOT NULL,
  fk_sensor INT NOT NULL
);

ALTER TABLE results
  ADD CONSTRAINT pk_fk_res_sens 
  FOREIGN KEY (fk_sensor) 
  REFERENCES sensors(id);