<?php
  
class EncodedToSVG extends Polyline
{
  /**
   * Decode string and generate a full SVG document.
   *
   * @uses DOMDocument
   * @param string $encoded - Encoded poyline
   * @return string - SVG document
   */
  public static function DecodeToSVG($encoded)
  {
    $points = parent::Decode($encoded);
    list($x, $y) = self::shiftPoint($points);
    $path = sprintf('M %f %f L ', $x, $y);
    // Init bounding box
    $minX = $maxX = $x;
    $minY = $maxY = $y;
    while ( $points ) {
      list($x, $y) = self::shiftPoint($points);
      $path .= sprintf('%f %f, ', $x, $y);
      // Grow MBR
      if($x < $minX) $minX = $x;
      if($y < $minY) $minY = $y;
      if($x > $maxX) $maxX = $x;
      if($y > $maxY) $maxY = $y;
    }
    // Close poylgon
    $path = rtrim($path, ', ') . ' Z';
    
    // Build XML
    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->formatOutput = true;
    // Root
    $root = $dom->createElementNS('http://www.w3.org/2000/svg', 'svg');
    $root->appendChild(new DomAttr('version', '1.2'));
    $mbr =  sprintf("%f %f %f %f", $minX, $minY, abs($maxX - $minX), abs($maxY - $minY));
    $root->appendChild(new DomAttr('viewBox', $mbr));
    $root->appendChild(new DomAttr('viewport-fill', 'lightblue'));
    $root->appendChild(new DomAttr('style', 'background-color:lightblue;'));
    // Group
    $g = $dom->createElement('g');
    $g->appendChild(new DomAttr('stroke', 'rgba(0,0,0,0.5)'));
    $g->appendChild(new DomAttr('stroke-width', '0.25%'));
    $g->appendChild(new DomAttr('fill', 'beige'));
    // Path
    $p = $dom->createElement('path');
    $p->appendChild(new DomAttr('d', $path));
    
    // Pull it all together
    $g->appendChild($p);
    $root->appendChild($g);
    $dom->appendChild($root);
    
    return $dom->saveXML();
  }

  /**
   * Shift point tuple from start of list.
   *
   * Remember that latitude is Y, and longitude is X on the coordinate system.
   * Depending on your data set, you may need to adjust signing to match hemispheres.
   *
   * @param array &$points - Reference to list
   * @return array - Tuple of (x, y)
   */
  private static function shiftPoint(&$points)
  {
    $y = array_shift($points);
    $x = array_shift($points);
    return array( $x, $y * -1 );
  }
}
