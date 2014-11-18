<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Serialization;

use hollodotme\MilestonES\Contract;
use hollodotme\MilestonES\SerializationStrategy;
use hollodotme\MilestonES\SerializerRegistry;
use hollodotme\MilestonES\Serializers\PhpSerializer;

class SerializationStrategyTest extends \PHPUnit_Framework_TestCase
{

	/** @var SerializerRegistry */
	private $registry;

	public function setUp()
	{
		$this->registry = new SerializerRegistry();
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\DefaultSerializationContractIsNotRegistered
	 */
	public function testConstructionFailsWhenDefaultContractIsNotRegistered()
	{
		$this->registry->registerSerializerForContract( new Contract( PhpSerializer::class ), new PhpSerializer() );

		new SerializationStrategy( $this->registry, new Contract( 'Some\\Contract' ) );
	}

	public function testGetDefaultSerializer()
	{
		$default_serializer = new PhpSerializer();

		$this->registry->registerSerializerForContract( new Contract( PhpSerializer::class ), new PhpSerializer() );
		$this->registry->registerSerializerForContract( new Contract( PhpSerializer::class ), $default_serializer );

		$strategy = new SerializationStrategy( $this->registry, new Contract( PhpSerializer::class ) );

		$this->assertSame( $default_serializer, $strategy->getDefaultSerializer() );
	}

	public function testGetDefaultContract()
	{
		$default_contract = new Contract( PhpSerializer::class );

		$this->registry->registerSerializerForContract( new Contract( PhpSerializer::class ), new PhpSerializer() );
		$this->registry->registerSerializerForContract( new Contract( PhpSerializer::class ), new PhpSerializer() );

		$strategy = new SerializationStrategy( $this->registry, $default_contract );

		$this->assertSame( $default_contract, $strategy->getDefaultContract() );
	}

	public function testGetSerializerForContract()
	{
		$json_contract = new Contract( PhpSerializer::class );
		$php_contract  = new Contract( PhpSerializer::class );

		$this->registry->registerSerializerForContract( $json_contract, new PhpSerializer() );
		$this->registry->registerSerializerForContract( $php_contract, new PhpSerializer() );

		$strategy = new SerializationStrategy( $this->registry, $php_contract );

		$this->assertInstanceOf( PhpSerializer::class, $strategy->getSerializerForContract( $json_contract ) );
		$this->assertInstanceOf( PhpSerializer::class, $strategy->getSerializerForContract( $php_contract ) );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\SerializationContractIsNotRegistered
	 */
	public function testGetSerializerForContractFailsWhenContractIsNotRegistered()
	{
		$php_contract = new Contract( PhpSerializer::class );
		$this->registry->registerSerializerForContract( $php_contract, new PhpSerializer() );
		$strategy = new SerializationStrategy( $this->registry, $php_contract );

		$strategy->getSerializerForContract( new Contract( 'Some\\Not\\Registered\\Contract' ) );
	}
}
 