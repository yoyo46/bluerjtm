<div id="wrapper-modal-write">
    <?php 
            $modelName = false;
            $dataColumns = array(
                'nodoc' => array(
                    'name' => __('No. Pengeluaran'),
                ),
                'Tgl Keluar' => array(
                    'name' => __('Tgl Keluar'),
                    'class' => 'text-center',
                ),
                'spk_nodoc' => array(
                    'name' => __('No SPK'),
                ),
                'date' => array(
                    'name' => __('Tanggal SPK'),
                    'class' => 'text-center',
                ),
                'note' => array(
                    'name' => __('Keterangan'),
                ),
            );

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/products/receipts/forms/search_spk');
    ?>
    <div class="box-body table-responsive browse-form">
        <table class="table table-hover">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
            <tbody>
                <?php
                        if(!empty($values)){
                            foreach ($values as $key => $value) {
                                $id = $this->Common->filterEmptyField($value, 'ProductExpenditure', 'id');
                                $nodoc = $this->Common->filterEmptyField($value, 'ProductExpenditure', 'nodoc');
                                $transaction_date = $this->Common->filterEmptyField($value, 'ProductExpenditure', 'transaction_date');
                                $note = $this->Common->filterEmptyField($value, 'ProductExpenditure', 'note');

                                $spk_nodoc = $this->Common->filterEmptyField($value, 'Spk', 'nodoc');
                                $spk_date = $this->Common->filterEmptyField($value, 'Spk', 'transaction_date');

                                $transaction_date = $this->Common->formatDate($transaction_date, 'd/m/Y');
                                $spk_date = $this->Common->formatDate($spk_date, 'd/m/Y');
                ?>
                <tr data-value="<?php echo $nodoc; ?>" data-change="#document-number" data-form=".receipt-form" data-wrapper-write=".wrapper-table-documents" data-duplicate="false" data-id="<?php echo $id; ?>" >
                    <?php
                            echo $this->Html->tag('td', $nodoc.$this->Form->hidden('ProductExpenditureDetail.product_expenditure_id.', array(
                                'value' => $id,
                            )));
                            echo $this->Html->tag('td', $transaction_date, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $spk_nodoc);
                            echo $this->Html->tag('td', $spk_date, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $note);
                    ?>
                </tr>
                <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan'), array(
                            'colspan' => 7,
                            'class' => 'text-center alert alert-warning',
                        )));
                    }
                ?>
            </tbody>
        </table>
    </div>
    <?php
            if(!empty($values)){
                echo $this->element('pagination', array(
                    'options' => array(
                        'urlClass' => 'ajaxCustomModal',
                        'urlTitle' => __('Daftar SPK Internal'),
                    ),
                ));
            }
    ?>
</div>