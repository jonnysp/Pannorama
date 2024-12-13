<?php

/*
 * Copyright (c) 2005-2024 Jonny Spitzner
 *
 * @license LGPL-3.0+
*/

use Pannorama\Model\PannoramaModel;
use Pannorama\Model\PannoramaHotspotModel;
use Pannorama\Model\PannoramaSceneModel;

use Contao\ArrayUtil;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;


$GLOBALS['TL_MODELS']['tl_pannorama'] = PannoramaModel::class;
$GLOBALS['TL_MODELS']['tl_pannorama_hotspot'] = PannoramaHotspotModel::class;
$GLOBALS['TL_MODELS']['tl_pannorama_scene'] = PannoramaSceneModel::class;


ArrayUtil::arrayInsert($GLOBALS['BE_MOD']['pannorama'], 100, array
	(
		'pannorama' => array('tables' => array('tl_pannorama', 'tl_pannorama_scene','tl_pannorama_hotspot'))
	)
);

/**
 * Style sheet
 */
if (System::getContainer()->get('contao.routing.scope_matcher')
	->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create(''))
) 
{
	$GLOBALS['TL_CSS'][] = 'bundles/jonnysppannorama/pannorama.css|static';

	$GLOBALS['BE_FFL']['pannoramasceneposition']   = 'PannoramaScenePositionSelector';
	$GLOBALS['BE_FFL']['pannoramahotspotposition'] = 'PannoramaHotspotPositionSelector';
	$GLOBALS['BE_FFL']['pannoramatargetposition']   ='PannoramaTargetPositionSelector';

};


/**
 * Front end modules
 */
ArrayUtil::arrayInsert($GLOBALS['TL_CTE'], 1, array
	(
		'includes' => array
		(
			'pannorama_viewer'    => 'PannoramaViewer'
		)
	)
);
