<?php
class SpkHelper extends AppHelper {
	var $helpers = array(
        'Common', 'Html'
    );

    function _callDisplayToggle ( $type, $value ) {
        $document_type = $this->Common->filterEmptyField($value, 'Spk', 'document_type');
        $result = '';
        
        switch ($type) {
            case 'mechanic':
                if( !in_array($document_type, array( 'internal', 'production' )) ) {
                    $result = 'hide';
                }
                break;
            case 'wht':
                if( !in_array($document_type, array( 'wht' )) ) {
                    $result = 'hide';
                }
                break;
            case 'eksternal':
                if( !in_array($document_type, array( 'eksternal' )) ) {
                    $result = 'hide';
                }
                break;
            case 'production':
                if( !in_array($document_type, array( 'production' )) ) {
                    $result = 'hide';
                }
                break;
        }

        return $result;
    }
}