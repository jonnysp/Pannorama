<?php

/**
 * Table tl_cds
 */
$GLOBALS['TL_DCA']['tl_pannorama'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ctable'                      => array('tl_pannorama_scene'),
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 2,
			'disableGrouping'         => true,
			'panelLayout'             => 'filter;search,limit'
		),
		'label' => array
		(
			'showColumns'			=> true,
			'fields'                => array('title', 'autoLoad','hotSpotDebug')
		),
		
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama']['editheader'],
				'href'                => 'table=tl_pannorama_scene',
				'icon'                => 'system/modules/pannorama/assets/images/pannorama.png'
			),

			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)

		)
	),

	// Palettes
	'palettes' => array
	(
		'default'               => '{title_legend},title,autoLoad,loadButtonLabel,preview,firstScene,sceneFadeDuration;{debug_legend},hotSpotDebug'
	),


	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama']['title'],
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>128, 'tl_class'=>'w100'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),

		'autoLoad' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama']['autoLoad'],
			'inputType'               => 'checkbox',
			'isBoolean'				  => true,
			'eval'                    => array( 'submitOnChange'=>true,'tl_class'=>'w50'),
			'sql'                     => "char(1) NOT NULL default '0'"
		),

		'loadButtonLabel' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama']['loadButtonLabel'],
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default 'Click to Load Panorama'"
		),

		'sceneFadeDuration' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama']['sceneFadeDuration'],
			'default'				  => 2000,
			'inputType'               => 'text',
			'eval'                    => array( 'tl_class'=>'w50','rgxp'=>'natural'),
			'sql'                     => "int(10) NOT NULL default '2000'"
		),

		'firstScene' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama']['firstScene'],
			'search'                  => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_pannorama', 'getScenes'),
			'eval'                    => array( 'submitOnChange'=>true, 'doNotCopy'=>true,'maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),

		'hotSpotDebug' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama']['hotSpotDebug'],
			'inputType'               => 'checkbox',
			'isBoolean'				  => true,
			'eval'                    => array( 'tl_class'=>'w50'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'preview' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama']['preview'],
			'inputType'               => 'fileTree',
			'eval'                    => array( 'tl_class'=>'clr','mandatory'=>false,'fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>$GLOBALS['TL_CONFIG']['validImageTypes']),
			'sql'                     => "binary(16) NULL",
		)

	)
);

//class tl_pannorama extends Backend{
//
//	public function getScenes(DataContainer $dc)
//	{
//		$objScenes =  \PannoramaScene::findByPid($dc->id);
//		$arrScenes = array();
//		if (isset($objScenes)){
//			foreach ($objScenes as $objScene)
//			{
//				$arrScenes[$objScene->id] = $objScene->title;
//			}
//		};
//		return $arrScenes;
//	}
//}