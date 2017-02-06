<?php
class ReportHelper extends AppHelper {
	var $helpers = array(
		'Common', 'Html', 'Number',
		'Session'
	);

	function _callLabelName( $value ) {
        $region = $this->Common->filterEmptyField($value, 'Region', 'name');
        $city = $this->Common->filterEmptyField($value, 'City', 'name');
        $_type = $this->Common->filterEmptyField($value, 'PropertyType', 'name');
        $_action = $this->Common->filterEmptyField($value, 'PropertyAction', 'name');
        $result = false;

		if( !empty($_type) ) {
        	$result = $_type;
		} else {
        	$result = __('Semua Properti');
		}

		if( !empty($_action) ) {
        	$result .= '&nbsp;'.$_action;
		}

		if( !empty($city) ) {
        	$result .= __(', di %s', $city);
		}

		if( !empty($region) ) {
        	$result .= __(', %s', $region);
		}

		return $result;
	}

	function _callSpec ( $value, $options = array() ) {
        $empty = $this->Common->filterEmptyField($options, 'empty');
        $divider = $this->Common->filterEmptyField($options, 'divider', false, ', ', false);
        $tag = $this->Common->filterEmptyField($options, 'tag');

        $spec = $this->Common->filterEmptyField($value, 'Specification');
        $result = array();

        if( !empty($spec) ) {
        	foreach ($spec as $label => $val) {
        		if( !empty($tag) ) {
        			$label = $this->Html->tag($tag, $label);
        		}

    			$result[] = __('%s: %s', $label, $val);
        	}

        	$result = implode($divider, $result);
        }

        if( !empty($result) ) {
        	return $result;
        } else {
        	return $empty;
        }
	}

    function _callActions ( $value, $action_detail = 'detail' ) {
        $id = $this->Common->filterEmptyField($value, 'Report', 'id');
        $document_status = $this->Common->filterEmptyField($value, 'Report', 'document_status');

        $actions = array(
            $this->Html->link(
                $this->Common->icon('rv4-magnify'), array(
                'controller' => 'reports',
                'action' => $action_detail,
                $id,
                'admin' => true,
            ), array(
                'escape' => false,
                'title' => __('Lihat Detil'),
            )),
        );

        if($document_status == 'completed') {
            $actions[] = $this->Html->link(
                $this->Common->icon('rv4-download'), array(
                'controller' => 'reports',
                'action' => 'download',
                $id,
                'admin' => true,
            ), array(
                'escape' => false,
                'title' => __('Unduh Laporan'),
            ));
        }

        // $actions[] = $this->Html->link(
        //     $this->Common->icon('rv4-trash'), array(
        //     'controller' => 'reports',
        //     'action' => 'delete',
        //     $id,
        //     'admin' => true,
        // ), array(
        //     'escape' => false,
        //     'title' => __('Hapus'),
        // ), __('Anda yakin ingin menghapus laporan ini?'));

        return $actions;
    }

    function _callDetail ( $value ) {
        $details = $this->Common->filterEmptyField($value, 'ReportDetail');
        $result = false;

        if( !empty($details) ) {
            $contentLi = false;

            foreach ($details as $key => $detail) {
                $titleField = $this->Common->filterEmptyField($detail, 'ReportDetail', 'title');
                $value_name = $this->Common->filterEmptyField($detail, 'ReportDetail', 'value_name');
                $titleField = str_replace('_', ' ', $titleField);

                if( is_array($value_name) ) {
                    $value_name = implode(', ', $value_name);
                }

                $contentLi .= $this->Html->tag('li', __('%s: %s', $titleField, $this->Html->tag('strong', $value_name)));
            }

            $result = $this->Html->tag('ul', $contentLi);
        }

        return $result;
    }

    function _callAccessDownload ( $value ) {
        $type = $this->Common->filterEmptyField($value, 'Report', 'report_type_id');
        $group_by = Set::extract('/ReportDetail/ReportDetail/field', $value);
        $admin_rumahku = Configure::read('User.Admin.Rumahku');

        if( empty($admin_rumahku) || in_array($type, array( 'performance', 'summary', 'kprs' )) ) {
            return true;
        } else {
            return false;
        }
    }

    function _callPercentage ( $entry, $total ) {
        if( !empty($entry) ) {
            $percent = ( $entry / $total ) * 100;

            if( $percent > 100 ) {
                $percent = 100;
            }

            return $this->Common->getFormatPrice($percent, 0, 0);
        } else {
            return 0;
        }
    }
}