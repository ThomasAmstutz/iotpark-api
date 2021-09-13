# API Enpoints

## Get
| URI | Description | State |
-|-|-|
| /api/parkings                       | Return all the parkings in the databse | Done |
| /api/parkings/<int\>                | Return a particular parking            | Done |
| /api/parkings/<int\>/sensors        | Retourne every sensors in a parking    | Done |
| /api/parkings/<int\>/sensors/<int\> | Return one sensor in a parking         | Done |

<br>

## Post
| URI | Description | State |
-|-|-|
| /api/parkings                | Add a parking             | Done |
| /api/parkings/<int\>/sensors | Add a sensor to a parking | Done |

<br>

## Put
| URI | Description | State |
-|-|-|
| /api/parkings/<int\>                | Modify a parking             | Todo |
| /api/parkings/<int\>/sensors/<int\> | Modify a sensor in a parking | Todo |

<br>

## Delete
| URI | Description | State |
-|-|-|
| /api/parkings/<int\>                | Delete a parking             | Todo |
| /api/parkings/<int\>/sensors/<int\> | Delete a sensor in a parking | Todo |