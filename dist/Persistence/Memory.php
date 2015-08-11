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
	protected $recordsInTransaction;

	/** @var WrapsEventForCommit[] */
	protected $recordsCommited;

	/** @var bool */
	protected $isInTransaction;

	public function __construct()
	{
		$this->isInTransaction      = false;
		$this->recordsCommited      = [ ];
		$this->recordsInTransaction = [ ];
	}

	public function beginTransaction()
	{
		$this->guardIsNotInTransaction();

		$this->startTransaction();

		$this->recordsInTransaction = [ ];
	}

	public function commitTransaction()
	{
		$this->guardIsInTransaction();

		$this->recordsCommited      = array_merge_recursive( $this->recordsCommited, $this->recordsInTransaction );
		$this->recordsInTransaction = [ ];

		$this->endTransaction();
	}

	public function rollbackTransaction()
	{
		$this->guardIsInTransaction();

		$this->recordsInTransaction = [ ];

		$this->endTransaction();
	}

	/**
	 * @return bool
	 */
	public function isInTransaction()
	{
		return $this->isInTransaction;
	}

	/**
	 * @param WrapsEventForCommit $commitEnvelope
	 */
	public function persistEventEnvelope( WrapsEventForCommit $commitEnvelope )
	{
		$this->guardIsInTransaction();

		$key = $this->buildKey( $commitEnvelope->getStreamIdContract(), $commitEnvelope->getStreamId() );

		if ( !empty($commitEnvelope->getFile()) )
		{
			$fileContent = $this->getFileContent( $commitEnvelope->getFile() );
		}
		else
		{
			$fileContent = null;
		}

		$this->recordsInTransaction[ $key ][] = [
			'envelope'    => clone $commitEnvelope,
			'fileContent' => $fileContent
		];
	}

	/**
	 * @param string $file
	 *
	 * @return string
	 */
	protected function getFileContent( $file )
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
	protected function getCommitedRecordsForKey( $key )
	{
		$records = [ ];

		foreach ( $this->recordsCommited[ $key ] as $record )
		{
			/** @var WrapsEventForCommit $envelope */
			$envelope = $record['envelope'];
			if ( isset($record['fileContent']) && !is_null( $record['fileContent'] ) )
			{
				$file = $this->restoreFileWithContent( $record['fileContent'] );
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
	protected function restoreFileWithContent( $content )
	{
		$filepath = $this->getRestoreFilePath();

		if ( @file_put_contents( $filepath, $content ) !== false )
		{
			return $filepath;
		}
		else
		{
			throw new RestoringFileWithContentFailed();
		}
	}

	/**
	 * @return string
	 */
	protected function getRestoreFilePath()
	{
		return tempnam( '/tmp', 'MilestonES_File' );
	}

	/**
	 * @param string $streamType
	 * @param string $streamId
	 *
	 * @return string
	 */
	protected function buildKey( $streamType, $streamId )
	{
		return $streamType . '#' . $streamId;
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	protected function eventStreamExistsForKey( $key )
	{
		return array_key_exists( $key, $this->recordsCommited );
	}

	/**
	 * @throws PersistenceHasStartedTransactionAlready
	 */
	protected function guardIsNotInTransaction()
	{
		if ( $this->isInTransaction() )
		{
			throw new PersistenceHasStartedTransactionAlready();
		}
	}

	/**
	 * @throws PersistenceHasNoTransactionStarted
	 */
	protected function guardIsInTransaction()
	{
		if ( !$this->isInTransaction() )
		{
			throw new PersistenceHasNoTransactionStarted();
		}
	}

	protected function startTransaction()
	{
		$this->isInTransaction = true;
	}

	protected function endTransaction()
	{
		$this->isInTransaction = false;
	}
}
