<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface TracksAggregateRootRepositories
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface TracksAggregateRootRepositories
{
	/**
	 * @param string               $repositoryFqcn
	 * @param TracksAggregateRoots $repository
	 */
	public function track( $repositoryFqcn, TracksAggregateRoots $repository );

	/**
	 * @param string $repositoryFqcn
	 *
	 * @return bool
	 */
	public function isTracked( $repositoryFqcn );

	/**
	 * @param string $repositoryFqcn
	 *
	 * @return TracksAggregateRoots
	 */
	public function getTracked( $repositoryFqcn );
}