<?php

class PannoramaTargetPositionSelector extends \Widget
{

	protected $strTemplate = 'be_widget';
	protected $blnSubmitInput = true;	
 
	public function generate()
	{
		$this->loadLanguageFile('tl_pannorama');
		
		$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/jonnysppannorama/pannellum.js';
		$GLOBALS['TL_CSS'][] = 		  'bundles/jonnysppannorama/pannellum.css';
		$GLOBALS['TL_CSS'][] 		= 'bundles/jonnysppannorama/hotspots.css';

		$container = System::getContainer();
		$rootDir = $container->getParameter('kernel.project_dir');

		$selfspot = \PannoramaHotspotModel::findByPk($this->__get('currentRecord'));
		$startscene = \PannoramaSceneModel::findByPk($selfspot->sceneId);

		//config

		switch ($startscene->type) {
		    case 'equirectangular':
				$config['type'] = 'equirectangular';
				$config['panorama'] = \Environment::get('base').\FilesModel::findByPk($startscene->panorama)->path;
		        break;
		    case 'cubemap_single':
		    	$config['type'] = 'cubemap';
				$filemodel = \FilesModel::findByPk($startscene->panorama);
				if (isset($filemodel)){
					$file = new \File($filemodel->path);
					if ($file->isImage && $file->isGdImage){
						$panelsizeheight = $file->imageSize[1] / 3;
						$panelsizewidth = $file->imageSize[0] / 4;
						if ($panelsizeheight == $panelsizewidth){

							$image = new Image($file);
							$image->setTargetWidth($panelsizewidth)->setTargetHeight($panelsizeheight)->setResizeMode('crop')->setZoomLevel(100);

							//front
							$image->setImportantPart(array('x' => $panelsizewidth,'y' => $panelsizeheight,'width' => $panelsizewidth,'height' => $panelsizeheight))->executeResize();
							$cubemap[]=$image->getResizedPath();

							//right
							$image->setImportantPart(array('x' => $panelsizewidth*2,'y' => $panelsizeheight,'width' => $panelsizewidth,'height' => $panelsizeheight))->executeResize();
							$cubemap[]=$image->getResizedPath();

							//back
							$image->setImportantPart(array('x' => $panelsizewidth*3,'y' => $panelsizeheight,'width' => $panelsizewidth,'height' => $panelsizeheight))->executeResize();
							$cubemap[]=$image->getResizedPath();

							//left
							$image->setImportantPart(array('x' => 0,'y' => $panelsizeheight,'width' => $panelsizewidth,'height' => $panelsizeheight))->executeResize();
							$cubemap[]=$image->getResizedPath();

							//up
							$image->setImportantPart(array('x' => $panelsizewidth,'y' => 0,'width' => $panelsizewidth,'height' => $panelsizeheight))->executeResize();
							$cubemap[]=$image->getResizedPath();

							//down
							$image->setImportantPart(array('x' => $panelsizewidth,'y' => $panelsizeheight*2,'width' => $panelsizewidth,'height' => $panelsizeheight))->executeResize();
							$cubemap[]=$image->getResizedPath();

							$config['cubeMap'] = $cubemap;
							unset($cubemap);

						}else{
							echo $GLOBALS['TL_LANG']['tl_pannorama']['notcubemap'];
						}
					}
				}
		        break;
		    case 'cubemap_multi':
				$config['type'] = 'cubemap';
				//panoramafront,panoramaright,panoramaback,panoramaleft,panoramaup,panoramadown
				$config['cubeMap']	= array(
					\Environment::get('base').\FilesModel::findByPk($startscene->panoramafront)->path,
					\Environment::get('base').\FilesModel::findByPk($startscene->panoramaright)->path,
					\Environment::get('base').\FilesModel::findByPk($startscene->panoramaback)->path,
					\Environment::get('base').\FilesModel::findByPk($startscene->panoramaleft)->path,
					\Environment::get('base').\FilesModel::findByPk($startscene->panoramaup)->path,
					\Environment::get('base').\FilesModel::findByPk($startscene->panoramadown)->path
				);
		        break;
		}

		$config['autoLoad'] = true;
    	$config['pitch'] = floatval($this->varValue[0]);
		$config['yaw'] = floatval($this->varValue[1]);
		$config['hfov'] = intval($this->varValue[2]);
    	$config['hotSpotDebug'] = true;
    	$config['compass'] = boolval($startscene->compass);
		$config['northOffset'] =  intval($startscene->northOffset);
		$config['doubleClickZoom'] = boolval($startscene->doubleClickZoom);
		
		if (boolval($startscene->showZoomCtrl) == true || boolval($startscene->showFullscreenCtrl) == true) {
			$config['showControls'] = true;
			$config['showZoomCtrl'] = boolval($startscene->showZoomCtrl);
			$config['showFullscreenCtrl'] = boolval($startscene->showFullscreenCtrl);
		}else{
			$config['showControls'] = false;
		}

		if (boolval($startscene->showTitle) == true){
			if($startscene->title != ''){
				$config['title'] = $startscene->title;
			}
			if($startscene->author != ''){
				$config['author'] = $startscene->author;
			}
		}
		

		if(isset($startscene->id) && !empty($startscene->id)){
			if(\PannoramaHotspotModel::countBy('pid', $startscene->id) > 0){
				foreach (\PannoramaHotspotModel::findByPid($startscene->id) as $hotkey => $hotvalue){
					if ($selfspot <> $hotvalue){
						$tempposition = unserialize($hotvalue->position);
						$hotspot['pitch'] = floatval($tempposition[0]); 
						$hotspot['yaw'] = floatval($tempposition[1]);
						$hotspot['type'] = 'info';
						$hotspot['text'] = $hotvalue->title;
						$hotspot['cssClass'] = $hotvalue->type.'_spot';
						//$hotspot['URL'] = 'contao/main.php?do=Pannorama&table=tl_pannorama_hotspot&act=edit&id='.$hotvalue->id.'&rt='.\RequestToken::get();
						$config['hotSpots'][] = $hotspot;
						unset($hotspot);
					}
				}
			}
		}

		$pannoname = 'pannoramatarget'.$this->__get('currentRecord');
	
		echo '<div class="tl_text" id="'.$pannoname .'_canvas" style="width:auto; height:300px;"></div><br>
			<script type="text/javascript">

			var '.$pannoname.'viewer;

			window.addEvent("domready", function() {
				'.$pannoname.'initialize();
			});

			function '.$pannoname .'set(){
				'.$pannoname.'_pitch.set("value", '.$pannoname.'viewer.getPitch());
				'.$pannoname.'_yaw.set("value", '.$pannoname.'viewer.getYaw());
				'.$pannoname.'_hfov.set("value",'.$pannoname.'viewer.getHfov());         
			};

			function '.$pannoname .'initialize() {
				'.$pannoname.'_pitch = document.getElementById("ctrl_'.$this->strId.'_0");
				'.$pannoname.'_yaw = document.getElementById("ctrl_'.$this->strId.'_1");
				'.$pannoname.'_hfov = document.getElementById("ctrl_'.$this->strId.'_2");
				'.$pannoname.'viewer = pannellum.viewer("'.$pannoname .'_canvas", 
				'.json_encode($config).'
				).on("mouseup", '.$pannoname .'set).on("mousedown", '.$pannoname .'set).on("zoomchange", '.$pannoname .'set);
			}
			</script>';

		unset($config);

		$arrFields = array();

		for ($i=0; $i<3; $i++)
		{
			$arrFields[] = sprintf('<input type="text" name="%s[]" id="ctrl_%s" class="tl_text_3" value="%s" %s onfocus="Backend.getScrollOffset()">',
									$this->strName,
									$this->strId.'_'.$i,
									StringUtil::specialchars(@$this->varValue[$i]),
									$this->getAttributes());
		}


		return sprintf('<div id="ctrl_%s"%s>%s</div>%s',
						$this->strId,
						(($this->strClass != '') ? ' class="' . $this->strClass . '"' : ''),
						implode(' ', $arrFields),
						$this->wizard);
	}
}