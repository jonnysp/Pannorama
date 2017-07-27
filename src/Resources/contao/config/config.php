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
array_insert($GLOBALS['BE_MOD']['content'], 100, array
(
	'pannorama' => array
	(
		'tables' => array('tl_pannorama', 'tl_pannorama_scene','tl_pannorama_hotspot')
	)
));

/**
 * Front end modules
 */
array_insert($GLOBALS['TL_CTE'], 2, array
(
	'includes' => array
	(
		'pannorama_viewer'    => 'PannoramaViewer'
	)
));


