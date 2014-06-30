<?php

class Qbikz_ProjectBootstrap_Model_Installer_Customer extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{
    /**
     * [$_additionalArrays description]
     * @var array
     */
    protected $_additional = array('address');

    /**
     * [$_fileName description]
     * @var string
     */
    protected $_fileName = 'customer';

    /**
     * [$_identifier description]
     * @var string
     */
    protected $_identifier = 'email';

    /**
     * [reset description]
     * @return [type] [description]
     */
    public function cleanup()
    {
        return $this;
    }

    /**
     * [setup description]
     * @return Qbikz_ProjectBootstrap_Model_Setup_Category_Attribute [description]
     */
    public function install()
    {
        //var_dump($this->_getData());
        return $this;
    }
}
