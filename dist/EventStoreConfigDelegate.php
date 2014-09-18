<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;
use hollodotme\MilestonES\Interfaces\PersistsEventEnvelopes;
use hollodotme\MilestonES\Interfaces\ServesEventStoreConfiguration;
use hollodotme\MilestonES\Persistence\Memory;
use hollodotme\MilestonES\Serializers\JsonSerializer;
use hollodotme\MilestonES\Serializers\PhpSerializer;

/**
 * Class EventStoreConfigDelegate
 *
 * @package hollodotme\MilestonES
 */
class EventStoreConfigDelegate implements ServesEventStoreConfiguration
{
	/**
	 * @return PersistsEventEnvelopes
	 */
	public function getPersistanceStrategy()
	{
		return new Memory();
	}

	/**
	 * @return ObservesCommitedEvents[]
	 */
	public function getGlobalObserversForCommitedEvents()
	{
		return [ ];
	}

	/**
	 * @return SerializerRegistry
	 */
	public function getSerializerRegistry()
	{
		$registry = new SerializerRegistry();

		$registry->registerSerializerForContract(
			new Contract( JsonSerializer::class ),
			new JsonSerializer()
		);

		$registry->registerSerializerForContract(
			new Contract( PhpSerializer::class ),
			new PhpSerializer()
		);

		return $registry;
	}

	/**
	 * @return Contract
	 */
	public function getSerializationDefaultContract()
	{
		return new Contract( JsonSerializer::class );
	}

	/**
	 * @return SerializationStrategy
	 */
	final public function getSerializationStrategy()
	{
		$registry         = $this->getSerializerRegistry();
		$default_contract = $this->getSerializationDefaultContract();

		return new SerializationStrategy( $registry, $default_contract );
	}
}
