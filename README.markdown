# Google Maps Polyline Encoding Tool

A simple PHP class for translating latitude & longitude points into a Google Map encoded [polyline][polylineRef].

### Encoding

```php
<?php
require_once 'Polyline.php';

// Points to encode
$points = array(
        array(41.89084,-87.62386),
        array(41.89086,-87.62279),
        array(41.89028,-87.62277),
        array(41.89028,-87.62385),
        array(41.89084,-87.62386)
    );

print Polyline::Encode($points);

?>
```
Output:

```
wxt~Fd`yuOCuErBC?vEoB@
```

![Tribune](http://emcconville.com/Polyline/tribune.png)

### Decoding

```php
<?php
require_once 'Polyline.php';

// String to decode
$str    = "kiw~FpoavObBA?fAzEC";
$points = Polyline::Decode($str);
var_dump($points);
?>
```

Output:

```
array(8) {
  [0] =>
  double(41.90374)
  [1] =>
  double(-87.66729)
  [2] =>
  double(41.90324)
  [3] =>
  double(-87.66728)
  [4] =>
  double(41.90324)
  [5] =>
  double(-87.66764)
  [6] =>
  double(41.90214)
  [7] =>
  double(-87.66762)
}
```

![Tribune](http://emcconville.com/Polyline/dustygroove.png)

### Singleton

The Polyline object can be initialized as a single object, and be referenced throughout an application.

```php
<?php
require_once 'Polyline.php';

// Create singleton
$myPolyline = Polyline::Singleton();

// Create a polyline from an array of points
$myPolyline->polyline("tribune",array(41.89084,-87.62386,41.89086,-87.62279
                                      41.89028,-87.62277,41.89028,-87.62385));

// Create a polyline from an encoded string
$myPolyline->polyline("dustygroove","kiw~FpoavObBA?fAzEC");

/* ... do work .. */

$anotherPolyline = Polyline::Singleton();
var_dump( $anotherPolyline->getTribunePoints() );
var_dump( $anotherPolyline->getDustyGrooveEncoded() );
?>
```

Output:

```
string(22) "wxt~Fd`yuOCuErBC?vEoB@"

array(8) {
  [0] =>
  double(41.90374)
  [1] =>
  double(-87.66729)
  [2] =>
  double(41.90324)
  [3] =>
  double(-87.66728)
  [4] =>
  double(41.90324)
  [5] =>
  double(-87.66764)
  [6] =>
  double(41.90214)
  [7] =>
  double(-87.66762)
}
```

[polylineRef]: http://code.google.com/apis/maps/documentation/javascript/reference.html#Polygon
[algorithmRef]: http://code.google.com/apis/maps/documentation/utilities/polylinealgorithm.html
