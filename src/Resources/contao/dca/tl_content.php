<?php

$GLOBALS['TL_DCA']['tl_content']['palettes']['pannorama_viewer'] = '{type_legend},type;{pannorama_legend},pannoramaviewer;{protected_legend:hide},protected;{expert_legend:hide},cssID,space;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['pannoramaviewer'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['pannorama_viewer'],
	'inputType'               => 'select',
	//'options_callback'        => array('DataContainerPannorama', 'getPannorama'),
	'eval'                    => array('mandatory'=>true, 'chosen'=>true, 'submitOnChange'=>true),
	'wizard' => array
	(
				//array('DataContainerPannorama', 'editPannorama')
	),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"

);
