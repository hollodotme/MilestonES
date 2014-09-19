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
	public function testSerializeData( $data, $expected_json_string )
	{
		$serializer = new JsonSerializer();

		$this->assertJsonStringEqualsJsonString( $expected_json_string, $serializer->serializeData( $data ) );
	}

	/**
	 * @dataProvider unserializeProvider
	 */
	public function testUnserializeData( $json_string, $expected_data )
	{
		$serializer = new JsonSerializer();

		$this->assertEquals( $expected_data, $serializer->unserializeData( $json_string ) );
	}

	/**
	 * @dataProvider serializeProvider
	 */
	public function testUnserializeDataInvertsSerializeData( $data )
	{
		$serializer = new JsonSerializer();

		$this->assertEquals( $data, $serializer->unserializeData( $serializer->serializeData( $data ) ) );
	}

	public function serializeProvider()
	{
		$object      = new \stdClass();
		$object->key = "value";

		return array(
			array(array("value"), '["value"]'),
			array($object, '{"key":"value"}'),
		);
	}

	public function unserializeProvider()
	{
		$object      = new \stdClass();
		$object->key = "value";

		return array(
			array('{"key":"value"}', $object),
			array('["value"]', array("value")),
		);
	}
}
 