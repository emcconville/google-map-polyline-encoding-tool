# Google Maps Polyline Encoding Tool [![Build Status][buildStatusImage]][buildStatusLink]

A simple PHP class for translating [polyline][polylineRef] into an [encoded][algorithmRef] strings for Google Maps.

## Family

|Requires|[google-map-polyline-encoding-tool][l1]|[polyline-encoder][l2]|[php_polyline][l3]|
|:-------|:-------------------------------------:|:--------------------:|:----------------:|
|PHP     | 5.3                                   | 5.4                  | C-API            |

|Supports|[google-map-polyline-encoding-tool][l1]|[polyline-encoder][l2]|[php_polyline][l3]|
|:-------|:-------------------------------------:|:--------------------:|:----------------:|
|Google  |&#10004;                               |&#10004;              |&#10004;          |
|Bing    |                                       |&#10004;              |                  |
|Precision|&#10004;                              |&#10004;              |&#10004;          |
|Tuple   |                                       |&#10004;              |&#10004;          |
|Traits  |                                       |&#10004;              |                  |
|Abstraction|                                    |&#10004;              |                  |

|Hosted   |[google-map-polyline-encoding-tool][l1]|[polyline-encoder][l2]|[php_polyline][l3]|
|:--------|:-------------------------------------:|:--------------------:|:----------------:|
|Github   |&#10004;                               |&#10004;              |                  |
|Bitbucket|                                       |&#10004;              |&#10004;          |


## Install

The easiest way to use this library is to clone the GitHub, build a distributed copy, and add the library into your application.

```
shell~> git clone git://github.com/emcconville/google-map-polyline-encoding-tool.git
shell~> make
shell~> cp dist/Polyline.php /path/to/your/application/includes/Polyline.php
```

## Usage

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

![Tribune][tribuneTower]

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

![Tribune][dustyGroove]


### Specify precision

Precision defaults to 1e-5 (0.00001) which is expected by Google Map API. Other 
API's like [OSRM][osrmRef] expect a precision of 1e-6.
You can adjust the precision you want by sub-classing Polyline, and overwrite
the `$precision` static property.

```
class PolylineOSRM extents Polyline
{
	protected static $precision = 6;
}
$points = PolylineOSRM::Decode($line);
$line = PolylineOSRM::Encode($points);
```

**Caution**

 - Adjusting the precision level will not guarantee improved accuracy. Existing
   issues with PHP's internal float point arithmetic can contribute accuracy issues.
 - Third party libraries will not automatically know what level of precision was
   used during encoding.


### Namespace

By default, no namespace is defined. If a user wishes to have this library under
a namespace, simply run the following.

```
shell~> make namespace
```
This will copy a namespace-enabled version to `dist/emcconville/Polyline.php`.
Behavior & usage are preserved.

```php
use emcconville\Polyline as GooPly;
$gooString = GooPly::decode($points);
```

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

// Re-establish singleton object
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
[buildStatusLink]: http://travis-ci.org/emcconville/google-map-polyline-encoding-tool
[buildStatusImage]: https://secure.travis-ci.org/emcconville/google-map-polyline-encoding-tool.png
[tribuneTower]: http://emcconville.com/Polyline/tribune.png
[dustyGroove]: http://emcconville.com/Polyline/dustygroove.png
[osrmRef]: http://map.project-osrm.org/
[l1]: https://github.com/emcconville/google-map-polyline-encoding-tool
[l2]: https://bitbucket.org/emcconville/polyline-encoder
[l3]: https://bitbucket.org/emcconville/php_polyline
