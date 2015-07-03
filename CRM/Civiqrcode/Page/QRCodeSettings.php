<?php

require_once 'CRM/Core/Page.php';

class CRM_Civiqrcode_Page_QRCodeSettings extends CRM_Core_Page {
  function run() {
    $qrToken   = QRCODE_SETTING_DB_COLUMN_QRCODE_TOKEN;
    $qrTarget  = QRCODE_SETTING_DB_COLUMN_QRCODE_TARGET;
    $argExt    = QRCODE_SETTING_DB_COLUMN_QRCODE_ARG_EXT;
    $argCs     = QRCODE_SETTING_DB_COLUMN_QRCODE_ARG_CS;
    $QrCodeDAO = CRM_Civiqrcode_Form_QRCodeSettings::getQrDetails();
    $existingQrcodeTokens = array();
    while ($QrCodeDAO->fetch()) {
      $url = CRM_Utils_System::url('civicrm/admin/form/qrcodesetting', 'reset=1&action=update&id='.$QrCodeDAO->id, TRUE);
      $existingQrcodeTokens[$QrCodeDAO->id] = array(
        'qr_token_name'     => $QrCodeDAO->$qrToken,
        'qr_target_url'     => $QrCodeDAO->$qrTarget,
        'arg_externalid'    => $QrCodeDAO->$argExt,
        'arg_checksum'      => $QrCodeDAO->$argCs,
        'token_replace'     => "[CiviCRM.civiqrcode.{$QrCodeDAO->$qrToken};block=w:image;ope=changepic]",
        'action'            => sprintf("<span><a href='%s'>Edit</a></span>&nbsp;
                                        <span><a href='javascript:void(0)' onclick='delQrCode(%d);'>Delete</a></span>", 
                                $url, $QrCodeDAO->id
                                ),
      );
    }
    
    if (!CRM_Utils_Array::crmIsEmptyArray($existingQrcodeTokens)) {
      $this->assign('existingQrcodeTokens', $existingQrcodeTokens);
    }
    
    $this->assign('addone', CRM_Utils_System::url('civicrm/admin/form/qrcodesetting', 'reset=1'));
    
    parent::run();
  }
}
