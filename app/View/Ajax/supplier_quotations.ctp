<?php 
        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No. SQ'),
            ),
            'transaction_date' => array(
                'name' => __('Tgl SQ'),
            ),
            'available_date' => array(
                'name' => __('Tgl Berlaku'),
            ),
            'note' => array(
                'name' => __('Note'),
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div id="wrapper-modal-write">
    <?php 
            echo $this->element('blocks/ajax/forms/searchs/supplier_quotations');
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
                                $id = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'id');
                                $nodoc = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'nodoc');
                                $availableFrom = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'available_from');
                                $availableTo = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'available_to');
                                $transactionDate = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'transaction_date');
                                $note = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'note');

                                $customAvailable = $this->Common->getCombineDate($availableFrom, $availableTo );
                                $customDate = $this->Common->formatDate($transactionDate, 'd/m/Y');
                                $urlAjax = $this->Html->url(array(
                                    'controller' => 'ajax',
                                    'action' => 'getSupplierQuotation',
                                    $id,
                                ));

                                $availableText = sprintf(__('Tgl Berlaku SQ: %s'), $customAvailable);
                ?>
                <tr data-value="<?php echo $nodoc; ?>" data-text="<?php echo $nodoc; ?>" data-change="#supplier-quotation" href="<?php echo $urlAjax; ?>" data-show=".temp-document-picker" data-change-extra="#available-date" data-extra-text="<?php echo $availableText; ?>">
                    <?php
                            echo $this->Html->tag('td', $nodoc);
                            echo $this->Html->tag('td', $customDate);
                            echo $this->Html->tag('td', $customAvailable);
                            echo $this->Html->tag('td', $note);
                            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                                'class' => 'delete-document btn btn-danger btn-xs',
                                'escape' => false,
                            )), array(
                                'class' => 'actions hide',
                            ));
                    ?>
                </tr>
                <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan'), array(
                            'colspan' => 6,
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
                    'urlTitle' => __('Data Supplier Quotation'),
                ),
            ));
    ?>
</div>