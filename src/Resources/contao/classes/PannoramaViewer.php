<?php
use Contao\ContentElement;
use Contao\System;
use Contao\BackendTemplate;
use Contao\FilesModel;
use Contao\File;
use Contao\Image;
use Contao\Image\ResizeConfiguration;
use Contao\Image\ResizeOptions;
use Contao\Image\ImportantPart;

use Pannorama\Model\PannoramaModel;
use Pannorama\Model\PannoramaSceneModel;
use Pannorama\Model\PannoramaHotspotModel;

class PannoramaViewer extends ContentElement
{
	protected $strTemplate = 'ce_pannorama';

	public function generate()
	{
		$request = System::getContainer()->get('request_stack')->getCurrentRequest();

		if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
		{

			$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/jonnysppannorama/pannellum.js';
			$GLOBALS['TL_CSS'][] = 		  'bundles/jonnysppannorama/pannellum.css';
			$GLOBALS['TL_CSS'][] 		= 'bundles/jonnysppannorama/hotspots.css';

			$container = System::getContainer();
			$rootDir = $container->getParameter('kernel.project_dir');

			$objPannorama = PannoramaModel::findByPK($this->pannoramaviewer);
			 
			if (isset($objPannorama)) {
				$objTemplate = new BackendTemplate('be_wildcard');
				$objTemplate->title =  $objPannorama->title;

				//build config
		 		$config['default']['firstScene'] = $objPannorama->firstScene;
		        $config['default']['sceneFadeDuration'] = intval($objPannorama->sceneFadeDuration);
		        $config['default']['autoLoad'] = boolval($objPannorama->autoLoad);

				if (boolval($objPannorama->autoLoad) == false){
					$config['default']['preview'] = FilesModel::findByPk($objPannorama->preview)->path;
					$config['default']['loadButtonLabel'] = $objPannorama->loadButtonLabel;
				}


		        $config['default']['hotSpotDebug'] = boolval($objPannorama->hotSpotDebug);

				//Scenen
				if(PannoramaSceneModel::countBy('pid', $objPannorama->id) > 0){

			        foreach (PannoramaSceneModel::findByPid($objPannorama->id) as $key => $value) {
			        	
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
								$config['scenes'][$value->id]['panorama'] = FilesModel::findByPk($value->panorama)->path;
								break;

						    case 'cubemap_single':
								$config['scenes'][$value->id]['type'] = 'cubemap';
								$filemodel = FilesModel::findByPk($value->panorama);
								if (isset($filemodel)){

									$file = new File($filemodel->path);
									$panelsizeheight = $file->imageSize[1] / 3;
									$panelsizewidth = $file->imageSize[0] / 4;

									if ($panelsizeheight == $panelsizewidth){

										$cubemap = array();

										$resizeconfig =	(new ResizeConfiguration())
										->setWidth($panelsizewidth)
										->setHeight($panelsizeheight)
										->setMode(ResizeConfiguration::MODE_CROP)
										->setZoomLevel(100);

										$image = System::getContainer()
												->get('contao.image.factory')
												->create($rootDir . '/' . $filemodel
												->cloneOriginal()->path);

										//front
										$image->setImportantPart( new ImportantPart(0.25, 0.333333333333333 , 0.25 , 0.333333333333333 ));
										$cubemap[] = System::getContainer()->get('contao.image.resizer')->resize($image,$resizeconfig,new ResizeOptions())->getUrl($rootDir);

										//right
										$image->setImportantPart( new ImportantPart(0.5, 0.333333333333333 , 0.25 , 0.333333333333333 ));
										$cubemap[] = System::getContainer()->get('contao.image.resizer')->resize($image,$resizeconfig,new ResizeOptions())->getUrl($rootDir);

										//back
										$image->setImportantPart( new ImportantPart(0.75, 0.333333333333333 , 0.25, 0.333333333333333 ));
										$cubemap[] = System::getContainer()->get('contao.image.resizer')->resize($image,$resizeconfig,new ResizeOptions())->getUrl($rootDir);

										//left
										$image->setImportantPart( new ImportantPart(0, 0.333333333333333 , 0.25, 0.333333333333333 ));
										$cubemap[]=  System::getContainer()->get('contao.image.resizer')->resize($image,$resizeconfig,new ResizeOptions())->getUrl($rootDir);

										//top
										$image->setImportantPart( new ImportantPart(0.25, 0, 0.25, 0.333333333333333 ));
										$cubemap[] = System::getContainer()->get('contao.image.resizer')->resize($image,$resizeconfig,new ResizeOptions())->getUrl($rootDir);


										//bottom
										$image->setImportantPart( new ImportantPart(0.25, 0.666666666666666 , 0.25, 0.333333333333333 ));
										$cubemap[] = System::getContainer()->get('contao.image.resizer')->resize($image,$resizeconfig,new ResizeOptions())->getUrl($rootDir);

										$config['scenes'][$value->id]['cubeMap'] = $cubemap;
										unset($cubemap);
									}
								}
						        break;

						    case 'cubemap_multi':

								$config['scenes'][$value->id]['type'] = 'cubemap';
								if(
									($value->panoramafront !== null) && ($value->panoramaright !== null) && ($value->panoramaback !== null) &&
									($value->panoramaleft !== null) && ($value->panoramaup !== null) && ($value->panoramadown !== null) 
								){

									//panoramafront,panoramaright,panoramaback,panoramaleft,panoramaup,panoramadown
	
									$cubemap[] = 	FilesModel::findByPk($value->panoramafront)->path;
									$cubemap[] = 	FilesModel::findByPk($value->panoramaright)->path;
									$cubemap[] = 	FilesModel::findByPk($value->panoramaback)->path;
									$cubemap[] = 	FilesModel::findByPk($value->panoramaleft)->path;
									$cubemap[] = 	FilesModel::findByPk($value->panoramaup)->path;
									$cubemap[] = 	FilesModel::findByPk($value->panoramadown)->path;

									$config['scenes'][$value->id]['cubeMap'] = $cubemap;
									unset($cubemap);
								}

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
			        	$config['scenes'][$value->id]['mouseZoom'] = $value->mouseZoom;
						$config['scenes'][$value->id]['minHfov'] =  intval($value->minHfov);
						$config['scenes'][$value->id]['maxHfov'] = intval($value->maxHfov);
						$config['scenes'][$value->id]['doubleClickZoom'] = boolval($value->doubleClickZoom);
						$config['scenes'][$value->id]['draggable'] = boolval($value->draggable);

						//Hotspots
						if(PannoramaHotspotModel::countBy('pid', $value->id) > 0){
							foreach (PannoramaHotspotModel::findByPid($value->id) as $hotkey => $hotvalue){
								$tempposition = unserialize($hotvalue->position);
								$temptargetposition = unserialize($hotvalue->targetposition);
				
								$hotspot['pitch'] = floatval($tempposition[0]); 
								$hotspot['yaw'] = floatval($tempposition[1]);
								$hotspot['type'] = $hotvalue->type;
								$hotspot['text'] = $hotvalue->title;
								$hotspot['cssClass'] = $hotvalue->type.'_spot';

								if ($hotvalue->type == 'scene'){ 
									$hotspot['sceneId'] = $hotvalue->sceneId;
									$hotspot['targetPitch'] = floatval($temptargetposition[0]);
									$hotspot['targetYaw'] = floatval($temptargetposition[1]);
									$hotspot['targetHfov'] = intval($temptargetposition[2]);
								}
				
								$config['scenes'][$value->id]['hotSpots'][]= $hotspot;
								unset($hotspot);
								unset($tempposition);
								unset($temptargetposition);
							}
						}
			        }
				}

				$objTemplate->wildcard = '<div style="height:400px;" id="panorama'. $this->id.'" class="tl_text"></div></br>'."<script type=".'"text/javascript"'.">pannellum.viewer('panorama". $this->id."',". json_encode($config) .');</script>';

				unset($config);
				return $objTemplate->parse();	
			}
		}
		return parent::generate();
	}//end generate



