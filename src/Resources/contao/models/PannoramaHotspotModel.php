<?php
namespace Pannorama;

use Contao\Model;

class PannoramaHotspotModel extends Model
{
    protected static $strTable = 'tl_pannorama_hotspot';
}

class_alias(PannoramaHotspotModel::class, 'PannoramaHotspotModel');