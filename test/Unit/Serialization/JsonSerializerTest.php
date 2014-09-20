<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Serialization;

use hollodotme\MilestonES\Serializers\JsonSerializer;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider serializeProvider
	 */
	public function testSerializeData( \stdClass $data, $expected_json_string )
	{
		$serializer = new JsonSerializer();

		$this->assertJsonStringEqualsJsonString( $expected_json_string, $serializer->serializeData( $data ) );
	}

	/**
	 * @dataProvider unserializeProvider
	 */
	public function testUnserializeData( $json_string, \stdClass $expected_data )
	{
		$serializer = new JsonSerializer();

		$this->assertEquals( $expected_data, $serializer->unserializeData( $json_string ) );
	}

	/**
	 * @dataProvider serializeProvider
	 */
	public function testUnserializeDataInvertsSerializedData( \stdClass $data )
	{
		$serializer = new JsonSerializer();

		$this->assertEquals( $data, $serializer->unserializeData( $serializer->serializeData( $data ) ) );
	}

	public function serializeProvider()
	{
		$object      = new \stdClass();
		$object->key = "value";

		return [
			[ $object, '{"key":"value"}' ],
		];
	}

	public function unserializeProvider()
	{
		$object      = new \stdClass();
		$object->key = "value";

		return [
			[ '{"key":"value"}', $object ],
		];
	}
}
