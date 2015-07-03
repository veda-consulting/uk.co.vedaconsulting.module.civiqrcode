<?php

require_once 'CRM/Core/Form.php';

class CRM_Civiqrcode_Form_QRCodeSettings extends CRM_Core_Form {
  function preProcess() {
    parent::preProcess();  
  }
  
  
  static function getQrDetails( $id = NULL ) {
    
    $tableName = QRCODE_SETTING_DB_TABLENAME;
    $existingQrcodeTokens = array();
    $query = "SELECT * FROM $tableName";
    if ($id && is_int($id)) {
      $query .= " WHERE id = {$id}";
    }
    return CRM_Core_DAO::executeQuery($query);
  }
  
  function setDefaultValues() {
    $defaults = $details = array();
    $id = CRM_Utils_Request::retrieve( 'id', 'Integer', $this );

    $QrCodeDAO = array();
    if ($id) {
      $qrToken   = QRCODE_SETTING_DB_COLUMN_QRCODE_TOKEN;
      $qrTarget  = QRCODE_SETTING_DB_COLUMN_QRCODE_TARGET;
      $argExt    = QRCODE_SETTING_DB_COLUMN_QRCODE_ARG_EXT;
      $argCs     = QRCODE_SETTING_DB_COLUMN_QRCODE_ARG_CS;      
      $QrCodeDAO = self::getQrDetails($id);
      if ($QrCodeDAO->fetch()) {
        $defaults['qr_token_name']    = $QrCodeDAO->$qrToken;
        $defaults['qr_target_url']    = $QrCodeDAO->$qrTarget;
        $defaults['arg_externalid']   = $QrCodeDAO->$argExt;
        $defaults['arg_checksum']     = $QrCodeDAO->$argCs;
      }
    }
    
    return $defaults;
  }
  
  function buildQuickForm() {
    //token name 
    $this->add('text', 'qr_token_name', ts('QRCode Token Name'), array('maxlength' => 100), TRUE );
    
    //QRcode target url
    $this->add('text', 'qr_target_url', ts('QRCode Target URL'), array('size' => 50), TRUE );
    
    //Include checksum in URL
    $this->add('checkbox', 'arg_checksum', ts('Include Checksum'), '', FALSE, array('checked' => 'checked'));
    
    //Include membership id in URL
    $this->add('checkbox', 'arg_externalid', ts('Include External ID'), '', FALSE, array('checked' => 'checked'));
    
    //add Form Rule.
    $this->addFormRule(array('CRM_Civiqrcode_Form_QRCodeSettings', 'formRule'), $this);
    
    $this->add('hidden', 'action', CRM_Utils_Request::retrieve( 'action', 'Integer', $this ));
    $this->add('hidden', 'id', CRM_Utils_Request::retrieve( 'id', 'Integer', $this ));
        
    //Submit buttons
    $this->addButtons(array(
      array('type' => 'submit', 'name' => ts('Submit'), 'isDefault' => TRUE, ),
      array('type' => 'cancel', 'name' => ts('Cancel'), ),
    ));
    
    $helpText = array(
      'qr_token_name' => 'Unique token name.',
      'qr_target_url' => 'Add the target URL of the QRcode. Doesn\'t need to add URL params inside the URL For ex: add "civicrm/contact/view" to set target to view contact page. cid should be add by default in URL',
      'arg_externalid' => 'Please Select checkbox if external_id need to be added into URL. NOTE: external_id should be added as "mid" in URL',
      'arg_checksum' => 'Please Select checkbox if checksum need to be added into URL. checksum used for validation of the contact',
    );
    
    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    $this->assign('helpText', $helpText);
    $this->assign('viewLink', CRM_Utils_System::url('civicrm/view/qrcodesetting', 'reset=1'));
    parent::buildQuickForm();
  }
  
  /**
   * global validation rules for the form
   *
   * @param array $fields posted values of the form
   *
   * @return array list of errors to be posted back to the form
   * @static
   * @access public
   */
  static function formRule( $values, $form ) {
    $errors = array( );
    $tableName = QRCODE_SETTING_DB_TABLENAME;
    $qrToken   = QRCODE_SETTING_DB_COLUMN_QRCODE_TOKEN;
    if ($values['qr_token_name'] && empty($values['action']) && empty($values['id'])) {
      $sql = "SELECT count(*) FROM {$tableName} WHERE {$qrToken} = %1";
      $checkTokenExist = CRM_Core_DAO::singleValueQuery($sql, array(1=>array($values['qr_token_name'], 'String')));
      if ($checkTokenExist) {
        $errors['qr_token_name'] = "Token Name Already Exists..";
      }
    }
    return $errors;
  } 
    
  function postProcess() {
    $values = $this->exportValues();
    
    $tableName = QRCODE_SETTING_DB_TABLENAME;
    $qrToken   = QRCODE_SETTING_DB_COLUMN_QRCODE_TOKEN;
    $qrTarget  = QRCODE_SETTING_DB_COLUMN_QRCODE_TARGET;
    $argExt    = QRCODE_SETTING_DB_COLUMN_QRCODE_ARG_EXT;
    $argCs     = QRCODE_SETTING_DB_COLUMN_QRCODE_ARG_CS;
    //Update QRCode settings
    $query = "INSERT INTO {$tableName} (
              {$qrToken},
              {$qrTarget},
              {$argExt},
              {$argCs}
              ) 
              VALUES ( %1, %2, %3, %4 )
              ON DUPLICATE KEY UPDATE 
              {$qrToken}  = %1,
              {$qrTarget} = %2,
              {$argExt}   = %3,
              {$argCs}    = %4
              ";

    $querParams = array(
      1 => array($values['qr_token_name'], 'String'),
      2 => array($values['qr_target_url'], 'String'),
      3 => array(isset($values['arg_externalid']) ? $values['arg_externalid'] : 0, 'Integer'),
      4 => array(isset($values['arg_checksum']) ? $values['arg_checksum'] : 0, 'Integer'),
    );
    CRM_Core_DAO::executeQuery( $query, $querParams );
    $urlArguments = array(
      'reset' => 1,
    );
    
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/view/qrcodesetting', $urlArguments));
    parent::postProcess();
  }
  
  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
