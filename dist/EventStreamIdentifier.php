<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;

/**
 * Class EventStreamIdentifier
 * @package hollodotme\MilestonES
 */
final class EventStreamIdentifier implements IdentifiesEventStream
{

	/** @var Identifies */
	private $stream_id;

	/** @var Identifies */
	private $stream_type_id;

	/**
	 * @param Identifies $id
	 */
	public function __construct( Identifies $id )
	{
		$this->stream_id      = $id;
		$this->stream_type_id = new ClassNameIdentifier( get_class( $id ) );
	}

	/**
	 * @return Identifies
	 */
	public function getStreamId()
	{
		return $this->stream_id;
	}

	/**
	 * @return Identifies
	 */
	public function getStreamTypeId()
	{
		return $this->stream_type_id;
	}
}