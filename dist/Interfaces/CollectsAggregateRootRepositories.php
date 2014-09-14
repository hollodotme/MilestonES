<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface CollectsAggregateRootRepositories
 *
 * @package Interfaces
 */
interface CollectsAggregateRootRepositories extends IdentityMap, \Iterator, \Countable
{
	public function commitChanges();
}
