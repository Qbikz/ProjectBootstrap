<?php

class Qbikz_ProjectBootstrap_Model_Installer_Category extends Qbikz_ProjectBootstrap_Model_Installer_Abstract
{
    /**
     * [$_filename description]
     * @var string
     */
    protected $_fileName = 'category';

    /**
     * [$paths description]
     * @var array
     */
    private $_paths = array();

    /**
     * [cleanup description]
     * @return [type] [description]
     */
    public function cleanup()
    {
        $db = $this->getConnection();
        # Delete all categories except the main node
        $db->delete(
            $this->getTable('catalog/category'),
            $db->quoteInto('entity_id >= ?', 2)
        );
        return $this;
    }

    /**
     * [install description]
     * @return [type] [description]
     */
    public function install()
    {
        $rows = $this->_getData();
        foreach ($rows as $row) {
            $row = array_merge(array(
                'is_anchor'                     => 1,
                'is_active'                     => 1,
                'include_in_menu'               => 1,
                'display_mode'                  => 'PRODUCTS',
                'landing_page'                  => '',
                'page_layout'                   => '',
                'custom_design'                 => '',
                'custom_layout_update'          => '',
                'custom_apply_to_products'      => 0,
                'custom_use_parent_settings'    => 0,
            ), array_filter($row));

            # Create the category instance
            $this->_createCategory($row);
        }
        return $this;
    }

    /**
     * [_createCategory description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    private function _createCategory(array $data)
    {
        # Set parent path
        $parent = $data['path'];
        # Fetch the name from the path
        $name   = array_pop($parent);
        # Create path strings
        $parent = implode('::', $parent);

        # Save the category
        $model = Mage::getModel('catalog/category')
            ->addData($data)
            ->setName($name)
            ->setPath($this->_getPath($parent))
            ->save();

        # Save the path of the category.
        $this->_paths[implode('::', $data['path'])] = $model->getPath();
    }

    /**
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    private function _getPath($path)
    {
        if (empty($path)) {
            return 1;
        }
        if (! isset($this->_paths[$path])) {
            Mage::throwException(sprintf('Path not found: %s', $path));
        }
        return $this->_paths[$path];
    }
}
