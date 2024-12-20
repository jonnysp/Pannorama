<?php

use Contao\DC_Table;
use Contao\DataContainer;
use Contao\Backend;
use Contao\System;
use Contao\FilesModel;
use Contao\Image;
use Contao\Image\ResizeConfiguration;

use Pannorama\Model\PannoramaHotspotModel;


/**
 * Table tl_recipes
 */
$GLOBALS['TL_DCA']['tl_pannorama_scene'] = array
(

// Config
	'config' => array
	(
		'dataContainer'               => DC_Table::class,
		'ptable'                      => 'tl_pannorama',
		'ctable'                      => array('tl_pannorama_hotspot'),
		'enableVersioning'            => true,
		'markAsCopy'                 => 'title',
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
			'headerFields'            => array('title','autoLoad','firstScene','sceneFadeDuration','hotSpotDebug'),
			'disableGrouping'         => true,
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'   => array('tl_pannorama_scene', 'generateReferenzRow')
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
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['editscene'],
				'href'                => 'table=tl_pannorama_hotspot',
				'icon'                => 'bundles/jonnysppannorama/images/hotspot.svg'
			),

			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.svg'
				
			),
			

			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.svg'
			),

			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.svg'
			),

			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),

			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'    => array('type','showTitle','compass','autoRotateOn'),
		'default'         => '{title_legend},type,title,showTitle;{controls_legend:hide},showZoomCtrl,showFullscreenCtrl;{zoom_legend:hide},keyboardZoom,doubleClickZoom,mouseZoom,minHfov,maxHfov;{compass_legend:hide},compass;{rotate_legend:hide},autoRotateOn,draggable;{scene_legend},panorama;position;',
		'equirectangular' => '{title_legend},type,title,showTitle;{controls_legend:hide},showZoomCtrl,showFullscreenCtrl;{zoom_legend:hide},keyboardZoom,doubleClickZoom,mouseZoom,minHfov,maxHfov;{compass_legend:hide},compass;{rotate_legend:hide},autoRotateOn,draggable;{scene_legend},panorama;position;',
		'cubemap_single'  => '{title_legend},type,title,showTitle;{controls_legend:hide},showZoomCtrl,showFullscreenCtrl;{zoom_legend:hide},keyboardZoom,doubleClickZoom,mouseZoom,minHfov,maxHfov;{compass_legend:hide},compass;{rotate_legend:hide},autoRotateOn,draggable;{scene_legend},panorama;position;',
		'cubemap_multi'   => '{title_legend},type,title,showTitle;{controls_legend:hide},showZoomCtrl,showFullscreenCtrl;{zoom_legend:hide},keyboardZoom,doubleClickZoom,mouseZoom,minHfov,maxHfov;{compass_legend:hide},compass;{rotate_legend:hide},autoRotateOn,draggable;{scene_legend},panoramafront,panoramaright,panoramaback,panoramaleft,panoramaup,panoramadown;position;'
	),

    // Subpalettes
    'subpalettes' => array (
        'compass'            => 'northOffset',
        'autoRotateOn'		 => 'autoRotate,autoRotateInactivityDelay',
        'showTitle'			 => 'author'
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
			'label'     => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['type'],
			'inputType' => 'select',
			'default'   => 'equirectangular',
			'sorting'   => true,
			'flag'      => 1,
			'options'   => array('equirectangular','cubemap_single', 'cubemap_multi'),
			'reference' => &$GLOBALS['TL_LANG']['tl_pannorama_scene'],
			'eval'      => array('includeBlankOption' => false,'submitOnChange' => true,'mandatory' => true,'tl_class'=> 'w50'),
			'sql'       => "varchar(128) NOT NULL default 'equirectangular'"
		),
		'showTitle' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['showTitle'],
			'inputType'               => 'checkbox',
			'isBoolean'				  => true,
			'eval'                    => array('submitOnChange' => true,'tl_class'=>'w50'),
			'sql'                     => array('type' => 'boolean', 'default' => false)
		),
		'title' => array
		(
			'label'    				=> &$GLOBALS['TL_LANG']['tl_pannorama_scene']['title'],
			'search'              	=> true,
			'inputType'          	=> 'text',
			'eval'                  => array('mandatory'=>true, 'maxlength'=>128, 'tl_class'=>'w50','allowHtml'=>true,'preserveTags'=>true),
			'sql'            		=> "varchar(256) NOT NULL default ''"
		),
		'author' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['author'],
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array( 'maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),

		'keyboardZoom' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['keyboardZoom'],
			'default'				  => true,
			'inputType'               => 'checkbox',
			'isBoolean'				  => true,
			'eval'                    => array( 'submitOnChange' => true,'tl_class'=>'w100'),
			'sql'                     => array('type' => 'boolean', 'default' => true)
		),

		'doubleClickZoom' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['doubleClickZoom'],
			'default'				  => true,
			'inputType'               => 'checkbox',
			'isBoolean'				  => true,
			'eval'                    => array( 'submitOnChange' => true,'tl_class'=>'w100'),
			'sql'                     => array('type' => 'boolean', 'default' => true)
		),


		'mouseZoom' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['mouseZoom'],
			'inputType'               => 'select',
			'options'                 => array('true','false','fullscreenonly'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_pannorama_scene'],
			'eval'                    => array( 'tl_class'=>'w100'),
			'sql'                     => "varchar(128) NOT NULL default 'true'"
		),
		'showZoomCtrl' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['showZoomCtrl'],
			'inputType'               => 'checkbox',
			'isBoolean'				  => true,
			'eval'                    => array( 'tl_class'=>'w50'),
			'sql'                     => array('type' => 'boolean', 'default' => true)
		),
		'showFullscreenCtrl' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['showFullscreenCtrl'],
			'inputType'               => 'checkbox',
			'isBoolean'				  => true,
			'eval'                    => array( 'tl_class'=>'w50'),
			'sql'                     => array('type' => 'boolean', 'default' => true)
		),
		'draggable' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['draggable'],
			'inputType'               => 'checkbox',
			'isBoolean'				  => true,
			'eval'                    => array( 'tl_class'=>'w50'),
			'sql'                     => array('type' => 'boolean', 'default' => true)
		),

		'panorama' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['panorama'],
			'inputType'               => 'fileTree',
			'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'%contao.image.valid_extensions%'),
			'sql'                     => "binary(16) NULL",
		),
		'panoramafront' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['panoramafront'],
			'inputType'               => 'fileTree',
			'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'%contao.image.valid_extensions%'),
			'sql'                     => "binary(16) NULL",
		),
		'panoramaright' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['panoramaright'],
			'inputType'               => 'fileTree',
			'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'%contao.image.valid_extensions%'),
			'sql'                     => "binary(16) NULL",
		),
		'panoramaback' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['panoramaback'],
			'inputType'               => 'fileTree',
			'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'%contao.image.valid_extensions%'),
			'sql'                     => "binary(16) NULL",
		),
		'panoramaleft' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['panoramaleft'],
			'inputType'               => 'fileTree',
			'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'%contao.image.valid_extensions%'),
			'sql'                     => "binary(16) NULL",
		),
		'panoramaup' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['panoramaup'],
			'inputType'               => 'fileTree',
			'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'%contao.image.valid_extensions%'),
			'sql'                     => "binary(16) NULL",
		),
		'panoramadown' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['panoramadown'],
			'inputType'               => 'fileTree',
			'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'%contao.image.valid_extensions%'),
			'sql'                     => "binary(16) NULL",
		),
		'compass' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['compass'],
			'inputType'               => 'checkbox',
			'isBoolean'				  => true,
			'eval'                    => array('submitOnChange' => true, 'tl_class'=>'w50'),
			'sql'                     => "char(1) NOT NULL default '0'"
		),
		'northOffset' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['northOffset'],
			'default'				  => 0,
			'inputType'               => 'text',
			'eval'                    => array( 'tl_class'=>'w50','rgxp'=>'digit','maxval'=>360,'minval' => 0),
			'sql'                     => "varchar(128) NOT NULL default '0'"
		),
		'position' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['position'],
			'inputType'               => 'pannoramasceneposition',
			'eval'                    => array( 'tl_class'=>'clr', 'nospace'=>false),
			'sql'                     => "varchar(128) NOT NULL default 'a:3:{i:0;s:1:\"0\";i:1;s:1:\"0\";i:2;s:3:\"120\";}'"
		),

		'autoRotateOn' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['autoRotateOn'],
			'inputType'               => 'checkbox',
			'isBoolean'				  => true,
			'eval'                    => array( 'submitOnChange' => true,'tl_class'=>'w100'),
			'sql'                     => array('type' => 'boolean', 'default' => false)
		),
		'autoRotate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['autoRotate'],
			'default'				  => -3,
			'inputType'               => 'text',
			'eval'                    => array( 'tl_class'=>'w50','rgxp'=>'digit','maxval'=>10,'minval' => -10),
			'sql'                     => "int(128) NOT NULL default '-3'"
		),

		'autoRotateInactivityDelay' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['autoRotateInactivityDelay'],
			'default'				  => 0,
			'inputType'               => 'text',
			'eval'                    => array( 'tl_class'=>'w50','rgxp'=>'natural'),
			'sql'                     => "int(128) unsigned NOT NULL default '5000'"
		),

		'minHfov' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['minHfov'],
			'default'				  => 50,
			'inputType'               => 'text',
			'eval'                    => array( 'tl_class'=>'w50','rgxp'=>'natural','maxval'=>120,'minval' => 50),
			'sql'                     => "int(128) unsigned NOT NULL default '50'"
		),

		'maxHfov' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pannorama_scene']['maxHfov'],
			'default'				  => 120,
			'inputType'               => 'text',
			'eval'                    => array( 'tl_class'=>'w50','rgxp'=>'natural','maxval'=>120,'minval' => 50),
			'sql'                     => "int(128) unsigned NOT NULL default '120'"
		)


	)
);