	protected function compile(){
		$GLOBALS['TL_CSS'][] = 		  'bundles/jonnysppannorama/pannellum.css';
		$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/jonnysppannorama/pannellum.js';

		$container = System::getContainer();
		$rootDir = $container->getParameter('kernel.project_dir');

		$objPannorama = PannoramaModel::findByPK($this->pannoramaviewer);
		
        //translation
		if (file_exists('bundles/jonnysppannorama/'.strtoupper($GLOBALS['TL_LANGUAGE']).'.json')) {
			$config['default']['strings'] = json_decode(file_get_contents('bundles/jonnysppannorama/'.strtoupper($GLOBALS['TL_LANGUAGE']).'.json'), true);
		}

		$config['default']['firstScene'] = $objPannorama->firstScene;
		$config['default']['sceneFadeDuration'] = intval($objPannorama->sceneFadeDuration);
		$config['default']['autoLoad'] = boolval($objPannorama->autoLoad);

		if (boolval($objPannorama->autoLoad) == false){
			$config['default']['preview'] = FilesModel::findByPk($objPannorama->preview)->path;
			$config['default']['loadButtonLabel'] = $objPannorama->loadButtonLabel;
		}


        $config['default']['hotSpotDebug'] = boolval($objPannorama->hotSpotDebug);
		//Scenen
		if(PannoramaSceneModel::countBy('pid', $objPannorama->id) > 0){

	        foreach (PannoramaSceneModel::findByPid($objPannorama->id) as $key => $value) {
	        	
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
						$config['scenes'][$value->id]['panorama'] = FilesModel::findByPk($value->panorama)->path;
						break;

				    case 'cubemap_single':

						$config['scenes'][$value->id]['type'] = 'cubemap';
						$filemodel = FilesModel::findByPk($value->panorama);
						if (isset($filemodel)){

							$file = new File($filemodel->path);
							$panelsizeheight = $file->imageSize[1] / 3;
							$panelsizewidth = $file->imageSize[0] / 4;

							if ($panelsizeheight == $panelsizewidth){

								$cubemap = array();

								$resizeconfig =	(new ResizeConfiguration())
								->setWidth($panelsizewidth)
								->setHeight($panelsizeheight)
								->setMode(ResizeConfiguration::MODE_CROP)
								->setZoomLevel(100);

								$image = System::getContainer()
										->get('contao.image.factory')
										->create($rootDir . '/' . $filemodel
										->cloneOriginal()->path);

								//front
								$image->setImportantPart( new ImportantPart(0.25, 0.333333333333333 , 0.25 , 0.333333333333333 ));
								$cubemap[] = System::getContainer()->get('contao.image.resizer')->resize($image,$resizeconfig,new ResizeOptions())->getUrl($rootDir);

								//right
								$image->setImportantPart( new ImportantPart(0.5, 0.333333333333333 , 0.25 , 0.333333333333333 ));
								$cubemap[] = System::getContainer()->get('contao.image.resizer')->resize($image,$resizeconfig,new ResizeOptions())->getUrl($rootDir);

								//back
								$image->setImportantPart( new ImportantPart(0.75, 0.333333333333333 , 0.25, 0.333333333333333 ));
								$cubemap[] = System::getContainer()->get('contao.image.resizer')->resize($image,$resizeconfig,new ResizeOptions())->getUrl($rootDir);

								//left
								$image->setImportantPart( new ImportantPart(0, 0.333333333333333 , 0.25, 0.333333333333333 ));
								$cubemap[]=  System::getContainer()->get('contao.image.resizer')->resize($image,$resizeconfig,new ResizeOptions())->getUrl($rootDir);

								//top
								$image->setImportantPart( new ImportantPart(0.25, 0, 0.25, 0.333333333333333 ));
								$cubemap[] = System::getContainer()->get('contao.image.resizer')->resize($image,$resizeconfig,new ResizeOptions())->getUrl($rootDir);


								//bottom
								$image->setImportantPart( new ImportantPart(0.25, 0.666666666666666 , 0.25, 0.333333333333333 ));
								$cubemap[] = System::getContainer()->get('contao.image.resizer')->resize($image,$resizeconfig,new ResizeOptions())->getUrl($rootDir);

								$config['scenes'][$value->id]['cubeMap'] = $cubemap;
								unset($cubemap);
							}
						}
						break;

				    case 'cubemap_multi':

						$config['scenes'][$value->id]['type'] = 'cubemap';
						if(
							($value->panoramafront !== null) && ($value->panoramaright !== null) && ($value->panoramaback !== null) &&
							($value->panoramaleft !== null) && ($value->panoramaup !== null) && ($value->panoramadown !== null) 
						){

							//panoramafront,panoramaright,panoramaback,panoramaleft,panoramaup,panoramadown

							$cubemap[] = 	FilesModel::findByPk($value->panoramafront)->path;
							$cubemap[] = 	FilesModel::findByPk($value->panoramaright)->path;
							$cubemap[] = 	FilesModel::findByPk($value->panoramaback)->path;
							$cubemap[] = 	FilesModel::findByPk($value->panoramaleft)->path;
							$cubemap[] = 	FilesModel::findByPk($value->panoramaup)->path;
							$cubemap[] = 	FilesModel::findByPk($value->panoramadown)->path;

							$config['scenes'][$value->id]['cubeMap'] = $cubemap;
							unset($cubemap);
						}

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
	        	$config['scenes'][$value->id]['mouseZoom'] = $value->mouseZoom;
				$config['scenes'][$value->id]['minHfov'] =  intval($value->minHfov);
				$config['scenes'][$value->id]['maxHfov'] = intval($value->maxHfov);
				$config['scenes'][$value->id]['doubleClickZoom'] = boolval($value->doubleClickZoom);
				$config['scenes'][$value->id]['draggable'] = boolval($value->draggable);

				//Hotspots
				if(PannoramaHotspotModel::countBy('pid', $value->id) > 0){
					foreach (PannoramaHotspotModel::findByPid($value->id) as $hotkey => $hotvalue){
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

