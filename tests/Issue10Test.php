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
    $originalPoints = array(48.000006, 2.000004,48.00001,2.00000);
    $encoded = Polyline::Encode($originalPoints);
    $this->assertEquals('a_~cH_seK??', $encoded);
    $decodedPoints = Polyline::Decode($encoded);
    $this->assertTrue($decodedPoints[0] === $decodedPoints[2]);
    $this->assertTrue($decodedPoints[1] === $decodedPoints[3]);
  }
}
