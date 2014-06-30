<?php

class Qbikz_ProjectBootstrap_Model_Installer_Cms_Block extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{
    /**
     * [$_fileName description]
     * @var string
     */
    protected $_fileName = 'cms_block';

    /**
     * [$_templateScope description]
     * @var string
     */
    protected $_templateScope = 'block';

    /**
     * [cleanup description]
     * @return [type] [description]
     */
    public function cleanup()
    {
        $db = $this->getConnection();
        $db->delete($this->getTable('cms/block'));
        return $this;
    }

    /**
     * [setup description]
     * @return Qbikz_ProjectBootstrap_Model_Setup_Cms_Block [description]
     */
    public function install()
    {
        $rows = $this->_getData();
        foreach ($rows as $row) {
            $block = $this->_createBlock($row);
        }
        return $this;
    }

    /**
     * [_createBlock description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    private function _createBlock(array $data)
    {
        # Defaults
        $data = array_merge(array(
            'is_active' => 1,
            'content'   => '',
            'template'  => '',
            'stores'    => array(),
        ), array_filter($data));

        # Save block
        return Mage::getModel('cms/block')
            ->addData($data)
            ->setStores($this->_getStoreIds($data['stores']))
            ->setContent($this->_getContent($data['content'], $data['template']))
            ->save();
    }
}
