<?php

class Qbikz_ProjectBootstrap_Model_Installer_Website_Category extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{
    /**
     * [$_fileName description]
     * @var string
     */
    protected $_fileName = 'website';

    /**
     * [$_rootCategories description]
     * @var array
     */
    private $_rootCategories = array();

    /**
     * [cleanup description]
     * @return [type] [description]
     */
    public function cleanup()
    {
        $this->getConnection()->update(
            $this->getTable('core/store_group'),
            array('root_category_id' => null)
        );
        return $this;
    }

    /**
     * [install description]
     * @return [type] [description]
     */
    public function install()
    {
        $website    = null;
        $group      = null;

        $rows = $this->_getData();
        foreach ($rows as $row) {
            if (! empty($row['website_code'])) {
                $website = $this->_loadWebsite($row);
            }
            if (! $website || ! $website->getId()) {
                Mage::throwException('Website scope must be provided.');
            }
            if (! empty($row['store_group_name'])) {
                $group = $this->_loadGroup($row, $website);
                if (! $group || ! $group->getId()) {
                    Mage::throwException('Store group scope must be provided.');
                }
                $group->setRootCategoryId($this->_getRootCategoryId($row['store_group_root_category_name']))
                    ->save();
            }
        }
    }

    /**
     * [_getRootCategoryId description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    private function _getRootCategoryId($name)
    {
        if (empty($this->_rootCategories)) {
            $collection = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addLevelFilter(1);
            foreach ($collection as $item) {
                $this->_rootCategories[$item->getName()] = $item->getId();
            }
        }
        return isset($this->_rootCategories[$name])
            ? $this->_rootCategories[$name]
            : null;
    }

    /**
     * Create a new website and return the model.
     *
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    private function _loadWebsite(array $data)
    {
        return Mage::getModel('core/website')->load($data['website_code'], 'code');
    }

    /**
     * [_createGroup description]
     * @param  array                   $data    [description]
     * @param  Mage_Core_Model_Website $website [description]
     * @return [type]                           [description]
     */
    private function _loadGroup(array $data, Mage_Core_Model_Website $website)
    {
        foreach ($website->getGroups() as $group) {
            if ($data['store_group_name'] === $group->getName()) {
                return $group;
            }
        }
        return null;
    }
}
