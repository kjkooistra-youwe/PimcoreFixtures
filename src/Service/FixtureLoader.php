<?php

namespace Youwe\FixturesBundle\Service;

use Youwe\FixturesBundle\Alice\Providers\Assets;
use Youwe\FixturesBundle\Alice\Persister\PimcorePersister;
use Youwe\FixturesBundle\Alice\Processor\ClassificationStoreProcessor;
use Youwe\FixturesBundle\Alice\Processor\DocumentProperties;
use Youwe\FixturesBundle\Alice\Processor\UserProcessor;
use Youwe\FixturesBundle\Alice\Processor\WorkspaceProcessor;
use Youwe\FixturesBundle\Alice\Providers\ClassificationStoreProvider;
use Youwe\FixturesBundle\Alice\Providers\DateTime;
use Youwe\FixturesBundle\Alice\Providers\General;
use Youwe\FixturesBundle\Alice\Providers\ObjectReference;
use Nelmio\Alice\Fixtures;
use Pimcore\File;

class FixtureLoader
{
    const FIXTURE_FOLDER = PIMCORE_PRIVATE_VAR . '/bundles/FixturesBundle/fixtures';
    const IMAGES_FOLDER  = PIMCORE_PRIVATE_VAR . '/bundles/FixturesBundle/images';

    private static $objects = [];
    /**
     * @var bool
     */
    private $omitValidation;
    /**
     * @var bool
     */
    private $checkPathExists;

    /**
     * FixtureLoader constructor.
     * @param bool $checkPathExists
     * @param bool $omitValidation
     */
    public function __construct($checkPathExists, $omitValidation) {
        $this->omitValidation = $omitValidation;
        $this->checkPathExists = $checkPathExists;
    }
    /**
     * @param array|null $specificFiles Array of files in fixtures folder
     * @return array
     */
    public static function getFixturesFiles($specificFiles = [])
    {
        self::createFolderDependencies([
            self::FIXTURE_FOLDER,
            self::IMAGES_FOLDER
        ]);

        if (is_array($specificFiles) && count($specificFiles) > 0) {
            $fixturesFiles = glob(self::FIXTURE_FOLDER . '/{' . implode(',', $specificFiles) . '}.{yml,php}', GLOB_BRACE);
        } else {
            $fixturesFiles = glob(self::FIXTURE_FOLDER . '/*.{yml,php}',GLOB_BRACE);
        }

        usort($fixturesFiles, function ($a, $b) {
            return strnatcasecmp($a, $b);
        });

        return $fixturesFiles;
    }

    /**
     * @param string $fixtureFile
     */
    public function load($fixtureFile)
    {
        $providers = [
            new Assets(self::IMAGES_FOLDER), // Will provide functionality to load images
            new ClassificationStoreProvider(),
            new General(),
            new DateTime(),
            new ObjectReference(self::$objects),
        ];
        $processors = [
            new ClassificationStoreProcessor(),
            new UserProcessor(),
            new WorkspaceProcessor(),
            new DocumentProperties()
        ];
        $persister = new PimcorePersister($this->checkPathExists, $this->omitValidation);
        $basename = basename($fixtureFile);
        self::$objects[ $basename ] = array_merge(self::$objects, Fixtures::load($fixtureFile, $persister, ['providers' => $providers], $processors));
    }

    /**
     * Makes sure all folders are created so glob does not throw any error
     * @param array $folders
     */
    private static function createFolderDependencies($folders)
    {
        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                File::mkdir($folder);
            }
        }
    }
}
