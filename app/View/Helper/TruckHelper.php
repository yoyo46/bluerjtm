<?php
class TruckHelper extends AppHelper {
	var $helpers = array(
        'Common',
    );

    function _callDocumentType ( $type ) {
        switch ($type) {
            case 'stnk':
                $modelName = 'Stnk';
                break;
            case 'stnk_5_thn':
                $modelName = 'Stnk';
                break;
            
            default:
                $modelName = ucwords($type);
                break;
        }

        return $modelName;
    }
}