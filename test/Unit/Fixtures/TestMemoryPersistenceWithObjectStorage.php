<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\Interfaces\CarriesCommitData;
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
	 * @return CarriesCommitData[]
	 */
	protected function getCommitedRecordsForKey( $key )
	{
		$records = new \SplObjectStorage();

		foreach ( $this->recordsCommited[ $key ] as $record )
		{
			/** @var CarriesCommitData $envelope */
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
