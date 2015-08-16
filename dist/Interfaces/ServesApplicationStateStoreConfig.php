<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\SerializationStrategy;
use hollodotme\MilestonES\SerializerRegistry;

/**
 * Interface ServesApplicationStateStoreConfig
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface ServesApplicationStateStoreConfig
{
	/**
	 * @return PersistsCommitEnvelopes
	 */
	public function getEventPersistence();

	/**
	 * @return PersistsSnapshots
	 */
	public function getSnapshotPersistence();

	/**
	 * @return ListensForPublishedEvents[]
	 */
	public function getGlobalEventListeners();

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
