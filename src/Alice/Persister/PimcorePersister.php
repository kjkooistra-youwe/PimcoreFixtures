<?php

namespace Youwe\FixturesBundle\Alice\Persister;

use Nelmio\Alice\PersisterInterface;
use Pimcore\Model\Asset;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\DataObject;
use Pimcore\Model\Redirect;
use Pimcore\Model\WebsiteSetting;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\User\AbstractUser;
use Pimcore\Model\User\Permission;
use Pimcore\Model\User\Workspace;
use Pimcore\Model\DataObject\QuantityValue;

class PimcorePersister implements PersisterInterface
{
    /**
     * @var bool
     */
    private $checkPathExists;
    /**
     * @var bool
     */
    private $omitValidation;

    /**
     * @param bool $checkPathExists
     * @param bool $omitValidation
     */
    public function __construct($checkPathExists, $omitValidation)
    {
        $this->checkPathExists = $checkPathExists;
        $this->omitValidation = $omitValidation;
    }

    /**
     * Loads a fixture file
     *
     * @param AbstractObject array [object] $objects instance to persist in the DB
     * @throws \Exception
     */
    public function persist(array $objects)
    {

        foreach ($objects as $object) {
            switch (true) {
                case $object instanceof AbstractElement:
                    $this->persistObject($object);
                    break;
                case $object instanceof AbstractUser:
                    $this->persistUser($object);
                    break;
                // Add here cases of exception that don't even have a save method but they actually do
                case $object instanceof Permission\Definition:
                    $this->persistClassWithSave($object);
                    break;
                case $object instanceof Redirect:
                    $this->persistClassWithSave($object);
                    break;
                case $object instanceof Workspace\DataObject:
                case $object instanceof Workspace\Asset:
                    $this->persistClassWithSave($object);
                    break;
                case $object instanceof DataObject\Objectbrick:
                    $this->persistObjectBrickSave($object);
                    break;
                case $object instanceof QuantityValue\Unit:
                case $object instanceof WebsiteSetting:
                    $this->persistClassWithSave($object);
                    break;
//                case $object instanceof Model\AbstractModel:
//                    var_dump(get_class($object));
//                    // Don't do persist because is not required to be persisted ex. FieldCollection
//                    // Also don't move because AbstractElement and AbstractObject are AbstractModel
//                    return null;
//                default:
//                    var_dump(get_class($object));
            }
        }
    }

    /**
     * @param DataObject\Concrete|Document|Asset $element
     */
    private function persistObject($element)
    {
        if ($this->checkPathExists === true) {
            if ($parent = $element->getParent()) {

                $path = str_replace('//', '/', $parent->getFullPath() . '/');
                $element->setPath($path);
            }
            $tmpObject = $element::getByPath($element->getFullPath());

            if ($tmpObject) {
                $objClass = get_class($element);
                if ($tmpObject instanceof $objClass) {
                    $element->setId($tmpObject->getId());
                } else {
                    $tmpObject->delete();
                }
            }
        }

        // We expect an element, only Object\Concrete has mandatory fields
        if(method_exists($element, 'setOmitMandatoryCheck')){
            $element->setOmitMandatoryCheck($this->omitValidation);
        }

        $element->save();
    }

    /**
     * @param AbstractUser $object
     */
    private function persistUser($object)
    {

        if ($this->ignorePathAlreadyExists === true) {
            $tmpObj = $object::getByName($object->getName());

            if ($tmpObj) {
                $object->setId($tmpObj->getId());
            }
        }
        $object->save();

    }

    /**
     * @param \stdClass $object
     */
    private function persistClassWithSave($object)
    {
        $object->save();
    }

    /**
     * @param Object\Objectbrick\Data\AbstractData $objectBrick
     * @throws \UnexpectedValueException
     */
    private function persistObjectBrickSave($objectBrick)
    {
        $setter = 'set' . $objectBrick->getFieldname();
        /** @var DataObject\Concrete $object */
        $object = $objectBrick->getObject();
        if (!method_exists($object, $setter)) {
            throw new \UnexpectedValueException(sprintf('Object with class %s has no setter %s', get_class($object), $setter));
        }
        $object->$setter($objectBrick);
        $object->save();
    }

    /**
     * Finds an object by class and id
     *
     * @param  string|AbstractObject $class
     * @param  int $id
     * @return mixed
     */
    public function find($class, $id)
    {

        $obj = $class::getById($id);
        if (!$obj) {
            throw new \UnexpectedValueException('Object with Id ' . $id . ' and Class ' . $class . ' not found');
        }

        return $obj;
    }

}
