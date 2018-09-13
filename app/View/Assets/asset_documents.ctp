<?php
        $payment_id = !empty($payment_id)?$payment_id:false;
?>
<div id="wrapper-modal-write">
    <?php 
            $modelName = false;
            $dataColumns = array(
                'check-box' => array(
                    'name' => '',
                    'class' => 'text-center',
                ),
                'name' => array(
                    'name' => __('Nama'),
                    'class' => 'text-center',
                ),
                'group' => array(
                    'name' => __('Group Asset'),
                    'class' => 'text-center',
                ),
                'note' => array(
                    'name' => __('Keterangan'),
                ),
                'nilai_perolehan' => array(
                    'name' => __('Nilai Perolehan'),
                    'class' => 'text-center',
                ),
                'ak_penyusutan' => array(
                    'name' => __('Ak. Penyusutan'),
                    'class' => 'text-center',
                ),
            );

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/assets/searchs/index', array(
                'urlBack' => array(
                    'controller' => 'assets',
                    'action' => 'asset_documents',
                    'payment_id' => $payment_id,
                ),
                'urlForm' => array(
                    'action' => 'search',
                    'asset_documents',
                    'payment_id' => $payment_id,
                ),
            ));
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover document-picker">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
            <tbody>
                <?php
                        if(!empty($values)){
                            foreach ($values as $key => $value) {
                                $id = $this->Common->filterEmptyField($value, 'Asset', 'id');
                                $name = $this->Common->filterEmptyField($value, 'Asset', 'name');
                                $note = $this->Common->filterEmptyField($value, 'Asset', 'note');
                                $nilai_perolehan = $this->Common->filterEmptyField($value, 'Asset', 'nilai_perolehan');
                                $ak_penyusutan = $this->Common->filterEmptyField($value, 'Asset', 'ak_penyusutan');
                                $group = $this->Common->filterEmptyField($value, 'AssetGroup', 'group_name');

                                $nilai_perolehan = $this->Common->getFormatPrice($nilai_perolehan, 0, 2);
                                $ak_penyusutan = $this->Common->getFormatPrice($ak_penyusutan, 0, 2);
                ?>
                <tr class="pick-document item" rel="<?php echo $id; ?>" data-type="single-total">
                    <?php
                            echo $this->Html->tag('td', $this->Form->checkbox('document_id.'.$id, array(
                                'class' => 'check-option',
                                'value' => $id,
                            )), array(
                                'class' => 'removed check-box text-center',
                            ));
                            echo $this->Html->tag('td', $name.$this->Form->hidden('AssetSellDetail.asset_id.', array(
                                'value' => $id,
                            )));
                            echo $this->Html->tag('td', $group, array(
                                'class' => 'removed',
                            ));
                            echo $this->Html->tag('td', $note);
                            echo $this->Html->tag('td', $nilai_perolehan, array(
                                'class' => 'text-right calc-item',
                                'rel' => 0,
                            ));
                            echo $this->Html->tag('td', $ak_penyusutan, array(
                                'class' => 'text-right calc-item',
                                'rel' => 1,
                            ));

                            echo $this->Html->tag('td', $this->Common->buildInputForm('AssetSellDetail.price.', false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'text-right price',
                                'attributes' => array(
                                    'data-type' => 'input_price_coma',
                                ),
                            )), array(
                                'class' => 'text-center hide',
                            ));
                            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                                'class' => 'delete-document btn btn-danger btn-xs',
                                'escape' => false,
                            )), array(
                                'class' => 'actions hide text-center',
                            ));
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
                    'urlTitle' => __('Daftar Asset'),
                ),
            ));
    ?>
</div>