<?php
class LkuHelper extends AppHelper {
	var $helpers = array(
        'Common', 'Html'
    );

    function getCheckStatus ( $data, $modelName ) {
        $status = $this->Common->filterEmptyField($data, $modelName, 'status');
        $paid = $this->Common->filterEmptyField($data, $modelName, 'paid');
        $complete_paid = $this->Common->filterEmptyField($data, $modelName, 'complete_paid');
        $kekurangan_atpm = $this->Common->filterEmptyField($data, $modelName, 'kekurangan_atpm');
        $completed = $this->Common->filterEmptyField($data, $modelName, 'completed');
        $customStatus = '-';

        if( !empty($completed) || !empty($complete_paid) ) {
            $customStatus = $this->Html->tag('span', __('Selesai'), array(
                'class' => 'label label-success',
            ));
        } else {
            $customStatus = $this->Html->tag('span', __('Belum'), array(
                'class' => 'label label-default',
            ));
        }

        return $customStatus;
    }
}