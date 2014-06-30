<?php

class Qbikz_ProjectBootstrap_Model_Installer_Website extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{

    /**
     * [$_fileName description]
     * @var string
     */
    protected $_fileName = 'website';

    /**
     * [cleanup description]
     * @return [type] [description]
     */
    public function cleanup()
    {
        $db = $this->getConnection();
        # Delete default store(s)
        $db->delete(
            $this->getTable('core/store'),
            $db->quoteInto('store_id > ?', 0)
        );
        # Delete default store group(s)
        $db->delete(
            $this->getTable('core/store_group'),
            $db->quoteInto('group_id > ?', 0)
        );
        # Delete default website(s)
        $db->delete(
            $this->getTable('core/website'),
            $db->quoteInto('website_id > ?', 0)
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
        $store      = null;

        $rows = $this->_getData();
        foreach ($rows as $row) {
            # Create website
            if (! empty($row['website_code'])) {
                $website = $this->_createWebsite($row);
            }
            if (! $website || ! $website->getId()) {
                Mage::throwException('Website scope must be provided.');
            }
            # Create store group
            if (! empty($row['store_group_name'])) {
                $group = $this->_createGroup($row, $website);
                if ($row['store_group_name'] === $website->getDefaultStoreGroupName()) {
                    $website->setDefaultGroupId($group->getId())
                        ->save();
                }
            }
            if (! $group || ! $group->getId()) {
                Mage::throwException('Store group scope must be provided.');
            }
            # Create store
            if (! empty($row['store_code'])) {
                $store = $this->_createStore($row, $group);
                if ($row['store_code'] === $group->getDefaultStoreCode()) {
                    $group->setDefaultStoreId($store->getId())
                        ->save();
                }
            }
        }
    }

    /**
     * Create a new website and return the model.
     *
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    private function _createWebsite(array $data)
    {
        return Mage::getModel('core/website')
            ->setCode($data['website_code'])
            ->setName($data['website_name'])
            ->setSortOrder($data['website_sort_order'])
            ->setDefaultStoreGroupName($data['website_default_store_group_name'])
            ->setIsDefault($data['website_is_default'])
            ->save();
    }

    /**
     * [_createGroup description]
     * @param  array                   $data    [description]
     * @param  Mage_Core_Model_Website $website [description]
     * @return [type]                           [description]
     */
    private function _createGroup(array $data, Mage_Core_Model_Website $website)
    {
        return Mage::getModel('core/store_group')
            ->setWebsiteId($website->getId())
            ->setName($data['store_group_name'])
            ->setRootCategoryId(null)
            ->setDefaultStoreCode($data['store_group_default_store_code'])
            ->save();
    }

    /**
     * [_createStore description]
     * @param  array                 $data  [description]
     * @param  Mage_Core_Model_Group $group [description]
     * @return [type]                       [description]
     */
    private function _createStore(array $data, Mage_Core_Model_Store_Group $group)
    {
        return Mage::getModel('core/store')
            ->setWebsiteId($group->getWebsite()->getId())
            ->setGroupId($group->getId())
            ->setCode($data['store_code'])
            ->setName($data['store_name'])
            ->setIsActive($data['store_is_active'])
            ->setSortOrder($data['store_sort_order'])
            ->save();
    }
}
