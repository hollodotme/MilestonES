<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Persistence;

use hollodotme\MilestonES\Exceptions\KeyNotFoundInCommittedRecords;
use hollodotme\MilestonES\Exceptions\PersistenceHasNoTransactionStarted;
use hollodotme\MilestonES\Exceptions\PersistenceHasStartedTransactionAlready;

/**
 * Class MemoryPersistence
 *
 * @package hollodotme\MilestonES\Persistence
 */
abstract class MemoryPersistence
{
	/** @var bool */
	private $isInTransaction;

	/** @var array */
	private $recordsInTransaction;

	/** @var array */
	private $recordsCommitted;

	public function __construct()
	{
		$this->isInTransaction      = false;
		$this->recordsInTransaction = [ ];
		$this->recordsCommitted     = [ ];
	}

	public function beginTransaction()
	{
		$this->guardIsNotInTransaction();

		$this->startTransaction();

		$this->recordsInTransaction = [ ];
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
	 * @return bool
	 */
	public function isInTransaction()
	{
		return $this->isInTransaction;
	}

	public function commitTransaction()
	{
		$this->guardIsInTransaction();

		$this->recordsCommitted     = array_merge_recursive( $this->recordsCommitted, $this->recordsInTransaction );
		$this->recordsInTransaction = [ ];

		$this->endTransaction();
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

	public function rollbackTransaction()
	{
		$this->guardIsInTransaction();

		$this->recordsInTransaction = [ ];

		$this->endTransaction();
	}

	private function startTransaction()
	{
		$this->isInTransaction = true;
	}

	private function endTransaction()
	{
		$this->isInTransaction = false;
	}

	/**
	 * @param string|int $key
	 * @param mixed      $record
	 */
	final protected function addRecordForKey( $key, $record )
	{
		$this->guardIsInTransaction();

		$this->recordsInTransaction[ $key ][] = $record;
	}

	/**
	 * @param string|int $key
	 *
	 * @throws KeyNotFoundInCommittedRecords
	 * @return array
	 */
	final protected function getCommittedRecordsForKey( $key )
	{
		if ( isset($this->recordsCommitted[ $key ]) )
		{
			return $this->recordsCommitted[ $key ];
		}
		else
		{
			throw new KeyNotFoundInCommittedRecords( $key );
		}
	}
}