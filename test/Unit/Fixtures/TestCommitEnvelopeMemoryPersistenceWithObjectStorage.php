<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\Exceptions\EventStreamDoesNotExistForKey;
use hollodotme\MilestonES\Interfaces\CarriesCommitData;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Persistence\CommitEnvelopeMemoryPersistence;

/**
 * Class TestCommitEnvelopeMemoryPersistenceWithObjectStorage
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestCommitEnvelopeMemoryPersistenceWithObjectStorage extends CommitEnvelopeMemoryPersistence
{
	/**
	 * @param IdentifiesEventStream $eventStreamId
	 * @param int                   $startRevision
	 *
	 * @throws EventStreamDoesNotExistForKey
	 * @return CarriesCommitData[]
	 */
	public function getCommitEnvelopesForStreamId( IdentifiesEventStream $eventStreamId, $startRevision = 0 )
	{
		$commitEnvelopes = parent::getCommitEnvelopesForStreamId( $eventStreamId, $startRevision );

		$objectStorage = new \SplObjectStorage();

		foreach ( $commitEnvelopes as $commitEnvelope )
		{
			$objectStorage->attach( $commitEnvelope );
		}

		return $objectStorage;
	}
}
