<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\RepositoryNotTracked;
use hollodotme\MilestonES\Interfaces\TracksAggregateRootRepositories;
use hollodotme\MilestonES\Interfaces\TracksAggregateRoots;
use hollodotme\MilestonES\Snapshots\Interfaces\TakesSnapshots;

/**
 * Class AggregateRootRepositoryMap
 *
 * @package hollodotme\MilestonES
 */
final class AggregateRootRepositoryMap implements TracksAggregateRootRepositories
{
	/** @var array|TracksAggregateRoots[]|TakesSnapshots[] */
	private $repositories;

	/**
	 * AggregateRootRepositoryMap constructor.
	 */
	public function __construct()
	{
		$this->repositories = [ ];
	}

	/**
	 * @param string               $repositoryFqcn
	 * @param TracksAggregateRoots $repository
	 */
	public function track( $repositoryFqcn, TracksAggregateRoots $repository )
	{
		if ( !$this->isTracked( $repositoryFqcn ) )
		{
			$this->repositories[ $repositoryFqcn ] = $repository;
		}
	}

	/**
	 * @param string $repositoryFqcn
	 *
	 * @return bool
	 */
	public function isTracked( $repositoryFqcn )
	{
		return array_key_exists( $repositoryFqcn, $this->repositories );
	}

	/**
	 * @param string $repositoryFqcn
	 *
	 * @throws RepositoryNotTracked
	 * @return TracksAggregateRoots
	 */
	public function getTracked( $repositoryFqcn )
	{
		if ( $this->isTracked( $repositoryFqcn ) )
		{
			return $this->repositories[ $repositoryFqcn ];
		}
		else
		{
			throw new RepositoryNotTracked( $repositoryFqcn );
		}
	}
}