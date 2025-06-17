<?php

/*
 * Copyright (c) 2005-2024 Jonny Spitzner
 *
 * @license LGPL-3.0+
*/

use Pannorama\PannoramaModel;
use Pannorama\PannoramaHotspotModel;
use Pannorama\PannoramaSceneModel;


$GLOBALS['TL_MODELS']['tl_pannorama'] = PannoramaModel::class;
$GLOBALS['TL_MODELS']['tl_pannorama_hotspot'] = PannoramaHotspotModel::class;
$GLOBALS['TL_MODELS']['tl_pannorama_scene'] = PannoramaSceneModel::class;

/**
 * Back end modules
 */

$GLOBALS['BE_MOD']['pannorama']['pannorama' ] = array
(
		'tables' => array('tl_pannorama', 'tl_pannorama_scene','tl_pannorama_hotspot')
);


/**
 * Style sheet
 */
if (TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'bundles/jonnysppannorama/pannorama.css|static';
}


/**
 * Front end modules
 */
array_insert($GLOBALS['TL_CTE'], 1, array
(
	'includes' => array
	(
		'pannorama_viewer'    => 'PannoramaViewer'
	)
));

/**
 * Back end form fields
 */
//array_insert($GLOBALS['BE_FFL'] ,1, array
//(
//	'pannoramasceneposition'        => 'PannoramaScenePositionSelector',
//	'pannoramahotspotposition'      => 'PannoramaHotspotPositionSelector',
//	'pannoramatargetposition'		=>'PannoramaTargetPositionSelector'
//));

$GLOBALS['BE_FFL']['pannoramasceneposition'] = PannoramaScenePositionSelector::class;
$GLOBALS['BE_FFL']['pannoramahotspotposition'] = PannoramaHotspotPositionSelector::class;
$GLOBALS['BE_FFL']['pannoramatargetposition'] = PannoramaTargetPositionSelector::class;