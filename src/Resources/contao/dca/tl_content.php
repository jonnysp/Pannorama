<?php

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

	public function getPannorama()
	{
		$objCats =  \PannoramaModel::findAll();
		$arrCats = array();
		if (isset($objCats)) {
			foreach ($objCats as $objCat)
			{
				$arrCats[$objCat->id] =  $objCat->title .' (ID ' . $objCat->id . ')';
			}
		}
		return $arrCats;
	}

	public function editPannorama(DataContainer $dc)
	{
		$this->loadLanguageFile('tl_pannorama');
		return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=pannorama&amp;act=edit&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="' . sprintf(StringUtil::specialchars($GLOBALS['TL_LANG']['tl_pannorama']['edit'][1]), $dc->value) . '" onclick="Backend.openModalIframe({\'title\':\'' . StringUtil::specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_pannorama']['edit'][1], $dc->value))) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.svg', $GLOBALS['TL_LANG']['tl_pannorama']['edit'][0]) . '</a>';
	}


}
