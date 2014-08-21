# Well-Known Binary

Vector geometry files can be standardized as a common text, or binary file.
Usually referenced as *well-known text* (`.wkt`), or *well-known binary* (`.wkb`).
This examples demonstrates reading `.wkb` files and outputting encoded strings.

***Notice:***
In this example; only Polygons are supported. Points, Lines, and MultiPolygons
can quickly be completed.

## Usage

```php
<?php
require 'src/Polyline.php';
require 'examples/WellKnownBinary/WkbPolyline.php';

$wkb = new WkbPolyline();
$encoded = $wkb->encodeFromFile( 'examples/WellKnownBinary/cleveland-mbr.wkb' );
//=> 'wz||Fr~vrN?_sbAhwh@??~rbAiwh@?'
$points = Polyline::decode($encoded);
//=> array(
//    41.60444, -81.87898, 41.60444, -81.53274,
//    41.39063, -81.53274, 41.39063, -81.87898,
//    41.60444, -81.87898
// )
```
![Cleveland Rocks][cleveland]

```php
// Or
$blob = file_get_contents( 'examples/WellKnownBinary/cleveland-mbr.wkb' );
$encoded = $wkb->encodeFromBlob( $blob );
//=> 'wz||Fr~vrN?_sbAhwh@??~rbAiwh@?'
```

[cleveland]: http://emcconville.com/Polyline/cleveland.png