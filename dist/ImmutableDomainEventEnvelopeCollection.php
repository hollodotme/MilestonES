<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;

/**
 * Class ImmutableDomainEventEnvelopeCollection
 *
 * @package hollodotme\MilestonES
 */
class ImmutableDomainEventEnvelopeCollection extends DomainEventEnvelopeCollection
{
	/**
	 * @param WrapsDomainEvent[] $events
	 *
	 * @throws Exceptions\ItemDoesNotRepresentADomainEventEnvelope
	 */
	public function __construct( array $events )
	{
		foreach ( $events as $event )
		{
			parent::offsetSet( null, $event );
		}
	}

	/**
	 * @param int|null        $offset
	 * @param WrapsDomainEvent $value
	 *
	 * @throws EventCollectionIsImmutable
	 */
	final public function offsetSet( $offset, $value )
	{
		throw new EventCollectionIsImmutable();
	}

	/**
	 * @param int $offset
	 *
	 * @throws EventCollectionIsImmutable
	 */
	final public function offsetUnset( $offset )
	{
		throw new EventCollectionIsImmutable();
	}
}
