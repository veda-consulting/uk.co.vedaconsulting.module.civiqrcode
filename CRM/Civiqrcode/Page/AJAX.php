<?php

/**
* This class contains all contact related functions that are called using AJAX (jQuery)
*/

class CRM_Civiqrcode_Page_AJAX {
    static function deleteQrCode( ) {
        $id   = CRM_Utils_Request::retrieve( 'id', 'Integer', CRM_Core_DAO::$_nullArray );
        if ($id) {
            CRM_Core_DAO::executeQuery('DELETE FROM civicrm_qrcode_settings WHERE id = %1', array(1=>array($id, 'Integer')));
            echo 'deleted';
        }
        CRM_Utils_System::civiExit();
    } // End of funtion
}
