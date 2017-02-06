<?php
class PurchaseHelper extends AppHelper {
	var $helpers = array(
        'Html', 'Common',
    );

    function _callStatusSQ ( $data ) {
        $status = $this->Common->filterEmptyField($data, 'SupplierQuotation', 'status');
        $is_po = $this->Common->filterEmptyField($data, 'SupplierQuotation', 'is_po');

        if( !empty($status) ) {
            if( !empty($is_po) ) {
                $customStatus = $this->Html->tag('span', __('PO'), array(
                    'class' => 'label label-success',
                ));
            } else {
                $customStatus = $this->Html->tag('span', __('Aktif'), array(
                    'class' => 'label label-primary',
                ));
            }
        } else {
            $customStatus = $this->Html->tag('span', __('Non-Aktif'), array(
                'class' => 'label label-danger',
            ));
        }

        return $customStatus;
    }

    function _callStatus ( $data, $modelName = 'SupplierQuotation' ) {
        $status = $this->Common->filterEmptyField($data, $modelName, 'status');

        if( !empty($status) ) {
            $customStatus = $this->Html->tag('span', __('Aktif'), array(
                'class' => 'label label-success',
            ));
        } else {
            $customStatus = $this->Html->tag('span', __('Non-Aktif'), array(
                'class' => 'label label-danger',
            ));
        }

        return $customStatus;
    }

    function _callStatusDocument ( $data, $modelName = 'SupplierQuotation' ) {
        $status_document = $this->Common->filterEmptyField($data, $modelName, 'status_document');

        switch ($status_document) {
            case 'paid':
                $customStatus = $this->Html->tag('span', __('Sudah Dibayar'), array(
                    'class' => 'label label-success',
                ));
                break;

            case 'half_paid':
                $customStatus = $this->Html->tag('span', __('Dibayar Sebagian'), array(
                    'class' => 'label label-primary',
                ));
                break;

            case 'void':
                $customStatus = $this->Html->tag('span', __('Void'), array(
                    'class' => 'label label-danger',
                ));
                break;

            case 'posting':
                $customStatus = $this->Html->tag('span', __('Commit'), array(
                    'class' => 'label label-success',
                ));
                break;

            case 'unposting':
                $customStatus = $this->Html->tag('span', __('Draft'), array(
                    'class' => 'label label-default',
                ));
                break;
            
            default:
                $customStatus = $this->Html->tag('span', __('Belum Dibayar'), array(
                    'class' => 'label label-default',
                ));
                break;
        }

        return $customStatus;
    }

    function calculate ( $value, $ppn_include = false, $modelName = 'PurchaseOrderDetail' ) {
        $price = $this->Common->filterEmptyField($value, $modelName, 'price');
        $qty = $this->Common->filterEmptyField($value, $modelName, 'qty');
        $disc = $this->Common->filterEmptyField($value, $modelName, 'disc');
        $ppn = $this->Common->filterEmptyField($value, $modelName, 'ppn');

        $total = ( $price * $qty ) - $disc;

        if( empty($ppn_include) ) {
            $total += $ppn;
        }

        return $total;
    }

    public function _callDisabledNoSq($is_sq = null, $sq_id = null) {
        if( !empty($is_sq) && empty($sq_id) ) {
            $disabled = true;
        } else {
            $disabled = false;
        }

        return $disabled;
    }
}