<?php

class Qbikz_ProjectBootstrap_Model_Installer_Tax_Rate extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{
    /**
     * [$_fileName description]
     * @var string
     */
    protected $_fileName = 'tax_rate';

    /**
     * [reset description]
     * @return [type] [description]
     */
    public function cleanup()
    {
        $db = $this->getConnection();

        # Delete all rules before processing this installer
        $db->delete($this->getTable('tax/tax_calculation'));
        $db->delete($this->getTable('tax/tax_calculation_rule'));
        $db->delete($this->getTable('tax/tax_calculation_rate'));

        return $this;
    }

    /**
     * [setup description]
     * @return Qbikz_ProjectBootstrap_Model_Setup_Tax_Rate [description]
     */
    public function install()
    {
        $db = $this->getConnection();

        $rows = $this->_getData();
        foreach ($rows as $row) {
            if (! $row['zip_is_range']) {
                $row['zip_is_range']    = null;
                $row['zip_from']        = null;
                $row['zip_to']          = null;
            }
            $db->insertForce(
                $this->getTable('tax/tax_calculation_rate'),
                $row
            );
        }
        return $this;
    }
}
