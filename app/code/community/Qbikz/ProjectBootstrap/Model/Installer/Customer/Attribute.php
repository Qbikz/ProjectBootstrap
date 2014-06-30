<?php

class Qbikz_ProjectBootstrap_Model_Installer_Customer_Attribute extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{
    /**
     * [$_fileName description]
     * @var string
     */
    protected $_fileName = 'customer_attribute';

    /**
     * [$_identfier description]
     * @var string
     */
    protected $_identfier = 'code';

    /**
     * [cleanup description]
     * @return [type] [description]
     */
    public function cleanup()
    {
        return $this;
    }

    /**
     * [install description]
     * @return Qbikz_ProjectBootstrap_Model_Installer_Customer_Attribute [description]
     */
    public function install()
    {
        $rows = $this->_getData();
        foreach ($rows as $row) {
            $this->_addEavAttribute(Mage_Customer_Model_Customer::ENTITY, $code, $row);
        }
        return $this;
    }
}
