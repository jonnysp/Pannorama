<?php
namespace Pannorama;

use Contao\Model;

class PannoramaSceneModel extends Model
{
    protected static $strTable = 'tl_pannorama_scene';
}

class_alias(PannoramaSceneModel::class, 'PannoramaSceneModel');