<?php
class TtujHelper extends AppHelper {
	var $helpers = array(
        'Common', 'Html'
    );

    function _callDariTujuan ( $value, $separator = '-' ) {
        $from_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
        $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
        
        return __('%s %s %s', $from_city_name, $separator, $to_city_name);
    }
}