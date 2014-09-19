<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Serialization;

use hollodotme\MilestonES\Serializers\PhpSerializer;

class PhpSerializerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider serializeProvider
	 */
	public function testSerializeData( $data, $expected_string )
	{
		$serializer = new PhpSerializer();

		$this->assertEquals( $expected_string, $serializer->serializeData( $data ) );
	}

	/**
	 * @dataProvider unserializeProvider
	 */
	public function testUnserializeData( $json_string, $expected_data )
	{
		$serializer = new PhpSerializer();

		$this->assertEquals( $expected_data, $serializer->unserializeData( $json_string ) );
	}

	/**
	 * @dataProvider serializeProvider
	 */
	public function testUnserializeDataInvertsSerializeData( $data )
	{
		$serializer = new PhpSerializer();

		$this->assertEquals( $data, $serializer->unserializeData( $serializer->serializeData( $data ) ) );
	}

	public function serializeProvider()
	{
		$object      = new \stdClass();
		$object->key = "value";

		return array(
			array(array("value"), 'a:1:{i:0;s:5:"value";}'),
			array($object, 'O:8:"stdClass":1:{s:3:"key";s:5:"value";}'),
			array(array('key' => 'value'), 'a:1:{s:3:"key";s:5:"value";}'),
		);
	}

	public function unserializeProvider()
	{
		$object      = new \stdClass();
		$object->key = "value";

		return array(
			array('a:1:{i:0;s:5:"value";}', array("value")),
			array('O:8:"stdClass":1:{s:3:"key";s:5:"value";}', $object),
			array('a:1:{s:3:"key";s:5:"value";}', array('key' => 'value')),
		);
	}
}
 