class tl_pannorama_scene extends Backend
{
	

	public function generateReferenzRow($arrRow)	
	{

		$this->loadLanguageFile('tl_pannorama_scene');

		$container = System::getContainer();
		$rootDir = $container->getParameter('kernel.project_dir');

		$label = '<table style="margin-left:215px;" class="tl_header_table">
                  <tr><th><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_pannorama_scene']['title'][0].':</span></th><th>'.$arrRow['title']. '</th></tr>
                  <tr><th><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_pannorama_scene']['type'][0].':</span></th><td>'.$GLOBALS['TL_LANG']['tl_pannorama_scene'][$arrRow['type']][0]. '</td></tr>	
                  <tr><th><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_pannorama_scene']['showZoomCtrl'][0].':</span></th><td>'. ($arrRow['showZoomCtrl'] == 1 ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no']) . '</td></tr>
				  <tr><th><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_pannorama_scene']['showFullscreenCtrl'][0].':</span></th><td>'. ($arrRow['showFullscreenCtrl'] == 1 ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no']) . '</td></tr>
                  <tr><th><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_pannorama_scene']['autoRotateOn'][0].':</span></th><td>'. ($arrRow['autoRotateOn'] == 1 ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no']) . '</td></tr>
                  <tr><th><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_pannorama_scene']['compass'][0].':</span></th><td>'. ($arrRow['compass'] == 1 ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no']) . '</td></tr>
                  <tr><th><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_pannorama_scene']['hotspots'].'</span></th><td>'.PannoramaHotspotModel::countBy('pid', $arrRow['id']).'</td></tr>
                  </table>';

		switch ($arrRow['type']) {

		    case 'equirectangular':
				$imagefile = FilesModel::findByUuid($arrRow['panorama']);
				if ($imagefile !== null && file_exists($rootDir . '/' . $imagefile->path))
				{
					$label = Image::getHtml(System::getContainer()->get('contao.image.factory')->create($rootDir . '/' . $imagefile->path, (new ResizeConfiguration())->setWidth(200)->setHeight(100)->setMode(ResizeConfiguration::MODE_BOX)->setZoomLevel(100))->getUrl($rootDir), '', 'style="float:left;"') . $label;
				}
				break;

		    case 'cubemap_single':

				$imagefile = FilesModel::findByUuid($arrRow['panorama']);
				if ($imagefile !== null && file_exists($rootDir . '/' . $imagefile->path))
				{
					$label = Image::getHtml(System::getContainer()->get('contao.image.factory')->create($rootDir . '/' . $imagefile->path, (new ResizeConfiguration())->setWidth(200)->setHeight(150)->setMode(ResizeConfiguration::MODE_BOX)->setZoomLevel(100))->getUrl($rootDir), '', 'style="float:left;"'). $label;
				}
				break;

		    case 'cubemap_multi':

				$resizeconfig =	(new ResizeConfiguration())->setWidth(50)->setHeight(50)->setMode(ResizeConfiguration::MODE_BOX)->setZoomLevel(100);

				$imagepaup = FilesModel::findByUuid($arrRow['panoramaup']);
				$imageleft = FilesModel::findByUuid($arrRow['panoramaleft']);
				$imagefront = FilesModel::findByUuid($arrRow['panoramafront']);
				$imageright = FilesModel::findByUuid($arrRow['panoramaright']);
				$imageback = FilesModel::findByUuid($arrRow['panoramaback']);
				$imagedown = FilesModel::findByUuid($arrRow['panoramadown']);

		        $label = '<table border="0" style="float:left;height:150px;width:200px;">
					<tr>
					  <td style="font-size:0px;padding:0px">&nbsp;</td>
					  <td style="font-size:0px;padding:0px">'. (file_exists($rootDir . '/' . $imagepaup->path) == true ? Image::getHtml(System::getContainer()->get('contao.image.factory')->create($rootDir . '/' . $imagepaup->path, $resizeconfig)->getUrl($rootDir), '', '') : '' ) . '</td>
					  <td style="font-size:0px;padding:0px">&nbsp;</td>
					  <td style="font-size:0px;padding:0px">&nbsp;</td>
					</tr>
					<tr>
					  <td style="font-size:0px;padding:0px">'. (file_exists($rootDir . '/' . $imageleft->path) == true ? Image::getHtml(System::getContainer()->get('contao.image.factory')->create($rootDir . '/' . $imageleft->path, $resizeconfig)->getUrl($rootDir), '', '') : '' ) . '</td>
					  <td style="font-size:0px;padding:0px">'. (file_exists($rootDir . '/' . $imagefront->path) == true ? Image::getHtml(System::getContainer()->get('contao.image.factory')->create($rootDir . '/' . $imagefront->path, $resizeconfig)->getUrl($rootDir), '', '') : '' ) . '</td>
					  <td style="font-size:0px;padding:0px">'. (file_exists($rootDir . '/' . $imageright->path) == true ? Image::getHtml(System::getContainer()->get('contao.image.factory')->create($rootDir . '/' . $imageright->path, $resizeconfig)->getUrl($rootDir), '', '') : '' ) . '</td>
					  <td style="font-size:0px;padding:0px">'. (file_exists($rootDir . '/' . $imageback->path) == true ? Image::getHtml(System::getContainer()->get('contao.image.factory')->create($rootDir . '/' . $imageback->path, $resizeconfig)->getUrl($rootDir), '', '') : '' ) . '</td>
					</tr>
					<tr>
					  <td style="font-size:0px;padding:0px">&nbsp;</td>
					  <td style="font-size:0px;padding:0px">'. (file_exists($rootDir . '/' . $imagedown->path) == true ? Image::getHtml(System::getContainer()->get('contao.image.factory')->create($rootDir . '/' . $imagedown->path, $resizeconfig)->getUrl($rootDir), '', '') : '' ) . '</td>
					  <td style="font-size:0px;padding:0px">&nbsp;</td>
					  <td style="font-size:0px;padding:0px">&nbsp;</td>
					</tr>
					</table>'.' '.$label;
		        
		        break;

		}
		return $label;

    }

}
