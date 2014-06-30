<?php

/* @var $installer Qbikz_ProjectBootstrap_Model_Setup */
$installer = $this;

/**
 * Start the setup procedure.
 */
$installer->startSetup();

/**
 * Start import
 */
$this->setupModules(array(
    'website',
    'tax_class',
    'tax_rate',
    'tax_rule',
    'cms_block',
    'cms_page',
    'category',
    'website_category',
    'customer_group',
    'customer',
));

/**
 * End the setup procedure.
 */
$installer->endSetup();
