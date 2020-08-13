<?php

namespace Youwe\FixturesBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class YouweFixturesBundle extends AbstractPimcoreBundle
{
	use PackageVersionTrait;

    protected function getComposerPackageName()
    {
        return 'youwe/pimcore-fixtures';
    }
}
