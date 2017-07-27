<?php
			
class PannoramaViewer extends ContentElement
{
	protected $strTemplate = 'ce_pannorama';

	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objCat = \PannoramaModel::findByPK($this->pannoramaviewer);
			if (isset($objCat)) {
				$objTemplate = new \BackendTemplate('be_wildcard');
				$objTemplate->title =  $objCat->title;
				return $objTemplate->parse();	
			}
		}
		return parent::generate();
	}//end generate

	protected function compile(){
		global $objPage;

		$GLOBALS['TL_CSS'][] = 		  'bundles/jonnysppannorama/pannellum.css';
		$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/jonnysppannorama/pannellum.js';
		

		$objPannorama = \PannoramaModel::findByPK($this->pannoramaviewer);
		
 		$config['default']['firstScene'] = $objPannorama->firstScene;
        $config['default']['sceneFadeDuration'] = intval($objPannorama->sceneFadeDuration);
        $config['default']['autoLoad'] = boolval($objPannorama->autoLoad);

		if (boolval($objPannorama->autoLoad) == false){
			$config['default']['preview'] = $this->Environment->base.\FilesModel::findByPk($objPannorama->preview)->path;
			$config['default']['loadButtonLabel'] = $objPannorama->loadButtonLabel;
		}

        $config['default']['hotSpotDebug'] = boolval($objPannorama->hotSpotDebug);

		//Scenen
		if(\PannoramaSceneModel::countBy('pid', $objPannorama->id) > 0){

	        foreach (\PannoramaSceneModel::findByPid($objPannorama->id) as $key => $value) {
	        	
				if (boolval($value->showTitle) == true){
					if($value->title != ''){
						$config['scenes'][$value->id]['title']  = $value->title;
					}
					if($value->author != ''){
						$config['scenes'][$value->id]['author'] = $value->author;
					}
				}

				$tempposition = unserialize($value->position);
				$config['scenes'][$value->id]['pitch'] = floatval($tempposition[0]); 
	        	$config['scenes'][$value->id]['yaw'] = floatval($tempposition[1]); 
	        	$config['scenes'][$value->id]['hfov'] = intval($tempposition[2]); 
				unset($tempposition);

				//scenetype
				switch ($value->type) {
				    case 'equirectangular':
						$config['scenes'][$value->id]['type'] ='equirectangular';
						$config['scenes'][$value->id]['panorama'] = $this->Environment->base.\FilesModel::findByPk($value->panorama)->path;
				        break;

				    case 'cubemap_single':
						$config['scenes'][$value->id]['type'] = 'cubemap';
						$filemodel = \FilesModel::findByPk($value->panorama);
						if (isset($filemodel)){
							$file = new \File($filemodel->path);
							$panelsizeheight = $file->imageSize[1] / 3;
							$panelsizewidth = $file->imageSize[0] / 4;

							if ($panelsizeheight == $panelsizewidth){
								$image = new \Image($file);
								//panoramafront,panoramaright,panoramaback,panoramaleft,panoramaup,panoramadown
								$cubemap[] = $this->Environment->base.$image->setImportantPart(array('x'=>$panelsizewidth,'y'=>$panelsizeheight,'width'=> $panelsizeheight,'height'=>$panelsizeheight))->setZoomLevel(100)->setResizeMode('crop')->setTargetWidth($panelsizewidth)->setTargetHeight($panelsizeheight)->executeResize()->getResizedPath(); // front
								$cubemap[] = $this->Environment->base.$image->setImportantPart(array('x'=>$panelsizewidth*2,'y'=>$panelsizeheight,'width'=> $panelsizeheight,'height'=>$panelsizeheight))->setZoomLevel(100)->setResizeMode('crop')->setTargetWidth($panelsizewidth)->setTargetHeight($panelsizeheight)->executeResize()->getResizedPath();
								$cubemap[] = $this->Environment->base.$image->setImportantPart(array('x'=>$panelsizewidth*3,'y'=>$panelsizeheight,'width'=> $panelsizeheight,'height'=>$panelsizeheight))->setZoomLevel(100)->setResizeMode('crop')->setTargetWidth($panelsizewidth)->setTargetHeight($panelsizeheight)->executeResize()->getResizedPath();
								$cubemap[] = $this->Environment->base.$image->setImportantPart(array('x'=>0,'y'=>$panelsizeheight,'width'=> $panelsizeheight,'height'=>$panelsizeheight))->setZoomLevel(100)->setResizeMode('crop')->setTargetWidth($panelsizewidth)->setTargetHeight($panelsizeheight)->executeResize()->getResizedPath(); // left
								$cubemap[] = $this->Environment->base.$image->setImportantPart(array('x'=>$panelsizewidth,'y'=>0,'width'=> $panelsizewidth,'height'=>$panelsizeheight))->setZoomLevel(100)->setResizeMode('crop')->setTargetWidth($panelsizewidth)->setTargetHeight($panelsizeheight)->executeResize()->getResizedPath(); //top
								$cubemap[] = $this->Environment->base.$image->setImportantPart(array('x'=>$panelsizewidth,'y'=>$panelsizeheight*2,'width'=> $panelsizeheight,'height'=>$panelsizeheight))->setZoomLevel(100)->setResizeMode('crop')->setTargetWidth($panelsizewidth)->setTargetHeight($panelsizeheight)->executeResize()->getResizedPath(); // down
								$config['scenes'][$value->id]['cubeMap'] = $cubemap;
								unset($cubemap);
							}
						}
				        break;

				    case 'cubemap_multi':
						$config['scenes'][$value->id]['type'] = 'cubemap';
						//panoramafront,panoramaright,panoramaback,panoramaleft,panoramaup,panoramadown
						$cubemap[] = $this->Environment->base.\FilesModel::findByPk($value->panoramafront)->path;
						$cubemap[] = $this->Environment->base.\FilesModel::findByPk($value->panoramaright)->path;
						$cubemap[] = $this->Environment->base.\FilesModel::findByPk($value->panoramaback)->path;
						$cubemap[] = $this->Environment->base.\FilesModel::findByPk($value->panoramaleft)->path;
						$cubemap[] = $this->Environment->base.\FilesModel::findByPk($value->panoramaup)->path;
						$cubemap[] = $this->Environment->base.\FilesModel::findByPk($value->panoramadown)->path;
						$config['scenes'][$value->id]['cubeMap'] = $cubemap;
						unset($cubemap);
				        break;

				}

				if (boolval($value->autoRotateOn) == true){
					$config['scenes'][$value->id]['autoRotate'] =  intval($value->autoRotate);
				    $config['scenes'][$value->id]['autoRotateInactivityDelay'] =  intval($value->autoRotateInactivityDelay);
				}

				if (boolval($value->showZoomCtrl) == true || boolval($value->showFullscreenCtrl) == true) {
					$config['scenes'][$value->id]['showControls'] = true;
					$config['scenes'][$value->id]['showZoomCtrl'] = boolval($value->showZoomCtrl);
					$config['scenes'][$value->id]['showFullscreenCtrl'] = boolval($value->showFullscreenCtrl);
				}else{
					$config['scenes'][$value->id]['showControls'] = false;
				}

				$config['scenes'][$value->id]['compass'] = boolval($value->compass);
				$config['scenes'][$value->id]['northOffset'] =  intval($value->northOffset);

				$config['scenes'][$value->id]['keyboardZoom'] = boolval($value->keyboardZoom);
	        	$config['scenes'][$value->id]['mouseZoom'] = boolval($value->mouseZoom);
				$config['scenes'][$value->id]['minHfov'] =  intval($value->minHfov);
				$config['scenes'][$value->id]['maxHfov'] = intval($value->maxHfov);

				//Hotspots
				if(\PannoramaHotspotModel::countBy('pid', $value->id) > 0){
					foreach (\PannoramaHotspotModel::findByPid($value->id) as $hotkey => $hotvalue){
						$tempposition = unserialize($hotvalue->position);
						$temptargetposition = unserialize($hotvalue->targetposition);
		
						$hotspot['pitch'] = floatval($tempposition[0]); 
						$hotspot['yaw'] = floatval($tempposition[1]);
						$hotspot['type'] = $hotvalue->type;
						$hotspot['text'] = $hotvalue->title;
						if ($hotvalue->cssClass <> ''){
							$hotspot['cssClass']= $hotvalue->cssClass;
						}		
						if ($hotvalue->type == 'scene'){ 
							$hotspot['sceneId'] = $hotvalue->sceneId;
							$hotspot['targetPitch'] = floatval($temptargetposition[0]);
							$hotspot['targetYaw'] = floatval($temptargetposition[1]);
							$hotspot['targetHfov'] = intval($temptargetposition[2]);
						}elseif ($hotvalue->type == 'info' && $hotvalue->url <> '') { 
							$hotspot['URL'] = $hotvalue->url; 
						}
		
						$config['scenes'][$value->id]['hotSpots'][]= $hotspot;
						unset($hotspot);
						unset($tempposition);
						unset($temptargetposition);
					}
				}
	        }
		}

		//$this->Template->pannorama = json_encode($config,JSON_PRETTY_PRINT);
		$this->Template->pannorama = json_encode($config);
		unset($config);

	}//end compile

}//end class

