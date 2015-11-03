<?php
class PurchaseHelper extends AppHelper {
	var $helpers = array(
        'Html', 'Common',
    );

    function _callStatusQuotation ( $data ) {
        $status = $this->Common->filterEmptyField($data, 'SupplierQuotation', 'status');

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
}