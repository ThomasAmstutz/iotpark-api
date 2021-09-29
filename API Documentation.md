# API Documentation

This file explains everything about the API and how to use it.

## API Enpoints

### Get
| URI | Description | State |
-|-|-|
| /api/parkings                       | Return all the parkings in the databse | Done |
| /api/parkings/<int\>                | Return a particular parking            | Done |
| /api/parkings/<int\>/sensors        | Retourne every sensors in a parking    | Done |
| /api/parkings/<int\>/sensors/<int\> | Return one sensor in a parking         | Done |
| /api/sensors/<int\>/results         | Return the results of one sensor       | Done |
| /api/sensors/<int\>/results/<int\>  | Return one result of one sensor        | Done |

<br>

### Post
| URI | Description | State |
-|-|-|
| /api/parkings                        | Add a parking             | Done |
| /api/parkings/<int\>/sensors         | Add a sensor to a parking | Done |
| /api/parkings/<int\>/sensors/results | Add a result              | Done |

<br>

### Put
| URI | Description | State |
-|-|-|
| /api/parkings/<int\>                | Modify a parking             | Todo |
| /api/parkings/<int\>/sensors/<int\> | Modify a sensor in a parking | Todo |

<br>

### Delete
| URI | Description | State |
-|-|-|
| /api/parkings/<int\>                | Delete a parking             | Todo |
| /api/parkings/<int\>/sensors/<int\> | Delete a sensor in a parking | Todo |

## API Parameters
Every request to the API must be made using the "application/json" type.
### GET parameters
#### /parkings
No parameters required
#### /parkings/<int\>
Parking ID (int) parameter in the URI

#### /parkings/<int\>/sensors
Parking ID (int) parameter in the URI

#### /parkings/<int\>/sensors/<int\>
Parking ID (int) parameter and sensor ID (int) parameter in the URI

#### /sensors/<int\>/results
Sensor ID (int) parameter in the URI

#### /sensors/<int\>/results/<int\>
Sensor ID (int) parameter and result ID (int) parameter in the URI

### POST parameters
#### /parkings
| Parameter | Description | Required |
-|-|-|
| name    | Name of the parking                            | Yes |
| address | Address of the parking                         | Yes |
| city    | City where the parking is located              | Yes |
| country | Country code of the parking (e.g.: CH, FR, US) | Yes |

#### /parkings/<int\>/sensors
Parking ID (int) parameter in the URI
| Parameter | Description | Required |
-|-|-|
| deviceId | ID of the device in the Sigfox dashboard | Yes |

#### /parkings/<int\>/sensors/results
Parking ID (int) parameter in the URI
| Parameter | Description | Required |
-|-|-|
| data      | Data sent by the device                               | Yes |
| device    | ID of the device in the Sigfox dashboard              | Yes |
| timestamp | Time in Epoch format when the data was recorded       | Yes |
| seqNumber | Sequence number of the result in the Sigfox dashboard | Yes |
| parkingId | ID of the parking in the database                     | Yes |

### PUT parameters
TODO
### DELETE parameters
TODO
