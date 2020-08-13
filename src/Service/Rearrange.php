<?php

namespace Youwe\FixturesBundle\Service;

use Symfony\Component\Yaml\Yaml;

class Rearrange {

    private $knownKeys = [];

    public function rearrangeFixtures() {

        $fixturesFiles = glob(FixtureLoader::FIXTURE_FOLDER . '/099_ibood_deal.yml');

        $parsedContent = [];

        foreach ($fixturesFiles as $file){
            $parsedContent [basename($file)] = Yaml::parse(file_get_contents($file));
            var_dump($parsedContent [basename($file)]);die;
        }

var_dump(current($parsedContent));die;
    }
}
