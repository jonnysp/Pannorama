<?php

/*
 * Copyright (c) 2005-2024 Jonny Spitzner
 *
 * @license LGPL-3.0+
*/

namespace Jonnysp\Pannorama\ContaoManager;

use Jonnysp\Pannorama\JonnyspPannorama;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;


class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(JonnyspPannorama::class)
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setReplace(['pannorama']),
        ];
    }
}
