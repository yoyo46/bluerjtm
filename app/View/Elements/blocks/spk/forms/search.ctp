<?php 
        $params = $this->params;

        if( !empty($params['named']) ) {
            $style = '';
        } else {
            $style = 'display: none;';
        }
?>
<div class="box collapsed-box box-collapse">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm search-collapse" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body" style="<?php echo $style; ?>">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'spk',
                        'action' => 'search',
                        'index',
                    )), 
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('nodoc', __('No SPK'));
                        echo $this->Common->buildInputForm('document_type', __('Jenis SPK'), array(
                            'empty' => __('- Pilih Jenis -'),
                            'class' => 'form-control chosen-select',
                            'options' => array(
                                'internal' => __('Internal'),
                                'eksternal' => __('Eksternal'),
                                'wht' => __('WHT'),
                                'production' => __('Produksi'),
                            ),
                        ));
                        echo $this->Common->buildInputForm('vendor_id', __('Supplier'), array(
                            'empty' => __('- Pilih Supplier -'),
                            'class' => 'form-control chosen-select',
                        ));
                        echo $this->Common->buildInputForm('note', __('Keterangan'));
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('date', __('Tanggal'), array(
                            'textGroup' => $this->Common->icon('calendar'),
                            'positionGroup' => 'positionGroup',
                            'class' => 'form-control pull-right date-range',
                        ));
                        echo $this->Common->buildInputForm('nopol', __('NoPol'));
                        echo $this->Common->buildInputForm('status', __('Status'), array(
                            'empty' => __('Pilih Status'),
                            'options' => array(
                                'open' => __('Open'),
                                'closed' => __('Closed'),
                                'finish' => __('Finish'),
                            ),
                        ));
                        echo $this->Common->buildInputForm('payment_status', __('Status Pembayaran'), array(
                            'empty' => __('Pilih Status'),
                            'options' => array(
                                'none' => __('Belum dibayar'),
                                'half_paid' => __('Dibayar sebagian'),
                                'paid' => __('Sudah dibayar'),
                            ),
                        ));
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'controller' => 'spk', 
                                'action' => 'index', 
                            ),
                        ));
                ?>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>