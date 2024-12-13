<?php
use Contao\System;
use Pannorama\Model\PannoramaModel;
use Contao\Backend;
use Contao\DataContainer;
use Contao\StringUtil;
use Contao\Image;

$GLOBALS['TL_DCA']['tl_content']['palettes']['pannorama_viewer'] = '{type_legend},type;{pannorama_legend},pannoramaviewer;{protected_legend:hide},protected;{expert_legend:hide},cssID,space;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['pannoramaviewer'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['pannorama_viewer'],
	'inputType'               => 'select',
	'options_callback'        => array('tl_content_pannorama', 'getPannorama'),
	'eval'                    => array('mandatory'=>true, 'chosen'=>true, 'submitOnChange'=>true),
	'wizard' 				  => array(array('tl_content_pannorama', 'editPannorama')),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

class tl_content_pannorama extends Backend 
{

	public function getPannorama(): array
	{
		$objPannos =  PannoramaModel::findAll();
		$arrPannos = array();
		if (isset($objPannos)) {
			foreach ($objPannos as $objPanno)
			{
				$arrPannos[$objPanno->id] =  $objPanno->title .' (ID ' . $objPanno->id . ')';
			}
		}
		return $arrPannos;
	}

	public function editPannorama(DataContainer $dc): string
	{

		$this->loadLanguageFile('tl_pannorama');

		$title = sprintf($GLOBALS['TL_LANG']['tl_pannorama']['edit'][1], $dc->value);
		$href = System::getContainer()->get('router')->generate('contao_backend', array('do'=>'pannorama', 'table'=>'tl_pannorama','act'=>'edit', 'id'=>$dc->value , 'popup'=>'1', 'nb'=>'1'));

		return ' <a href="' . StringUtil::specialcharsUrl($href) . '" title="' . StringUtil::specialchars($title) . '" onclick="Backend.openModalIframe({\'title\':\'' . StringUtil::specialchars(str_replace("'", "\\'", $title)) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.svg', $title) . '</a>';

	}


}
