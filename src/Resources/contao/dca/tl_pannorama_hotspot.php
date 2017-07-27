<?php

/**
 * Table tl_recipes
 */
$GLOBALS['TL_DCA']['tl_pannorama_hotspot'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_pannorama_scene',
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index'
			)
		)
	),
	

// List
	'list' => array
	(

		 'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('type'),
			'headerFields'            => array('title','type','showZoomCtrl','showFullscreenCtrl','autoRotateOn','compass'),
			'disableGrouping'         => true,
		//	'flag'        			  => 11,
			'panelLayout'             => 'filter;search,limit',
			'child_record_callback'   => array('tl_pannorama_hotspot', 'generateReferenzRow')
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
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif'
			),

			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)

		)
	),

// Palettes
	'palettes' => array
	(
		'__selector__'  => array('type', 'scene', 'info'),
		'default'       => '{title_legend},type,title,cssClass,position;',
		'scene'         => '{title_legend},type,title;{scene_legend},sceneId,cssClass;position,targetposition;',
		'info'  		=> '{title_legend},type,title;{info_legend},url,cssClass;position;',
	),


	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),

		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),

		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),

		'type'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['type'],
			'inputType' => 'select',
			'default'   => 'scene',
			'sorting'   => true,
			'flag'      => 1,
			'options'   => array('scene', 'info'),
			'reference' => &$GLOBALS['TL_LANG']['tl_pannorama_hotspot'],
			'eval'      => array('includeBlankOption' => false,'submitOnChange' => true,'mandatory' => true,'tl_class'=> 'w50'),
			'sql'       => "varchar(128) NOT NULL default 'scene'"
		),

		'title' => array
		(
			'label'    				=> &$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['title'],
			'search'              	=> true,
			'inputType'          	=> 'text',
			'eval'                  => array('mandatory'=>true, 'maxlength'=>128, 'tl_class'=>'w50','allowHtml'=>true,'preserveTags'=>true),
			'sql'            		=> "varchar(256) NOT NULL default ''"
		),
		'cssClass' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['cssClass'],
			'inputType'               => 'text',
			'eval'                    => array( 'maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),

		'sceneId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['sceneId'],
			'inputType'               => 'select',
			//'options_callback'        => array('tl_pannorama_hotspot', 'getScenes'),
			'eval'                    => array('includeBlankOption' => true,'submitOnChange' => true, 'mandatory'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),

		'url' => array
		(
			'label'     			  => &$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['url'],
			'inputType' 			  => 'text',
			'eval'      			  => array('decodeEntities'=>true,'nospace'=>true,'rgxp'=>'url','maxlength'=>255, 'tl_class'=>'w50'),
			'sql'       			  => "varchar(256) NOT NULL default ''"
		),

		'position' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['position'],
			'inputType'               => 'pannoramahotspotposition',
			'eval'                    => array( 'tl_class'=>'clr', 'nospace'=>false),
			'sql'                     => "varchar(128) NOT NULL default 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}'"
		),

		'targetposition' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['targetposition'],
			'inputType'               => 'pannoramatargetposition',
			'eval'                    => array( 'tl_class'=>'clr', 'nospace'=>false),
			'sql'                     => "varchar(128) NOT NULL default 'a:3:{i:0;s:1:\"0\";i:1;s:1:\"0\";i:2;s:3:\"120\";}'"
		)

	)
);



class tl_pannorama_hotspot extends Backend{
	

	public function generateReferenzRow($arrRow)	{
		$this->loadLanguageFile('tl_pannorama_hotspot');

		$thisScene =  \PannoramaSceneModel::findByPk($arrRow['sceneId']);

		$out =  '<table style="margin-left:40px;" class="tl_header_table">
			<tr><th><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['title'][0].':</span></th><th>'.$arrRow['title']. '</th></tr>
			<!--<tr><td><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['type'][0].':</span></td><td>'.$GLOBALS['TL_LANG']['tl_pannorama_hotspot'][$arrRow['type']][0]. '</td></tr>-->
			';	

		if ($arrRow['type'] == 'scene') {
			$out .=	'<tr><td><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['sceneId'][0].':</span></td><td>'.$thisScene->title. '</td></tr>';
		}elseif ($arrRow['type'] == 'info' && $arrRow['url'] <> '') {
			$out .=	'<tr><td><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_pannorama_hotspot']['url'][0].':</span></td><td>'.$arrRow['url']. '</td></tr>';
		}

		
		if ($arrRow['type'] == 'scene') {
			$out = \Image::getHtml(Image::get('system/modules/pannorama/assets/images/hotspot_big.png', 32, 32, 'center'), '', 'style="float:left;"') . ' ' . $out;
		}elseif ($arrRow['type'] == 'info') {
			$out = \Image::getHtml(Image::get('system/modules/pannorama/assets/images/information_big.png', 32, 32, 'center'), 'system/modules/pannorama/assets/images/hotspot.png', 'style="float:left;"') . ' ' . $out;
		}


		return	$out.'</table>';


    }


	public function getScenes(DataContainer $dc)
	{

		$objScenes = \PannoramaSceneModel::findByPid(\PannoramaSceneModel::findByPk(\PannoramaHotspotModel::findByPk($dc->id)->pid)->pid);

		$arrScenes = array();

		if (isset($objScenes)){
			foreach ($objScenes as $objScene)
			{
				$arrScenes[$objScene->id] = $objScene->title;
			}
		};
		return $arrScenes;
	}

}
