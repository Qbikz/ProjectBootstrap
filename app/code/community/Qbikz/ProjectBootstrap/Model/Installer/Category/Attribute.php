<?php

class Qbikz_ProjectBootstrap_Model_Installer_Category_Attribute extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{
    /**
     * [$_filename description]
     * @var string
     */
    protected $_fileName = 'category_attribute';

    /**
     * [$_identifier description]
     * @var string
     */
    protected $_identifier = 'code';

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
     * @return [type] [description]
     */
    public function install()
    {
        $rows = $this->_getData();
        foreach ($rows as $code => $row) {
            $this->_addEavAttribute(Mage_Catalog_Model_Category::ENTITY, $code, $row);
        }
        return $this;
    }
}
