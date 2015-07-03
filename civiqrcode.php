<?php

require_once 'civiqrcode.civix.php';
define('QRCODE_SETTING_DB_TABLENAME'            , 'civicrm_qrcode_settings');
define('QRCODE_SETTING_DB_COLUMN_QRCODE_TOKEN'  , 'qrcode_token_name');
define('QRCODE_SETTING_DB_COLUMN_QRCODE_TARGET' , 'qrcode_target_url');
define('QRCODE_SETTING_DB_COLUMN_QRCODE_ARG_EXT', 'arg_externalid');
define('QRCODE_SETTING_DB_COLUMN_QRCODE_ARG_CS' , 'arg_checksum');
/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civiqrcode_civicrm_config(&$config) {
  _civiqrcode_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function civiqrcode_civicrm_xmlMenu(&$files) {
  _civiqrcode_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civiqrcode_civicrm_install() {
  $tableName = QRCODE_SETTING_DB_TABLENAME;
  $qrToken   = QRCODE_SETTING_DB_COLUMN_QRCODE_TOKEN;
  $qrTarget  = QRCODE_SETTING_DB_COLUMN_QRCODE_TARGET;
  $argExt    = QRCODE_SETTING_DB_COLUMN_QRCODE_ARG_EXT;
  $argCs     = QRCODE_SETTING_DB_COLUMN_QRCODE_ARG_CS;
  
  //Create custom table to manage the QRCode settings
  $query = "CREATE TABLE IF NOT EXISTS {$tableName} (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            {$qrToken} varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, 
            {$qrTarget} varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
            {$argExt} tinyint(4) DEFAULT '1',
            {$argCs} tinyint(4) DEFAULT '1',
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_entity_id` ({$qrToken})
            ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
  CRM_Core_DAO::executeQuery( $query );
  
  _civiqrcode_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function civiqrcode_civicrm_uninstall() {
  //check table exists and drop while uninstall the extension
  $tableName = QRCODE_SETTING_DB_TABLENAME;
  CRM_Core_DAO::executeQuery("DROP TABLE IF EXISTS {$tableName}");
  _civiqrcode_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civiqrcode_civicrm_enable() {
  _civiqrcode_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function civiqrcode_civicrm_disable() {
  _civiqrcode_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function civiqrcode_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civiqrcode_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function civiqrcode_civicrm_managed(&$entities) {
  _civiqrcode_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civiqrcode_civicrm_caseTypes(&$caseTypes) {
  _civiqrcode_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function civiqrcode_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civiqrcode_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function civiqrcode_civicrm_navigationMenu( &$params ) {
  //get maxID
  $maxKey = max(array_keys($params)); 
  
  //set navigation Id 
  $navId = $maxKey+1;
  
  //set navigation menu
  $parentId       = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Administer', 'id', 'name');
  $params[$parentId]['child'][$navId] = array(
    'attributes' => array (
      'label'      => 'QRCode Token Settings',
      'name'       => 'QRCode_Token_Settings',
      'url'        => 'civicrm/view/qrcodesetting?reset=1',
      'permission' => 'administer CiviCRM',
      'operator'   => null,
      'separator'  => null,
      'parentID'   => $parentId,
      'navID'      => $navId,
      'active'     => 1,
    ),
  );
}


function civiqrcode_civicrm_tokens( &$tokens ) {
  $tableName = QRCODE_SETTING_DB_TABLENAME;
  $qrToken   = QRCODE_SETTING_DB_COLUMN_QRCODE_TOKEN;
  $getAllQrCodeTokenDAO = CRM_Core_DAO::executeQuery("SELECT {$qrToken} as qrtoken FROM {$tableName}");
  while ($getAllQrCodeTokenDAO->fetch()) {
    $tokens['contact']['civiqrcode.'.$getAllQrCodeTokenDAO->qrtoken] =  ts("QR Code - {$getAllQrCodeTokenDAO->qrtoken}");
  }
}

function civiqrcode_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
  if ((array_key_exists('contact', $tokens))) {
    $config = CRM_Core_Config::singleton();

    $imageUploadDir = $config->imageUploadDir;
    $extensionDir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
    $qrlibFile    = $extensionDir .'/lib/phpqrcode/phpqrcode.php';
    
    $tableName = QRCODE_SETTING_DB_TABLENAME;
    $qrToken   = QRCODE_SETTING_DB_COLUMN_QRCODE_TOKEN;
    $qrTarget  = QRCODE_SETTING_DB_COLUMN_QRCODE_TARGET;
    $argExt    = QRCODE_SETTING_DB_COLUMN_QRCODE_ARG_EXT;
    $argCs     = QRCODE_SETTING_DB_COLUMN_QRCODE_ARG_CS;
    
    $getAllQrCodeTokenDAO = CRM_Core_DAO::executeQuery("SELECT * FROM {$tableName}");
  
    foreach($cids as $id){
      
      while ($getAllQrCodeTokenDAO->fetch()) {
        $filename = $getAllQrCodeTokenDAO->$qrToken.'_'.$id.date('dmy').'.png';
        $pngAbsoluteFilePath = $imageUploadDir.$filename;

        //delete if the filename exists
        if (file_exists($pngAbsoluteFilePath)) { 
          unlink($pngAbsoluteFilePath);
        }
        
        $urlParams = array();
        $urlParams['cid'] = $id;
        $urlParams['reset'] = 1;
        
        if ($getAllQrCodeTokenDAO->$argExt) {
          $urlParams['mid'] =  CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $id, 'external_identifier', 'id');
        }
        if ($getAllQrCodeTokenDAO->$argCs) {
          $urlParams['cs'] =  CRM_Contact_BAO_Contact_Utils::generateChecksum($id);
        }
          
        $url = CRM_Utils_System::url($getAllQrCodeTokenDAO->$qrTarget, $urlParams, TRUE);
        require_once $qrlibFile;
        QRcode::png($url, $pngAbsoluteFilePath, 'L', 4, 2);
        
        $values[$id]['civiqrcode.'.$getAllQrCodeTokenDAO->$qrToken] = realpath($pngAbsoluteFilePath);
      }//end while
    }//end foreach
  }//end if
}
