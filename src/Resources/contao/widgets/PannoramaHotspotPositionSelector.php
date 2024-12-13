<?php
use Contao\Widget;
use Contao\System;
use Contao\StringUtil;
use Contao\FilesModel;
use Contao\File;
use Contao\Image;
use Contao\Image\ResizeConfiguration;
use Contao\Image\ResizeOptions;
use Pannorama\Model\PannoramaSceneModel;
use Pannorama\Model\PannoramaHotspotModel;
use Contao\Image\ImportantPart;

class PannoramaHotspotPositionSelector extends Widget
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

		$selfspot = PannoramaHotspotModel::findByPk($this->__get('currentRecord'));
		$startscene =  PannoramaSceneModel::findByPk($selfspot->pid);         

		//config
		switch ($startscene->type) {
		    case 'equirectangular':
				$config['type'] = 'equirectangular';
				$config['panorama'] = FilesModel::findByPk($startscene->panorama)->path;
		        break;

		    case 'cubemap_single':

				$config['type'] = 'cubemap';
				$filemodel = FilesModel::findByPk($startscene->panorama);
				if (isset($filemodel)){
					$file = new File($filemodel->path);
					if ($file->isImage && $file->isGdImage){
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
				
				if(
					($startscene->panoramafront !== null) && ($startscene->panoramaright !== null) && ($startscene->panoramaback !== null) &&
					($startscene->panoramaleft !== null) && ($startscene->panoramaup !== null) && ($startscene->panoramadown !== null) 
				){
					$config['cubeMap']	= array(
						FilesModel::findByPk($startscene->panoramafront)->path,
						FilesModel::findByPk($startscene->panoramaright)->path,
						FilesModel::findByPk($startscene->panoramaback)->path,
						FilesModel::findByPk($startscene->panoramaleft)->path,
						FilesModel::findByPk($startscene->panoramaup)->path,
						FilesModel::findByPk($startscene->panoramadown)->path
					);
				}

	        break;
		}


		$config['autoLoad'] = true;
    	$config['pitch'] = floatval($this->varValue[0]);
		$config['yaw'] = floatval($this->varValue[1]);
		$config['hfov'] = 120;
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

    	if(PannoramaHotspotModel::countBy('pid', $startscene->id) > 0){
			foreach (PannoramaHotspotModel::findByPid($startscene->id) as $hotkey => $hotvalue){
				if ($selfspot <> $hotvalue){
					$tempposition = unserialize($hotvalue->position);
					$hotspot['pitch'] = floatval($tempposition[0]); 
					$hotspot['yaw'] = floatval($tempposition[1]);
					$hotspot['type'] = 'info';
					$hotspot['text'] = $hotvalue->title;
					$hotspot['cssClass'] = $hotvalue->type.'_spot';
					//$hotspot['URL'] = System::getContainer()->get('router')->generate('contao_backend', array('do'=>'pannorama', 'table'=>'tl_pannorama_hotspot','act'=>'edit', 'id'=>$hotvalue->id));
					$config['hotSpots'][] = $hotspot;
					unset($hotspot);
				}
			}
    	}

		$pannonama = 'pannoramaposition'.$this->__get('currentRecord');
	
		echo '<div class="tl_text" id="'.$pannonama .'_canvas" style="width:auto; height:300px;"></div><br>
			<script type="text/javascript">

			var '.$pannonama.'viewer;

			window.addEvent("domready", function() {
				'.$pannonama.'initialize();
			});

			function '.$pannonama .'set(){
				'.$pannonama.'_pitch.set("value", '.$pannonama.'viewer.getPitch());
				'.$pannonama.'_yaw.set("value", '.$pannonama.'viewer.getYaw());        
			};

			function '.$pannonama .'initialize() {
				'.$pannonama.'_pitch = document.getElementById("ctrl_'.$this->strId.'_0");
				'.$pannonama.'_yaw = document.getElementById("ctrl_'.$this->strId.'_1");
				'.$pannonama.'viewer = pannellum.viewer("'.$pannonama .'_canvas", 
				'.json_encode($config).'
				).on("mouseup", '.$pannonama .'set).on("mousedown", '.$pannonama .'set).on("zoomchange", '.$pannonama .'set);
			}
			</script>';

		unset($config);

		$arrFields = array();

		for ($i=0; $i<2; $i++)
		{
			$arrFields[] = sprintf('<input type="text" name="%s[]" id="ctrl_%s" class="tl_text_2" value="%s" %s onfocus="Backend.getScrollOffset()">',
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

	return '';
	}
}