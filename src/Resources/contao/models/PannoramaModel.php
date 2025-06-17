<?php
namespace Pannorama;

use Contao\Model;

class PannoramaModel extends Model
{
    protected static $strTable = 'tl_pannorama';
}

class_alias(PannoramaModel::class, 'PannoramaModel');