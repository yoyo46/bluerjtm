<?php 
        $value = !empty($value)?$value:false;
        $idx = !empty($idx)?$idx:0;
        $options = array(
            'class' => 'text-right price_custom',
            'data-type' => 'input_price_coma',
        );

        $coa_id = $this->Common->filterEmptyField($value, 'GeneralLedgerDetail', 'coa_id');
        $debit = $this->Common->filterEmptyField($value, 'GeneralLedgerDetail', 'debit');
        $credit = $this->Common->filterEmptyField($value, 'GeneralLedgerDetail', 'credit');
        
        $debit = $this->Common->getFormatPrice($debit, 0, 2);
        $credit = $this->Common->getFormatPrice($credit, 0, 2);

        if( empty($idx) ) {
            $class = 'field-copy';
        } else {
            $class = '';
        }

        $attr = array(
            'class' => sprintf('%s pick-document item', $class),
            'data-tag' => 'tr',
        );

        echo $this->Html->tableCells(array(
            array(
                $this->Common->_callInputForm('GeneralLedgerDetail.coa_id.', array(
                    'empty' => __('Pilih COA'),
                    'options' => $coas,
                    'class' => 'chosen-select form-control',
                    'value' => $coa_id,
                    'fieldError' => __('GeneralLedgerDetail.%s.coa_id', $idx),
                )),
                $this->Common->_callInputForm('GeneralLedgerDetail.debit.', array_merge($options, array(
                    'rel' => 'debit',
                    'value' => $debit,
                    'fieldError' => __('GeneralLedgerDetail.%s.debit', $idx),
                ))),
                $this->Common->_callInputForm('GeneralLedgerDetail.credit.', array_merge($options, array(
                    'rel' => 'credit',
                    'value' => $credit,
                    'fieldError' => __('GeneralLedgerDetail.%s.credit', $idx),
                ))),
                $this->Html->link($this->Common->icon('times'), '#', array(
                    'class' => 'removed btn btn-danger btn-xs',
                    'escape' => false,
                    'data-eval' => 'calcGrandTotalCustom()',
                )),
            ),
        ), $attr, $attr);
?>