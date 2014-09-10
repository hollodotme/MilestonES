<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Adapters\Assert;
use hollodotme\MilestonES\Interfaces\IdentifiesSubject;
use hollodotme\MilestonES\Traits\SubjectIdentifying;

/**
 * Class SubjectIdentifier
 * @package hollodotme\MilestonES
 */
abstract class SubjectIdentifier implements IdentifiesSubject
{
	use SubjectIdentifying;

	/**
	 * @param string $id
	 */
	protected function __construct( $id )
	{
		Assert::that( $id )->notEmpty()->string();
		
		$this->_id = $id;
	}
} 