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
	public function testSerializeData( \stdClass $data, $expected_string )
	{
		$serializer = new PhpSerializer();

		$this->assertEquals( $expected_string, $serializer->serializeData( $data ) );
	}

	/**
	 * @dataProvider unserializeProvider
	 */
	public function testUnserializeData( $string, \stdClass $expected_data )
	{
		$serializer = new PhpSerializer();

		$this->assertEquals( $expected_data, $serializer->unserializeData( $string ) );
	}

	/**
	 * @dataProvider serializeProvider
	 */
	public function testUnserializeDataInvertsSerializedData( \stdClass $data )
	{
		$serializer = new PhpSerializer();

		$this->assertEquals( $data, $serializer->unserializeData( $serializer->serializeData( $data ) ) );
	}

	public function serializeProvider()
	{
		$object      = new \stdClass();
		$object->key = "value";

		return [
			[ $object, 'O:8:"stdClass":1:{s:3:"key";s:5:"value";}' ],
		];
	}

	public function unserializeProvider()
	{
		$object      = new \stdClass();
		$object->key = "value";

		return [
			[ 'O:8:"stdClass":1:{s:3:"key";s:5:"value";}', $object ],
		];
	}
}
