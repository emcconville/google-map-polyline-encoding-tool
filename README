Google Maps Polyline Encoding Tool
==================================
A simple PHP class for translating latitude & longitude points into a Google Map encoded [polyline][polylineRef].

Usage 
-----
```php
<?php
require_once 'Polyline.php';

// Points to encode
$points = array(
        array(41.89084857473952,-87.6238678202377),
        array(41.89086055484973,-87.62279493663175),
        array(41.890289500431564,-87.62277884337766),
        array(41.89028151362027,-87.62385172698362),
        array(41.89084857473952,-87.6238678202377)
    );

$encodedString = Polyline::Encode($points);
//=> "wxt~Fd`yuOCuErBC?vEoB@"

?>
```

[polylineRef]: http://code.google.com/apis/maps/documentation/javascript/reference.html#Polygon
[algorithmRef]: http://code.google.com/apis/maps/documentation/utilities/polylinealgorithm.html
[exampleMap]: http://maps.googleapis.com/maps/api/staticmap?sensor=false&size=200x200&path=weight:1|fillcolor:orange|enc:wxt~Fd%60yuOCuErBC?vEoB@