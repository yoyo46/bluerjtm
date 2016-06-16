<div class="box">
    <div class="box-header">
        <h3 class="box-title">Periode</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
	            echo $this->Form->create('Search', array(
	                'url'=> $this->Html->url( array(
	                    'controller' => 'revenues',
	                    'action' => 'search',
	                    'detail_ritase',
	                    $id
	                )), 
	                'role' => 'form',
	                'inputDefaults' => array('div' => false),
	            ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->label('date', __('Tanggal'));
                    ?>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php 
                                echo $this->Form->input('dateritase',array(
                                    'label'=> false,
                                    'class'=>'form-control pull-right date-range',
                                    'required' => false,
                                    'title' => __('Cari berdasarkan Tgl TTUJ, berangkat, tiba, bali dan sampai pool'),
                                ));
                        ?>
                    </div>
                </div>
                <?php 
                        echo $this->Common->buildInputForm('customerid', __('Customer'), array(
                            'class'=>'form-control chosen-select',
                            'empty' => __('Pilih Customer'),
                            'options' => $customers
                        ));
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'action' => 'detail_ritase', 
                                $id,
                                'admin' => false,
                            ),
                        ));
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('nodoc', __('No TTUJ'));
                        echo $this->Common->buildInputForm('note', __('Keterangan Muat'));
                ?>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>