# Well-Known Binary

Vector geometry files can be standardized as a common text, or binary file.
Usually referenced as *well-known text* (`.wkt`), or *well-known binary* (`.wkb`).
This examples demonstrates reading `.wkb` files and outputting encoded strings.

***Notice:***
In this example; only Polygons are supported. Points, Lines, and MultiPolygons
can quickly be completed.

## Usage

    <?php
    require 'src/Polyline.php';
    require 'examples/WellKnownBinary/WkbPolyline.php';
    
    $wkb = new WkbPolyline();
    $encoded = $wkb->encodeFromFile( 'examples/WellKnownBinary/cleveland-mbr.wkb' );
    //=> 'wz||Fr~vrN?_sbAhwh@??~rbAiwh@?'
    print json_encode( Polyline::decode($encoded) );
    //=> [
    //    41.60444,-81.87898,
    //    41.60444,-81.53274,
    //    41.39063,-81.53274,
    //    41.39063,-81.87898,
    //    41.60444,-81.87898
    // ]
