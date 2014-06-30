<?php

class Qbikz_ProjectBootstrap_Model_Installer_Customer_Group extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{
    /**
     * [$_fileName description]
     * @var string
     */
    protected $_fileName = 'customer_group';

    /**
     * [$_identfier description]
     * @var string
     */
    protected $_identifier = 'code';

    /**
     * [$_taxClasses description]
     * @var array
     */
    private $_taxClasses;

    /**
     * [cleanup description]
     * @return [type] [description]
     */
    public function cleanup()
    {
        $db = $this->getConnection();
        $db->delete(
            $this->getTable('customer/customer_group'),
            $db->quoteInto('customer_group_id > ?', 0)
        );
        return $this;
    }

    /**
     * [install description]
     * @return Qbikz_ProjectBootstrap_Model_Installer_Customer_Attribute [description]
     */
    public function install()
    {
        $rows = $this->_getData();
        foreach ($rows as $code => $row) {
            $group = Mage::getModel('customer/group')
                ->load($code, 'customer_group_code');

            $group->setCode($code)
                ->setTaxClassId($this->_getTaxClassId($row['tax_class']))
                ->save();
        }
        return $this;
    }

    /**
     * [_getTaxClassId description]
     * @param  [type] $taxClass [description]
     * @return [type]           [description]
     */
    private function _getTaxClassId($taxClass)
    {
        if (null === $this->_taxClasses) {
            $db = $this->getConnection();
            $this->_taxClasses = $db->fetchPairs(
                $db->select()->from(
                    $this->getTable('tax/tax_class'),
                    array('class_name', 'class_id')
                )
                ->where('class_type = ?', Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
            );
        }
        if (isset($this->_taxClasses[$taxClass])) {
            return $this->_taxClasses[$taxClass];
        }
        return null;
    }
}
