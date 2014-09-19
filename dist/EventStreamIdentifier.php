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

	/** @var Contract */
	private $stream_id_contract;

	/**
	 * @param Identifies $id
	 */
	public function __construct( Identifies $id )
	{
		$this->stream_id          = $id;
		$this->stream_id_contract = new Contract( get_class( $id ) );
	}

	/**
	 * @return Identifies
	 */
	public function getStreamId()
	{
		return $this->stream_id;
	}

	/**
	 * @return Contract
	 */
	public function getStreamIdContract()
	{
		return $this->stream_id_contract;
	}
}