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
    'customer_attribute',
    'category_attribute',
    'product_attribute',
    'product_attributeset',
));

/**
 * End the setup procedure.
 */
$installer->endSetup();
