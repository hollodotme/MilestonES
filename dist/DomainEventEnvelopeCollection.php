<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\ItemDoesNotRepresentADomainEventEnvelope;
use hollodotme\MilestonES\Interfaces\CollectsDomainEventEnvelopes;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;

/**
 * Class DomainEventEnvelopeCollection
 *
 * @package hollodotme\MilestonES
 */
class DomainEventEnvelopeCollection implements Interfaces\CollectsDomainEventEnvelopes
{

	/**
	 * @var WrapsDomainEvent[]
	 */
	protected $envelopes = [ ];

	/**
	 * @param CollectsDomainEventEnvelopes $envelopes
	 */
	public function removeEvents( CollectsDomainEventEnvelopes $envelopes )
	{
		$this->envelopes = array_filter(
			$this->envelopes,
			function ( WrapsDomainEvent $cur_envelope ) use ( $envelopes )
			{
				return !in_array( $cur_envelope, iterator_to_array( $envelopes ), true );
			}
		);
	}

	/**
	 * @param CollectsDomainEventEnvelopes $envelopes
	 */
	public function append( CollectsDomainEventEnvelopes $envelopes )
	{
		foreach ( $envelopes as $key => $envelope )
		{
			$this->offsetSet( null, $envelope );
		}
	}

	/**
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty($this->envelopes);
	}

	/**
	 * @return Interfaces\WrapsDomainEvent
	 */
	public function current()
	{
		return current( $this->envelopes );
	}

	public function next()
	{
		next( $this->envelopes );
	}

	/**
	 * @return int
	 */
	public function key()
	{
		return key( $this->envelopes );
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		return (key( $this->envelopes ) !== null);
	}

	public function rewind()
	{
		reset( $this->envelopes );
	}

	/**
	 * @param int $offset
	 *
	 * @return bool
	 */
	public function offsetExists( $offset )
	{
		return isset($this->envelopes[ $offset ]);
	}

	/**
	 * @param mixed $offset
	 *
	 * @return WrapsDomainEvent
	 */
	public function offsetGet( $offset )
	{
		if ( $this->offsetExists( $offset ) )
		{
			return $this->envelopes[ $offset ];
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param int|null         $offset
	 * @param WrapsDomainEvent $value
	 */
	public function offsetSet( $offset, $value )
	{
		$this->guardType( $value );

		if ( is_null( $offset ) )
		{
			$this->envelopes[] = $value;
		}
		else
		{
			$this->envelopes[ $offset ] = $value;
		}
	}

	/**
	 * @param int $offset
	 */
	public function offsetUnset( $offset )
	{
		unset($this->envelopes[ $offset ]);
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count( $this->envelopes );
	}

	/**
	 * @param mixed $item
	 *
	 * @throws ItemDoesNotRepresentADomainEventEnvelope
	 */
	private function guardType( $item )
	{
		if ( !($item instanceof Interfaces\WrapsDomainEvent) )
		{
			throw new ItemDoesNotRepresentADomainEventEnvelope( gettype( $item ) );
		}
	}

	/**
	 * @param callable $compareFunction
	 */
	public function sort( callable $compareFunction )
	{
		usort( $this->envelopes, $compareFunction );
	}
}
