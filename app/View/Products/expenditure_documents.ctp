<div id="wrapper-modal-write">
    <?php 
            $modelName = false;
            $dataColumns = array(
                'nodoc' => array(
                    'name' => __('No. SPK'),
                ),
                'date' => array(
                    'name' => __('Tanggal'),
                    'class' => 'text-center',
                ),
                'type' => array(
                    'name' => __('Jenis'),
                ),
                'nopol' => array(
                    'name' => __('No Pol'),
                ),
                'driver' => array(
                    'name' => __('Supir'),
                ),
            );

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/products/expenditures/forms/search_spk');
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
                                $id = $this->Common->filterEmptyField($value, 'Spk', 'id');
                                $nodoc = $this->Common->filterEmptyField($value, 'Spk', 'nodoc');
                                $transaction_date = $this->Common->filterEmptyField($value, 'Spk', 'transaction_date', false, true, array(
                                    'date' => 'd/m/Y',
                                ));
                                $document_type = $this->Common->filterEmptyField($value, 'Spk', 'document_type');
                                $nopol = $this->Common->filterEmptyField($value, 'Spk', 'nopol', '-');
                                $driver = $this->Common->filterEmptyField($value, 'Driver', 'name', '-');
                                $document_type = ucwords($document_type);
                ?>
                <tr data-value="<?php echo $nodoc; ?>" data-change="#document-number" data-form=".expenditure-form" data-wrapper-write=".wrapper-table-documents" data-duplicate="false" data-id="<?php echo $id; ?>" >
                    <?php
                            echo $this->Html->tag('td', $nodoc);
                            echo $this->Html->tag('td', $transaction_date, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $document_type);
                            echo $this->Html->tag('td', $nopol);
                            echo $this->Html->tag('td', $driver);
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
            echo $this->element('pagination', array(
                'options' => array(
                    'urlClass' => 'ajaxCustomModal',
                    'urlTitle' => __('Daftar SPK'),
                ),
            ));
    ?>
</div>