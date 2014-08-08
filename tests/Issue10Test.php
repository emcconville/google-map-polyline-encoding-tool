<?php
/**
 * Issue #10
 * Wrong rounding method for google.
 * @link https://github.com/emcconville/google-map-polyline-encoding-tool/issues/10
 */
class Issue10 extends PHPUnit_Framework_TestCase
{
  public function testRounding()
  {
    $original_points = array(48.000006, 2.000004,48.00001,2.00000);
    $encoded = Polyline::Encode($original_points);
    $this->assertEquals('a_~cH_seK??',$encoded);
    $decoded_points = Polyline::Decode($encoded);
    $this->assertTrue($decoded_points[0] === $decoded_points[2]);
    $this->assertTrue($decoded_points[1] === $decoded_points[3]);
  }
}
