<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\Interfaces\WrapsEventForCommit;
use hollodotme\MilestonES\Persistence\Memory;

/**
 * Class TestMemoryPersistenceWithObjectStorage
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestMemoryPersistenceWithObjectStorage extends Memory
{
	/**
	 * @param string $key
	 *
	 * @return WrapsEventForCommit[]
	 */
	protected function getCommitedRecordsForKey( $key )
	{
		$records = new \SplObjectStorage();

		foreach ( $this->records_commited[ $key ] as $record )
		{
			/** @var WrapsEventForCommit $envelope */
			$envelope = $record['envelope'];
			if ( isset($record['file_content']) && !is_null( $record['file_content'] ) )
			{
				$file = $this->restoreFileWithContent( $record['file_content'] );
				$envelope->setFile( $file );
			}

			$records->attach( $envelope );
		}

		return $records;
	}
}
