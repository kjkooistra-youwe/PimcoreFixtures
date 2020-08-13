<?php

namespace Youwe\FixturesBundle\Repository;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;

class FolderRepository
{
    /**
     * @param $query
     * @return AbstractObject[]
     */
    public function getFoldersByQuery($query = null)
    {
        $folders = new DataObject\Listing();
        $folders->setObjectTypes([AbstractObject::OBJECT_TYPE_FOLDER]);

        if ($query) {
            $folders->setCondition('CONCAT(o_path, o_key) LIKE ?', '%' . $query . '%');
        }

        return $folders->getObjects();
    }
}
