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
 *
 * @package hollodotme\MilestonES
 */
final class EventStreamIdentifier implements IdentifiesEventStream
{

	/** @var Identifies */
	private $streamId;

	/** @var Contract */
	private $streamIdContract;

	/**
	 * @param Identifies $streamId
	 */
	public function __construct( Identifies $streamId )
	{
		$this->streamId         = $streamId;
		$this->streamIdContract = new Contract( get_class( $streamId ) );
	}

	/**
	 * @return Identifies
	 */
	public function getStreamId()
	{
		return $this->streamId;
	}

	/**
	 * @return Contract
	 */
	public function getStreamIdContract()
	{
		return $this->streamIdContract;
	}
}