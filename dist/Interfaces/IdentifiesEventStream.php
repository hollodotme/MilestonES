<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface IdentifiesEventStream
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface IdentifiesEventStream
{
	/**
	 * @return IdentifiesAggregateRoot
	 */
	public function getAggregateRootId();

	/**
	 * @return IdentifiesAggregateType
	 */
	public function getAggregateTypeId();
}
