<?php

class Qbikz_ProjectBootstrap_Model_Installer_Product_Attributeset extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{

    /**
     * [$_filename description]
     * @var string
     */
    protected $_fileName = 'product_attributeset';

    /**
     * [$_identifier description]
     * @var string
     */
    protected $_identifier = 'name';

    /**
     * [$_attributeSetIds description]
     * @var [type]
     */
    private $_attributeSetIds;

    /**
     * [cleanup description]
     * @return [type] [description]
     */
    public function cleanup()
    {
        $entityTypeId = $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);
        $db = $this->getConnection();

        $db->delete(
            $this->getTable('eav/attribute_set'),
            $db->quoteInto('entity_type_id = ? AND attribute_set_id > 8', $entityTypeId)
        );
        return $this;
    }

    /**
     * [install description]
     * @return [type] [description]
     */
    public function install()
    {
        $db = $this->getConnection();
        $entityTypeId = $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);

        $attributeSetIds = $this->_getAttributeSetIds();
        $attributeGroups = $this->_getAttributeGroups();

        $rows = $this->_getData();
        foreach ($rows as $name => $row) {
            if (isset($attributeSetIds[$name])) {
                $attributeSet = Mage::getModel('eav/entity_attribute_set')
                    ->load($attributeSetIds[$name]);
            } else {
                $attributeSet = Mage::getModel('eav/entity_attribute_set')
                    ->setEntityTypeId($entityTypeId)
                    ->setAttributeSetName($name)
                    ->save();
            }
            if (! empty($row['extends'])) {
                $attributeSet->initFromSkeleton($attributeSetIds[$row['extends']]);
                $attributeSet->save();
            }
            foreach ($row['attributes'] as $attribute) {
                if (empty($attribute)) {
                    continue;
                }
                $this->addAttributeToSet(
                    $entityTypeId,
                    $attributeSet->getId(),
                    isset($attributeGroups[$attribute]) ? $attributeGroups[$attribute] : 'General',
                    $attribute
                );
            }
            $attributeSetIds[$name] = $attributeSet->getId();
        }
        return $this;
    }

    /**
     * [_getAttributeSetIds description]
     * @return [type] [description]
     */
    private function _getAttributeSetIds()
    {
        if (null === $this->_attributeSetIds) {
            $db = $this->getConnection();
            $entityTypeId = $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);

            $this->_attributeSetIds = $db->fetchPairs(
                $db->select()
                    ->from($this->getTable('eav/attribute_set'), array('attribute_set_name', 'attribute_set_id'))
                    ->where('entity_type_id = ?', $entityTypeId)
            );
        }
        return $this->_attributeSetIds;
    }

    /**
     * [_getAttributeGroups description]
     * @return [type] [description]
     */
    private function _getAttributeGroups()
    {
        $result = array();
        $rows = $this->_getData('product_attribute', 'code', array());
        foreach ($rows as $code => $row) {
            $result[$code] = $row['group'];
        }
        return $result;
    }
}
