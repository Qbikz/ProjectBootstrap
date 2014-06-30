<?php

abstract class Qbikz_ProjectBootstrap_Model_Installer_Abstract extends Mage_Eav_Model_Entity_Setup
{
    /**
     * [$_additionalArrays description]
     * @var array
     */
    protected $_additional = array();

    /**
     * [$_fileName description]
     * @var string
     */
    protected $_fileName;

    /**
     * [$_identifier description]
     * @var string
     */
    protected $_identifier;

    /**
     * [$_templateScope description]
     * @var string
     */
    protected $_templateScope;

    /**
     * Override parent constructor to allow easy inheritance.
     */
    public function __construct($resourceName = null)
    {
        if ($resourceName) {
            parent::__construct($resourceName);
        } else {
            parent::__construct('projectbootstrap_setup');
        }
    }

    /**
     * [cleanup description]
     * @return [type] [description]
     */
    abstract public function cleanup();

    /**
     * [install description]
     * @return [type] [description]
     */
    abstract public function install();

    /**
     * Prepare catalog attribute values to save
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);
        $data = array_merge($data, array(
            'frontend_input_renderer'       => $this->_getValue($attr, 'input_renderer'),
            'is_global'                     => $this->_getValue(
                $attr,
                'global',
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
            ),
            'is_visible'                    => $this->_getValue($attr, 'visible', 1),
            'is_searchable'                 => $this->_getValue($attr, 'searchable', 0),
            'is_filterable'                 => $this->_getValue($attr, 'filterable', 0),
            'is_comparable'                 => $this->_getValue($attr, 'comparable', 0),
            'is_visible_on_front'           => $this->_getValue($attr, 'visible_on_front', 0),
            'is_wysiwyg_enabled'            => $this->_getValue($attr, 'wysiwyg_enabled', 0),
            'is_html_allowed_on_front'      => $this->_getValue($attr, 'is_html_allowed_on_front', 0),
            'is_visible_in_advanced_search' => $this->_getValue($attr, 'visible_in_advanced_search', 0),
            'is_filterable_in_search'       => $this->_getValue($attr, 'filterable_in_search', 0),
            'used_in_product_listing'       => $this->_getValue($attr, 'used_in_product_listing', 0),
            'used_for_sort_by'              => $this->_getValue($attr, 'used_for_sort_by', 0),
            'apply_to'                      => $this->_getValue($attr, 'apply_to'),
            'position'                      => $this->_getValue($attr, 'position', 0),
            'is_configurable'               => $this->_getValue($attr, 'is_configurable', 1),
            'is_used_for_promo_rules'       => $this->_getValue($attr, 'used_for_promo_rules', 0)
        ));
        return $data;
    }

    /**
     * Creates a new text attribute for categories, sets some
     * default values.
     *
     * @param   string  $attributeCode      The attribute code to use.
     * @param   array   $attributeOptions   Optional parameter to change attribute options.
     * @return  mixed                       Mixed return from parent addAttribute method.
     */
    protected function _addEavAttribute($type, $attributeCode, array $attributeOptions = array())
    {
        $entityTypeId = $this->getEntityTypeId($type);
        if (! $entityTypeId) {
            Mage::throwException(sprintf('Invalid type specified: %s', $type));
        }
        if (isset($attributeOptions['options'])) {
            $attributeOptions['option']['values'] = $attributeOptions['options'];
        }
        return $this->addAttribute($entityTypeId, $attributeCode, array_merge(array(
            'type'              => 'varchar',
            'backend'           => '',
            'frontend'          => '',
            'label'             => $attributeCode,
            'input'             => 'text',
            'global'            => 0,
            'visible'           => 1,
            'required'          => 0,
            'default'           => '',
            'visible_on_front'  => 0,
            'user_defined'      => 1,
        ), $attributeOptions));
    }

    /**
     * Returns the data from the CSV file.
     *
     * @return [type] [description]
     */
    protected function _getData($fileName = null, $identifier = null, $additional = array())
    {
        if (null === $fileName) {
            $fileName = $this->_fileName;
        }
        if (null === $identifier) {
            $identifier = $this->_identifier;
        }
        if (empty($additional)) {
            $additional = $this->_additional;
        }
        if (null === $fileName) {
            Mage::throwException(sprintf('Filename not set for installer: %s', get_class($this)));
        }
        try {
            $rows = Mage::helper('projectbootstrap')->loadCsv(
                sprintf('%s/var/import/%s.csv', BP, $fileName),
                $additional,
                $identifier
            );
        } catch (Exception $e) {
            throw $e;
        }
        return $rows;
    }

    /**
     * Returns the content of a template file. Template files can be used to provide static
     * HTML templates instead of defining content in the CSV file.
     *
     * @return [type] [description]
     */
    protected function _getContent($content, $template)
    {
        if (! empty($template)) {
            $template = $this->_readTemplate($template);
            if (null !== $template) {
                return str_replace('%%%CONTENT%%%', $content, $template);
            }
        }
        return $content;
    }

    /**
     * [_getStoreIds description]
     * @param  array  $stores [description]
     * @return [type]         [description]
     */
    protected function _getStoreIds(array $stores)
    {
        if (empty($this->_storeIds)) {
            $db = $this->getConnection();
            $this->_storeIds = $db->fetchPairs(
                $db->select()
                    ->from($this->getTable('core/store'), array('code', 'store_id'))
            );
        }
        $result = array();
        foreach ($stores as $store) {
            if (isset($this->_storeIds[$store])) {
                $result[] = $this->_storeIds[$store];
            }
        }
        return ! empty($result)
             ? $result
             : array(0);
    }

    /**
     * [_readTemplate description]
     * @return [type] [description]
     */
    protected function _readTemplate($fileName)
    {
        if (is_string($this->_templateScope)) {
            $fileName = BP . '/var/import/template/' . $this->_templateScope . '/' . $fileName . '.html';
        } else {
            $fileName = BP . '/var/import/template/' . $fileName . '.html';
        }
        return file_exists($fileName) ? trim(file_get_contents($fileName)) : null;
    }
}
