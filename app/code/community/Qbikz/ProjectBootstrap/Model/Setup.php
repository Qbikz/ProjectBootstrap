<?php

class Qbikz_ProjectBootstrap_Model_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * [setup description]
     * @param  array  $modules [description]
     * @return [type]          [description]
     */
    public function setupModules(array $modules)
    {
        foreach ($modules as $module) {
            $this->setupModule($module);
        }
    }

    /**
     * [setupModule description]
     * @param  [type] $module [description]
     * @return [type]         [description]
     */
    public function setupModule($module)
    {
        /* @var float $start */
        $start = microtime(true);

        /* @var $installer Qbikz_ProjectBootstrap_Installer_Abstract */
        $installer = Mage::getSingleton('projectbootstrap/installer_' . $module);
        if (! $installer) {
            return Mage::log(sprintf('[%s] Installer not found', $module));
        }

        try {
            $installer->cleanup();
        } catch (Exception $e) {
            throw $e;
        }

        try {
            $installer->install();
        } catch (Exception $e) {
            try {
                $installer->cleanup();
            } catch (Exception $e) {
                throw $e;
            }
            Mage::log(sprintf('[%s] Error on install: %s', $module, $e->getMessage()));
        }

        Mage::log(sprintf(
            '[%s] finished in %.3f seconds',
            $module,
            microtime(true) - $start
        ));
    }
}
