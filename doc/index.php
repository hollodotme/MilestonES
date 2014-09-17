<?php
/**
 * Documentation index
 *
 * @author h.woltersdorf
 */

require_once __DIR__ . '/../vendor/autoload.php';

use hollodotme\TreeMDown\TreeMDown;

$treemdown = new TreeMDown( __DIR__ . '/MilestonES' );
$treemdown->setCompanyName( 'hollodotme' );
$treemdown->setProjectName( 'MilestonES' );
$treemdown->setShortDescription( 'Milestone enabled event store' );
$treemdown->display();
