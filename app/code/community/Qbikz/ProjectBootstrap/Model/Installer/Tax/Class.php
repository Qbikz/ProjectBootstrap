<?php

class Qbikz_ProjectBootstrap_Model_Installer_Tax_Class extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{
    /**
     * [$_fileName description]
     * @var string
     */
    protected $_fileName = 'tax_class';

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
        $db->delete($this->getTable('tax/tax_class'));

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
            $db->insertForce(
                $this->getTable('tax/tax_class'),
                array(
                    'class_name' => $row['name'],
                    'class_type' => strtoupper($row['type']),
                )
            );
        }
        return $this;
    }
}
