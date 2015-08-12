<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;

/**
 * Class EventStreamIdentifier
 *
 * @package hollodotme\MilestonES
 */
final class EventStreamIdentifier implements IdentifiesEventStream
{

	/** @var IdentifiesObject */
	private $streamId;

	/** @var Contract */
	private $streamIdContract;

	/**
	 * @param IdentifiesObject $streamId
	 */
	public function __construct( IdentifiesObject $streamId )
	{
		$this->streamId         = $streamId;
		$this->streamIdContract = new Contract( get_class( $streamId ) );
	}

	/**
	 * @return IdentifiesObject
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