<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;
use hollodotme\MilestonES\Interfaces\PersistsEventEnvelopes;
use hollodotme\MilestonES\Interfaces\ServesEventStoreConfig;
use hollodotme\MilestonES\Persistence\Memory;
use hollodotme\MilestonES\Serializers\PhpSerializer;

/**
 * Class EventStoreConfig
 *
 * @package hollodotme\MilestonES
 */
class EventStoreConfig implements ServesEventStoreConfig
{
	/**
	 * @return PersistsEventEnvelopes
	 */
	public function getPersistenceStrategy()
	{
		return new Memory();
	}

	/**
	 * @return ListensForPublishedEvents[]
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
		return new Contract( PhpSerializer::class );
	}

	/**
	 * @return SerializationStrategy
	 */
	final public function getSerializationStrategy()
	{
		$registry        = $this->getSerializerRegistry();
		$defaultContract = $this->getSerializationDefaultContract();

		return new SerializationStrategy( $registry, $defaultContract );
	}
}
