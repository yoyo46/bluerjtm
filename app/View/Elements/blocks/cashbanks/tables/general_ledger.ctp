<?php 
        $coas = !empty($coas)?$coas:false;
        $data = $this->request->data;
        $dataDetail = $this->Common->filterEmptyField($data, 'GeneralLedgerDetail');

		$dataColumns = array(
            'coa' => array(
                'name' => __('COA'),
                'class' => 'text-center',
                'style' => 'width:35%;',
            ),
            'debit' => array(
                'name' => __('Debit'),
                'class' => 'text-center',
                'style' => 'width:30%;',
            ),
            'credit' => array(
                'name' => __('Kredit'),
                'class' => 'text-center',
                'style' => 'width:30%;',
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'text-center',
                'style' => 'width:5%;',
            ),
        );

        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="temp-document-picker document-calc">
	<div class="box box-primary">
	    <?php 
	            echo $this->element('blocks/common/box_header', array(
	                'title' => __('Informasi Transaksi'),
	            ));
	    ?>
	    <div class="box-body table-responsive">
	        <table class="table table-hover">
		        <?php
	                    if( !empty($fieldColumn) ) {
	                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
	                    }
	            ?>
	        	<tbody class="field-content">
                    <?php
                            $grandtotal_debit = 0;
                            $grandtotal_credit = 0;

                            if(!empty($dataDetail)){
                                foreach ($dataDetail as $key => $value) {
                                    $grandtotal_debit += $this->Common->filterEmptyField($value, 'GeneralLedgerDetail', 'debit', 0);
                                    $grandtotal_credit += $this->Common->filterEmptyField($value, 'GeneralLedgerDetail', 'credit', 0);

                                    echo $this->element('blocks/cashbanks/tables/general_ledger_item', array(
                                        'idx' => $key,
                                        'value' => $value,
                                    ));
                                }
                            } else {
                                for ($i=0; $i < 2; $i++) {
                                    echo $this->element('blocks/cashbanks/tables/general_ledger_item', array(
                                        'idx' => $i,
                                    ));
                                }
                            }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="grandtotal">
                        <?php
                                $grandtotal_debit = $this->Common->getFormatPrice($grandtotal_debit, 0, 2);
                                $grandtotal_credit = $this->Common->getFormatPrice($grandtotal_credit, 0, 2);

                                echo $this->Html->tag('td', __('Grand Total'), array(
                                    'class' => 'text-right',
                                ));
                                echo $this->Html->tag('td', $grandtotal_debit, array(
                                    'class' => 'text-right total_custom',
                                    'rel' => 'debit',
                                    'data-decimal' => 2,
                                ));
                                echo $this->Html->tag('td', $grandtotal_credit, array(
                                    'class' => 'text-right total_custom',
                                    'rel' => 'credit',
                                    'data-decimal' => 2,
                                ));
                        ?>
                    </tr>
                </tfoot>
	    	</table>
	    </div>
	</div>
</div>