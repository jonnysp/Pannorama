<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Jonny Spitzner
 *
 * @license LGPL-3.0+
 */

/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD']['pannorama'], 100, array
(
	'pannorama' => array
	(
		'tables' => array('tl_pannorama', 'tl_pannorama_scene','tl_pannorama_hotspot')
	)
));

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
array_insert($GLOBALS['BE_FFL'] ,1, array
(
	'pannoramasceneposition'        => 'PannoramaScenePositionSelector',
	'pannoramahotspotposition'      => 'PannoramaHotspotPositionSelector',
	'pannoramatargetposition'		=>'PannoramaTargetPositionSelector'
));

