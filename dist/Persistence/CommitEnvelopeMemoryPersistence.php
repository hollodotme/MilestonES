<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Persistence;

use hollodotme\MilestonES\Exceptions\EventStreamDoesNotExistForKey;
use hollodotme\MilestonES\Exceptions\KeyNotFoundInCommittedRecords;
use hollodotme\MilestonES\Exceptions\RestoringFileWithContentFailed;
use hollodotme\MilestonES\Interfaces\CarriesCommitData;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\PersistsCommitEnvelopes;

/**
 * Class CommitEnvelopeMemoryPersistence
 *
 * @package hollodotme\MilestonES\Persistence
 */
class CommitEnvelopeMemoryPersistence extends MemoryPersistence implements PersistsCommitEnvelopes
{
	private $fileRestoreDir;

	/**
	 * CommitEnvelopeMemoryPersistence constructor.
	 *
	 * @param $fileRestoreDir
	 */
	public function __construct( $fileRestoreDir )
	{
		parent::__construct();

		$this->fileRestoreDir = $fileRestoreDir;
	}

	/**
	 * @param CarriesCommitData $commitEnvelope
	 */
	public function persistCommitEnvelope( CarriesCommitData $commitEnvelope )
	{
		$key = $this->buildKey( $commitEnvelope->getStreamIdContract(), $commitEnvelope->getStreamId() );

		if ( !empty($commitEnvelope->getFile()) )
		{
			$fileContent = $this->getFileContent( $commitEnvelope->getFile() );
		}
		else
		{
			$fileContent = null;
		}

		$this->addRecordForKey(
			$key,
			[
				'envelope'    => clone $commitEnvelope,
				'fileContent' => $fileContent
			]
		);
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
	 * @param IdentifiesEventStream $eventStreamId
	 * @param int                   $startRevision
	 *
	 * @throws EventStreamDoesNotExistForKey
	 * @return CarriesCommitData[]
	 */
	public function getCommitEnvelopesForStreamId( IdentifiesEventStream $eventStreamId, $startRevision = 0 )
	{
		$key = $this->buildKey( $eventStreamId->getStreamIdContract(), $eventStreamId->getStreamId() );

		try
		{
			return $this->getCommitEnvelopesForKey( $key );
		}
		catch ( KeyNotFoundInCommittedRecords $e )
		{
			throw new EventStreamDoesNotExistForKey( $key, 0, $e );
		}
	}

	/**
	 * @param string $key
	 *
	 * @return CarriesCommitData[]
	 */
	private function getCommitEnvelopesForKey( $key )
	{
		$commitEnvelopes  = [ ];
		$committedRecords = $this->getCommittedRecordsForKey( $key );

		foreach ( $committedRecords as $committedRecord )
		{
			/** @var CarriesCommitData $envelope */
			$envelope = $committedRecord['envelope'];
			if ( isset($committedRecord['fileContent']) && !is_null( $committedRecord['fileContent'] ) )
			{
				$file = $this->restoreFileWithContent( $committedRecord['fileContent'] );
				$envelope->setFile( $file );
			}

			$commitEnvelopes[] = $envelope;
		}

		return $commitEnvelopes;
	}

	/**
	 * @param string $content
	 *
	 * @throws RestoringFileWithContentFailed
	 * @return string
	 */
	private function restoreFileWithContent( $content )
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
	 * @throws RestoringFileWithContentFailed
	 * @return string
	 */
	private function getRestoreFilePath()
	{
		if ( file_exists( $this->fileRestoreDir ) )
		{
			return tempnam( $this->fileRestoreDir, 'MilestonES_File' );
		}
		else
		{
			throw new RestoringFileWithContentFailed();
		}
	}

	/**
	 * @param string $streamType
	 * @param string $streamId
	 *
	 * @return string
	 */
	private function buildKey( $streamType, $streamId )
	{
		return $streamType . '#' . $streamId;
	}
}
