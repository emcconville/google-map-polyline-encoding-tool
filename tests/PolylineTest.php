<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-02-17 at 14:08:49.
 */
class PolylineTest extends PHPUnit_Framework_TestCase
{
    protected $polylineName = "HydeParkRecords";
    protected $encoded = '}`c~FlyquOnAE?`B@|HBpGJ?@pI';
    protected $points = array(
            array(41.79999,-87.58695),
            array(41.79959,-87.58692),
            array(41.79959,-87.58741),
            array(41.79958,-87.58900),
            array(41.79956,-87.59037),
            array(41.79950,-87.59037),
            array(41.79949,-87.59206)
        );

    public function testSingleton()
    {
        $object = Polyline::Singleton();
        $this->assertInstanceOf('Polyline',$object);
        return $object;
    }

    /**
     * @depends testSingleton
     */
    public function testGooglePolyline(Polyline $object)
    {
        // uses example from google maps api docs
        // at https://developers.google.com/maps/documentation/utilities/polylinealgorithm
        $points = array(
            array(38.5, -120.2),
            array(40.7, -120.95),
            array(43.252, -126.453)
        );

        $encoded = $object->importPolyArray($this->polylineName, $points);
        $this->assertEquals('_p~iF~ps|U_ulLnnqC_mqNvxq`@', $encoded);
    }

    /**
     * @depends testSingleton
     */
    public function testPolyline(Polyline $object)
    {
        $encoded = $object->importPolyArray($this->polylineName,$this->points);
        $this->assertEquals($encoded,$this->encoded);
        $hash = $object->getNode($this->polylineName);
        $this->assertEquals($encoded,$hash['encoded']);
        return $object;
    }

    /**
     * @depends testPolyline
     */
    public function testGetPolyline(Polyline $object)
    {
        $this->assertEquals($this->encoded,$object->getPolyline($this->polylineName,'encoded'));
        $this->assertNull($object->getPolyline('I_Dont_exsits','encoded'));
        return $object;
    }

    /**
     * @depends testSingleton
     */
    public function testImportPolyString(Polyline $object)
    {
        $x = $object->importPolyString('nodeKey', $this->encoded);
        $this->assertEquals(14, count($x));
    }

    /**
     * @depends testGetPolyline
     */
    public function testGetters(Polyline $object)
    {
        $this->assertEquals($this->encoded,$object->getEncoded($this->polylineName));
        $this->assertEquals($this->encoded,$object->getHydeParkRecordsEncoded());
        return $object;
    }

     /**
     * @expectedException BadMethodCallException
     * @depends testPolyline
     */
    public function testGettersException(Polyline $object)
    {
        $object->thisMethodFails();
        return $object;
    }

    /**
     * @depends testGetters
     */
    public function testListPolylines(Polyline $object)
    {
        $list = $object->listPolylines();
        $this->assertCount(2, $list);
    }

    public function testEncode()
    {
        // Remove the following lines when you implement this test.
        $this->assertEquals($this->encoded,Polyline::Encode($this->points));
    }

    public function testDecode()
    {
        $this->assertCount(count($this->points) * 2, Polyline::Decode($this->encoded));
    }

    public function testFlatten()
    {
        $paired = array(
            array(1,2),
            array(3,4),
            array(5,6)
        );
        $this->assertEquals(array(1,2,3,4,5,6),Polyline::Flatten($paired));
    }

    public function testPair()
    {
        $paired = array(
            array(1,2),
            array(3,4),
            array(5,6)
        );
        $this->assertEquals($paired,Polyline::Pair(array(1,2,3,4,5,6)));
    }
}
