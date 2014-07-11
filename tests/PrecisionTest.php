<?php
class PrecisionPolyline extends Polyline
{
    protected static $precision = 6;
}
class PrecisionTest extends PHPUnit_Framework_TestCase
{
    protected $encoded = 'q}~~|AdshNkSsBid@eGqBlm@yKhj@bA?';
    protected $points = array(
        49.283049, -0.250691,
        49.283375, -0.250633,
        49.283972, -0.250502,
        49.284029, -0.251245,
        49.284234, -0.251938,
        49.284200, -0.251938
        );

    /**
     * @covers Polyline::Encode
     * @covers Polyline::Flatten
     */
    public function testEncodePrecision()
    {
        $this->assertEquals(
            $this->encoded,
            PrecisionPolyline::Encode($this->points)
        );
    }

    /**
     * @covers Polyline::Decode
     */
    public function testDecodePrecision()
    {
        $this->assertEquals(
            $this->points,
            PrecisionPolyline::Decode($this->encoded)
        );
    }
}
