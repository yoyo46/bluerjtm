<?php 
        if( !empty($customer) ) {
            $customer_id = !empty($customer['CustomerNoType']['id'])?$customer['CustomerNoType']['id']:false;
            $customer_name = !empty($customer['CustomerNoType']['name'])?$customer['CustomerNoType']['name']:false;
            $model = !empty($model)?$model:'Customer';

            if( !empty($prepayment_id) && !empty($customer['CashBankDetail']) ) {
                $contentTr = '';

                foreach ($customer['CashBankDetail'] as $key => $cashBankDetail) {
                    $coa_id = !empty($cashBankDetail['Coa']['id'])?$cashBankDetail['Coa']['id']:false;
                    $coa_code = !empty($cashBankDetail['Coa']['code'])?$cashBankDetail['Coa']['code']:false;
                    $coa_name = !empty($cashBankDetail['Coa']['name'])?$cashBankDetail['Coa']['name']:false;
                    $total = !empty($cashBankDetail['total'])?$cashBankDetail['total']:false;
                    // $content = $this->Html->tag('td', $coa_code.$this->Form->input('CashBankDetail.coa_id.',array(
                    //     'type'=> 'hidden', 
                    //     'value' => $coa_id,
                    // )));
                    // $content .= $this->Html->tag('td', $coa_name);
                    // $content .= $this->Html->tag('td', $coa_code.$this->Form->input('CashBankDetail.total.',array(
                    //     'label' => false,
                    //     'div' => false,
                    //     'class'=> 'form-control input_price', 
                    //     'value' => $total,
                    // )), array(
                    //     'class' => 'action-search',
                    // ));
                    // $content .= $this->Html->tag('td', $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                    //     'class' => 'delete-custom-field btn btn-danger btn-xs',
                    //     'action_type' => 'cashbank_first',
                    // )), array(
                    //     'class' => 'action-search',
                    // ));
                    // $contentTr .= $this->Html->tag('tr', $content, array(
                    //     'class' => 'child child-'.$coa_id,
                    //     'rel' => $coa_id,
                    // ));
                    $contentTr .= '<opentr class="child child-'.$coa_id.'" rel="'.$coa_id.'">
                        <opentd>
                            '.$coa_code.'
                            <input type="hidden" name="data[CashBankDetail][coa_id][]" value="'.$coa_id.'" id="CashBankDetailCoaId">
                        <closetd>
                        <opentd>
                            '.$coa_name.'
                        <closetd>
                        <opentd class="action-search">
                            <input name="data[CashBankDetail][total][]" class="form-control input_price" type="text" id="CashBankDetailTotal" value="'.$total.'">
                        <closetd>
                        <opentd class="action-search">
                            <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="cashbank_first"><i class="fa fa-times"></i> Hapus</a>
                        <closetd>
                    <closetr>';
                }

                echo $this->Html->tag('div', $contentTr, array(
                    'id' => 'content-table',
                ));
            } else {
                $coa_code = !empty($coaSetting['Coa']['code'])?$coaSetting['Coa']['code']:false;
                $coa_id = !empty($coaSetting['Coa']['id'])?$coaSetting['Coa']['id']:false;
                $coa_name = !empty($coaSetting['Coa']['name'])?$coaSetting['Coa']['name']:false;
                $ppn = !empty($customer['Revenue']['ppn'])?$customer['Revenue']['ppn']:0;
                $total = !empty($customer['Revenue']['total_without_tax'])?$customer['Revenue']['total_without_tax']:0;
                $ppn = $this->Common->calcFloat($total, $ppn);

                echo $this->Html->tag('div', $coa_code, array(
                    'id' => 'coa_code',
                ));
                echo $this->Html->tag('div', $coa_id, array(
                    'id' => 'coa_id',
                ));
                echo $this->Html->tag('div', $coa_name, array(
                    'id' => 'coa_name',
                ));
                echo $this->Html->tag('div', $ppn, array(
                    'id' => 'ppn',
                ));
            }

            echo $this->Html->tag('div', $customer_id, array(
                'id' => 'customer-id',
            ));
            echo $this->Html->tag('div', $customer_name, array(
                'id' => 'customer-name',
            ));
            echo $this->Html->tag('div', $model, array(
                'id' => 'receiver-type',
            ));
        }
?>