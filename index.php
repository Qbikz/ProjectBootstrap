<?php

# Determine developer mode
$isDeveloperMode = (isset($_SERVER['MAGE_IS_DEVELOPER_MODE']) && $_SERVER['MAGE_IS_DEVELOPER_MODE']);

# Set default values for developer mode
if ($isDeveloperMode) {
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_NONE);
    ini_set('display_errors', 0);
}

/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', __DIR__);

$mageFilename       = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile    = 'maintenance.flag';

# Check existence of Mage.php
if (! file_exists($mageFilename)) {
    echo $mageFilename." was not found";
    exit;
}

# Determine whether platform is in maintenace mode
if (file_exists($maintenanceFile)) {
    include_once MAGENTO_ROOT . '/errors/503.php';
    exit;
}

# Require Magento
require_once $mageFilename;

# Set developer mode
Mage::setIsDeveloperMode($isDeveloperMode);

# Check for enabled profiler
if (isset($_SERVER['MAGE_PROFILER'])) {
    Varien_Profiler::enable();
}

# Default run code
$mageRunCode = 'default';
if (isset($_SERVER['MAGE_RUN_CODE']) && $_SERVER['MAGE_RUN_CODE']) {
    $mageRunCode = $_SERVER['MAGE_RUN_CODE'];
}

# Default run type
$mageRunType = 'website';
if (isset($_SERVER['MAGE_RUN_TYPE']) && $_SERVER['MAGE_RUN_TYPE']) {
    $mageRunType = $_SERVER['MAGE_RUN_TYPE'];
}

# Run the application
Mage::run($mageRunCode, $mageRunType);
