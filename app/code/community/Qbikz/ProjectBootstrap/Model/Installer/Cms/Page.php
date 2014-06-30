<?php

class Qbikz_ProjectBootstrap_Model_Installer_Cms_Page extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{
    /**
     * [$_fileName description]
     * @var string
     */
    protected $_fileName = 'cms_page';

    /**
     * [$_templateScope description]
     * @var string
     */
    protected $_templateScope = 'page';

    /**
     * [reset description]
     * @return [type] [description]
     */
    public function cleanup()
    {
        $db = $this->getConnection();
        $db->delete($this->getTable('cms/page'));
        return $this;
    }

    /**
     * [setup description]
     * @return Qbikz_ProjectBootstrap_Model_Setup_Cms_Page [description]
     */
    public function install()
    {
        $rows = $this->_getData();
        foreach ($rows as $row) {
            $page = $this->_createPage($row);
        }
        return $this;
    }

    /**
     * [_createPage description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    private function _createPage(array $data)
    {
        # Defaults
        $data = array_merge(array(
            'is_active' => 1,
            'content'   => '',
            'template'  => '',
            'stores'    => array(),
        ), array_filter($data));

        # Create new object
        return Mage::getModel('cms/page')
            ->addData($data)
            ->setStores($this->_getStoreIds($data['stores']))
            ->setContent($this->_getContent($data['content'], $data['template']))
            ->save();
    }
}
