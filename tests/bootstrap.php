<?php
// Try to load distributed library
if (file_exists($file = __DIR__.'/../dist/Polyline.php')) {
    if (!class_exists('Polyline')) {
        print "Loading distributed library\n";
        require_once $file;
    }
// Else, attempt to load library source
} elseif (file_exists($file = __DIR__.'/../src/Polyline.php')) {
    if (!class_exists('Polyline')) {
        print "Loading library source\n";
        require_once $file;
    }
} else { // Panic and die
    exit(1);
}
