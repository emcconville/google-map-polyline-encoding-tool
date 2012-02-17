# Google Maps Polyline Encoding Tool

A simple PHP class for translating latitude & longitude points into a Google Map encoded [polyline][polylineRef].

### Encoding

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
$points = Polyline::Decode("kiw~FpoavObBA?fAzEC");
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

[polylineRef]: http://code.google.com/apis/maps/documentation/javascript/reference.html#Polygon
[algorithmRef]: http://code.google.com/apis/maps/documentation/utilities/polylinealgorithm.html
