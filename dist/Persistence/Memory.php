<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Persistence;

use hollodotme\MilestonES\Exceptions\EventStreamDoesNotExistForKey;
use hollodotme\MilestonES\Exceptions\PersistenceHasNoTransactionStarted;
use hollodotme\MilestonES\Exceptions\PersistenceHasStartedTransactionAlready;
use hollodotme\MilestonES\Exceptions\RestoringFileWithContentFailed;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\PersistsEventEnvelopes;
use hollodotme\MilestonES\Interfaces\WrapsEventForCommit;

/**
 * Class Memory
 *
 * @package hollodotme\MilestonES\Persistence
 */
class Memory implements PersistsEventEnvelopes
{

	/** @var WrapsEventForCommit[] */
	private $records_in_transaction;

	/** @var WrapsEventForCommit[] */
	private $records_commited;

	/** @var bool */
	private $is_in_transaction;

	public function __construct()
	{
		$this->is_in_transaction      = false;
		$this->records_commited = [ ];
		$this->records_in_transaction = [ ];
	}

	public function beginTransaction()
	{
		$this->guardIsNotInTransaction();

		$this->startTransaction();

		$this->records_in_transaction = [ ];
	}

	public function commitTransaction()
	{
		$this->guardIsInTransaction();

		$this->records_commited       = array_merge_recursive( $this->records_commited, $this->records_in_transaction );
		$this->records_in_transaction = [ ];

		$this->endTransaction();
	}

	public function rollbackTransaction()
	{
		$this->guardIsInTransaction();

		$this->records_in_transaction = [ ];

		$this->endTransaction();
	}

	/**
	 * @return bool
	 */
	public function isInTransaction()
	{
		return $this->is_in_transaction;
	}

	/**
	 * @param WrapsEventForCommit $event_envelope
	 */
	public function persistEventEnvelope( WrapsEventForCommit $event_envelope )
	{
		$this->guardIsInTransaction();

		$key = $this->buildKey( $event_envelope->getStreamIdContract(), $event_envelope->getStreamId() );

		if ( !empty($event_envelope->getFile()) )
		{
			$file_content = $this->getFileContent( $event_envelope->getFile() );
		}
		else
		{
			$file_content = null;
		}

		$this->records_in_transaction[ $key ][] = [
			'envelope'     => clone $event_envelope,
			'file_content' => $file_content
		];
	}

	/**
	 * @param string $file
	 *
	 * @return string
	 */
	private function getFileContent( $file )
	{
		return file_get_contents( $file );
	}

	/**
	 * @param IdentifiesEventStream $id
	 *
	 * @throws EventStreamDoesNotExistForKey
	 * @return WrapsEventForCommit[]
	 */
	public function getEventEnvelopesWithId( IdentifiesEventStream $id )
	{
		$key = $this->buildKey( $id->getStreamIdContract(), $id->getStreamId() );

		if ( $this->eventStreamExistsForKey( $key ) )
		{
			return $this->getCommitedRecordsForKey( $key );
		}
		else
		{
			throw new EventStreamDoesNotExistForKey( $key );
		}
	}

	/**
	 * @param string $key
	 *
	 * @return WrapsEventForCommit[]
	 */
	private function getCommitedRecordsForKey( $key )
	{
		$records = [ ];

		foreach ( $this->records_commited[ $key ] as $record )
		{
			/** @var WrapsEventForCommit $envelope */
			$envelope = $record['envelope'];
			if ( isset($record['file_content']) && !is_null( $record['file_content'] ) )
			{
				$file = $this->restoreFileWithContent( $record['file_content'] );
				$envelope->setFile( $file );
			}

			$records[] = $envelope;
		}

		return $records;
	}

	/**
	 * @param string $content
	 *
	 * @throws RestoringFileWithContentFailed
	 * @return string
	 */
	private function restoreFileWithContent( $content )
	{
		$filepath = tempnam( '/tmp', 'MilestonES_File' );

		if ( file_put_contents( $filepath, $content ) !== false )
		{
			return $filepath;
		}
		else
		{
			throw new RestoringFileWithContentFailed();
		}
	}

	/**
	 * @param string $stream_type
	 * @param string $stream_id
	 *
	 * @return string
	 */
	private function buildKey( $stream_type, $stream_id )
	{
		return $stream_type . '#' . $stream_id;
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	private function eventStreamExistsForKey( $key )
	{
		return array_key_exists( $key, $this->records_commited );
	}

	/**
	 * @throws PersistenceHasStartedTransactionAlready
	 */
	private function guardIsNotInTransaction()
	{
		if ( $this->isInTransaction() )
		{
			throw new PersistenceHasStartedTransactionAlready();
		}
	}

	/**
	 * @throws PersistenceHasNoTransactionStarted
	 */
	private function guardIsInTransaction()
	{
		if ( !$this->isInTransaction() )
		{
			throw new PersistenceHasNoTransactionStarted();
		}
	}

	private function startTransaction()
	{
		$this->is_in_transaction = true;
	}

	private function endTransaction()
	{
		$this->is_in_transaction = false;
	}
}
