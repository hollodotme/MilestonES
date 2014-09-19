<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Serialization;

use hollodotme\MilestonES\Contract;
use hollodotme\MilestonES\SerializerRegistry;
use hollodotme\MilestonES\Serializers\JsonSerializer;
use hollodotme\MilestonES\Serializers\PhpSerializer;

class SerializerRegistryTest extends \PHPUnit_Framework_TestCase
{
	public function testCanOverrideSerializerForSameContract()
	{
		$registry = new SerializerRegistry();
		$contract = new Contract( 'Some\\Contract' );
		$registry->registerSerializerForContract( $contract, new JsonSerializer() );

		$serializer = $registry->getSerializerForContract( $contract );

		$this->assertInstanceOf( JsonSerializer::class, $serializer );

		$registry->registerSerializerForContract( $contract, new PhpSerializer() );

		$serializer = $registry->getSerializerForContract( $contract );

		$this->assertNotInstanceOf( JsonSerializer::class, $serializer );
		$this->assertInstanceOf( PhpSerializer::class, $serializer );
	}

	public function testCanRegisterSameSerializerForDifferentContracts()
	{
		$registry           = new SerializerRegistry();
		$contract           = new Contract( 'Some\\Contract\\One' );
		$different_contract = new Contract( 'Some\\Contract\\Two' );

		$registry->registerSerializerForContract( $contract, new JsonSerializer() );
		$registry->registerSerializerForContract( $different_contract, new JsonSerializer() );

		$first_serializer  = $registry->getSerializerForContract( $contract );
		$second_serializer = $registry->getSerializerForContract( $different_contract );

		$this->assertInstanceOf( get_class( $first_serializer ), $second_serializer );
		$this->assertInstanceOf( get_class( $second_serializer ), $first_serializer );
	}

	public function testIsContractRegistered()
	{
		$registry                = new SerializerRegistry();
		$registered_contract     = new Contract( 'Some\\Registered\\Contract' );
		$not_registered_contract = new Contract( 'Some\\Not\\Registered\\Contract' );

		$registry->registerSerializerForContract( $registered_contract, new JsonSerializer() );

		$this->assertTrue( $registry->isContractRegistered( $registered_contract ) );
		$this->assertFalse( $registry->isContractRegistered( $not_registered_contract ) );
	}

	public function testGetSerializerForContract()
	{
		$registry = new SerializerRegistry();
		$registry->registerSerializerForContract(
			new Contract( 'Some\\Contract' ),
			new JsonSerializer()
		);

		$serializer = $registry->getSerializerForContract( new Contract( 'Some\\Contract' ) );

		$this->assertInstanceOf( JsonSerializer::class, $serializer );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\SerializationContractIsNotRegistered
	 */
	public function testGetSerializerForContractFailsWhenContractIsNotRegistered()
	{
		$registry = new SerializerRegistry();

		$registry->getSerializerForContract( new Contract( 'Some\\Contract' ) );
	}
}
 