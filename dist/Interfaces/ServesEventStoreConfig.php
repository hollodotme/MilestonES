<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\SerializationStrategy;
use hollodotme\MilestonES\SerializerRegistry;

/**
 * Interface ServesEventStoreConfig
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface ServesEventStoreConfig
{
	/**
	 * @return PersistsEventEnvelopes
	 */
	public function getPersistenceStrategy();

	/**
	 * @return ListensForPublishedEvents[]
	 */
	public function getGlobalObserversForCommitedEvents();

	/**
	 * @return SerializerRegistry
	 */
	public function getSerializerRegistry();

	/**
	 * @return IdentifiesObject
	 */
	public function getSerializationDefaultContract();

	/**
	 * @return SerializationStrategy
	 */
	public function getSerializationStrategy();
}
