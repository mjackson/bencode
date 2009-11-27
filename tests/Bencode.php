<?php

require_once 'PHPUnit/Framework.php';

require_once dirname(dirname(__FILE__)) . '/lib/Bencode.php';

class OptionParserTest extends PHPUnit_Framework_TestCase
{

    public function testEncodeDecode()
    {
        $original = "hello world";
        $encoded = "11:hello world";
        $this->assertEquals(Bencode::encode($original), $encoded);
        $this->assertEquals(Bencode::decode($encoded), $original);
        $this->assertEquals(Bencode::decode(Bencode::encode($original)), $original);

        $original = 123;
        $encoded = "i123e";
        $this->assertEquals(Bencode::encode($original), $encoded);
        $this->assertEquals(Bencode::decode($encoded), $original);
        $this->assertEquals(Bencode::decode(Bencode::encode($original)), $original);

        $original = array(1, 2, 3);
        $encoded = "li1ei2ei3ee";
        $this->assertEquals(Bencode::encode($original), $encoded);
        $this->assertEquals(Bencode::decode($encoded), $original);
        $this->assertEquals(Bencode::decode(Bencode::encode($original)), $original);

        $original = array("a" => "b", "c" => "def");
        $encoded = "d1:a1:b1:c3:defe";
        $this->assertEquals(Bencode::encode($original), $encoded);
        $this->assertEquals(Bencode::decode($encoded), $original);
        $this->assertEquals(Bencode::decode(Bencode::encode($original)), $original);
    }

}
