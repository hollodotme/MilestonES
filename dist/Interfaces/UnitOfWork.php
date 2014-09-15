<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface UnitOfWork
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface UnitOfWork extends CollectsAggregateRoots, CommitsChanges
{

}
