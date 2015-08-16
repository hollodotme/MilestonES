<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;
use hollodotme\MilestonES\Interfaces\PersistsCommitEnvelopes;
use hollodotme\MilestonES\Interfaces\PersistsSnapshots;
use hollodotme\MilestonES\Interfaces\ServesApplicationStateStoreConfig;
use hollodotme\MilestonES\Persistence\Memory;
use hollodotme\MilestonES\Serializers\PhpSerializer;

/**
 * Class ApplicationStateStoreConfig
 *
 * @package hollodotme\MilestonES
 */
class ApplicationStateStoreConfig implements ServesApplicationStateStoreConfig
{
	/**
	 * @return PersistsCommitEnvelopes
	 */
	public function getEventPersistence()
	{
		return new Memory();
	}

	/**
	 * @return PersistsSnapshots
	 */
	public function getSnapshotPersistence()
	{
	}

	/**
	 * @return ListensForPublishedEvents[]
	 */
	public function getGlobalEventListeners()
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
