<?php

/**
 * Well Known Binary Polyline example
 *
 * An example class to convert well-known binary files to Google encoded strings
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   WkbPolyline
 * @since     v1.2.2
 * @copyright 2014 emcconville
 * @license   GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @link      https://github.com/emcconville/google-map-polyline-encoding-tool
 * @author    E. McConville <emcconville@emcconville.com>
 */

class WkbPolyline extends Polyline
{
  const ENDIAN_BIG    =  0;
  const ENDIAN_LITTLE =  1;
  
  protected $fd;
  protected $cursor;
  protected $endianness;
  
  public function encodeFromFile($filename)
  {
    $this->fd = fopen($filename, 'rb');
    assert(is_resource($this->fd));
    
    $this->endianness = $this->_readByte();
    $header = $this->_readU32() % 1000;
    
    assert($header == 3, "This example only covers polylines");
    
    $points = array();
    
    // Iterate over cirlces
    for($i=0,$l=$this->_readU32(); $i < $l; $i++ )
    {
      // Iterate over points
      for($j=0,$p=$this->_readU32(); $j < $p; $j++ )
      {
        $points[] = $this->_readDouble(); // latitude
        $points[] = $this->_readDouble(); // longitude
      }
    }
    
    return parent::Encode($points);
  }
  
  
  private function _chunk($size=1)
  {
    $this->cursor += $size;
    return fread($this->fd,$size);
  }

  private function _readDouble()
  {
    $data = $this->_chunk(8);
    if($this->endianness == self::ENDIAN_BIG)
      $data = strrev($data);
    $double = unpack('ddouble',$data);
    if(!isset($double['double'])) throw new Exception("Unable to read double");
    return $double['double'];
  }
  private function _readU32()
  {
    $uint32 = unpack($this->endianness ? 'Vlong' : 'Nlong', $this->_chunk(4));
    if(!isset($uint32['long'])) throw new Exception("Unable to read unsigned long");
    return $uint32['long'];
  }

  private function _readByte()
  {
    return ord($this->_chunk());
  }

}