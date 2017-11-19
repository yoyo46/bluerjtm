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

    function _callLeadTime ( $value, $type = 'arrive', $empty = '-' ) {
        $day = $this->Common->filterEmptyField($value, 'Ttuj', sprintf('%s_leadtime_day', $type));
        $hour = $this->Common->filterEmptyField($value, 'Ttuj', sprintf('%s_leadtime_hour', $type));
        $minute = $this->Common->filterEmptyField($value, 'Ttuj', sprintf('%s_leadtime_minute', $type));
        $result = array();

        if( !empty($day) ) {
        	$result[] = __('%s Hari', $day);
        }
        if( !empty($hour) ) {
        	$result[] = __('%s Jam', $hour);
        }
        if( !empty($minute) ) {
        	$result[] = __('%s Menit', $minute);
        }

        if( !empty($result) ) {
        	return implode('<br>', $result);
        } else {
        	return $empty;
        }
    }
}