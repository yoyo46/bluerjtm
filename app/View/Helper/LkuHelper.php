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

        if(!empty($completed)){
            $customStatus = $this->Html->tag('span', __('Closing'), array(
                'class' => 'label label-success',
            ));
        } else if(!empty($paid)){
            if(!empty($complete_paid)){
                $customStatus = $this->Html->tag('span', __('Lunas'), array(
                    'class' => 'label label-success',
                ));
            }else{
                $customStatus = $this->Html->tag('span', __('Dibayar Sebagian'), array(
                    'class' => 'label label-info',
                ));
            }
        } else{
            if(!empty($status)){
                $customStatus = $this->Html->tag('span', __('Pending'), array(
                    'class' => 'label label-default',
                ));
            } else{
                $customStatus = $this->Html->tag('span', __('Non-aktif'), array(
                    'class' => 'label label-danger',
                ));
            }
        }

        return $customStatus;
    }
}