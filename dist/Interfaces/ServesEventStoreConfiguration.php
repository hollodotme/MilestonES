<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\SerializationStrategy;
use hollodotme\MilestonES\SerializerRegistry;

/**
 * Interface ServesEventStoreConfiguration
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface ServesEventStoreConfiguration
{
	/**
	 * @return PersistsEventEnvelopes
	 */
	public function getPersistanceStrategy();

	/**
	 * @return ObservesCommitedEvents[]
	 */
	public function getGlobalObserversForCommitedEvents();

	/**
	 * @return SerializerRegistry
	 */
	public function getSerializerRegistry();

	/**
	 * @return Identifies
	 */
	public function getSerializationDefaultContract();

	/**
	 * @return SerializationStrategy
	 */
	public function getSerializationStrategy();
}
