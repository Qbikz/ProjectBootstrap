<?php

class Qbikz_ProjectBootstrap_Model_Installer_Tax_Rule extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{
    /**
     * [$_fileName description]
     * @var string
     */
    protected $_fileName = 'tax_rule';

    /**
     * [$_identifier description]
     * @var string
     */
    protected $_identifier = 'code';

    /**
     * [$_rates description]
     * @var array|null
     */
    private $_taxRates;

    /**
     * [$_taxClasses description]
     * @var array|null
     */
    private $_taxClasses = array(
        Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT    => null,
        Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER   => null,
    );

    /**
     * [reset description]
     * @return Qbikz_ProjectBootstrap_Model_Setup_Tax_Rule [description]
     */
    public function cleanup()
    {
        $db = $this->getConnection();

        # Delete all rules before processing this installer
        $db->delete($this->getTable('tax/tax_calculation'));
        $db->delete($this->getTable('tax/tax_calculation_rule'));

        return $this;
    }

    /**
     * [setup description]
     * @return Qbikz_ProjectBootstrap_Model_Setup_Tax_Rule [description]
     */
    public function install()
    {
        $db = $this->getConnection();
        $rows = $this->_getData();
        $rules = array();
        foreach ($rows as $row) {
            $row['tax_product_class']   = $this->_getTaxClassIds($row['tax_product_class'], Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT);
            $row['tax_customer_class']  = $this->_getTaxClassIds($row['tax_customer_class'], Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER);
            $row['tax_rate']            = $this->_getTaxRateIds($row['tax_rate']);
            $model = Mage::getModel('tax/calculation_rule');
            $model->setData($row)
                  ->save();
        }
        return $this;
    }

    /**
     * [_getTaxProductClassIds description]
     * @param  [type] $taxClasses  [description]
     * @return [type]              [description]
     */
    private function _getTaxClassIds($taxClasses, $type = Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
    {
        if (null === $this->_taxClasses[$type]) {
            $db = $this->getConnection();
            $this->_taxClasses[$type] = $db->fetchPairs(
                $db->select()->from(
                    $this->getTable('tax/tax_class'),
                    array('class_name', 'class_id')
                )
                ->where('class_type = ?', $type)
            );
        }
        if (! is_array($taxClasses)) {
            $taxClasses = array($taxClasses);
        }
        $result = array();
        foreach ($taxClasses as $taxClass) {
            if (isset($this->_taxClasses[$type][$taxClass])) {
                $result[] = $this->_taxClasses[$type][$taxClass];
            }
        }
        return $result;
    }

    /**
     * [_getTaxRateIds description]
     * @param  [type] $taxRates [description]
     * @return [type]           [description]
     */
    private function _getTaxRateIds($taxRates)
    {
        if (null === $this->_taxRates) {
            $db = $this->getConnection();
            $this->_taxRates = $db->fetchPairs(
                $db->select()->from(
                    $this->getTable('tax/tax_calculation_rate'),
                    array('code', 'tax_calculation_rate_id')
                )
            );
        }
        if (! is_array($taxRates)) {
            $taxRates = array($taxRates);
        }
        $result = array();
        foreach ($taxRates as $taxRate) {
            if (isset($this->_taxRates[$taxRate])) {
                $result[] = $this->_taxRates[$taxRate];
            }
        }
        return $result;
    }
}
