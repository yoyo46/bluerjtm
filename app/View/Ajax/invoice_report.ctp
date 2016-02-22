<div class="row" id="modal-report">
    <?php 
            $tarif_type = $this->Common->filterEmptyField($value, 'Invoice', 'tarif_type');
            $status = $this->Common->filterEmptyField($value, 'Invoice', 'status');

            if( $tarif_type == 'angkut' ) {
    ?>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box">
            <?php 
                    echo $this->Html->tag('div', $this->Html->tag('p', __('Print Detail per Kota')), array(
                        'class' => 'inner',
                    ));
                    echo $this->Html->tag('div', $this->Common->icon('file'), array(
                        'class' => 'icon',
                    ));
                    echo $this->Html->link(sprintf(__('Lihat Laporan %s'), $this->Common->icon('arrow-circle-right')), array(
                        'controller' => 'revenues',
                        'action' => 'invoice_print',
                        $id
                    ), array(
                        'escape' => false,
                        'class' => 'small-box-footer',
                    ));
            ?>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box">
            <?php 
                    echo $this->Html->tag('div', $this->Html->tag('p', __('Print Detail')), array(
                        'class' => 'inner',
                    ));
                    echo $this->Html->tag('div', $this->Common->icon('file'), array(
                        'class' => 'icon',
                    ));
                    echo $this->Html->link(sprintf(__('Lihat Laporan %s'), $this->Common->icon('arrow-circle-right')), array(
                        'controller' => 'revenues',
                        'action' => 'invoice_print',
                        $id,
                        'print' => 'date',
                    ), array(
                        'escape' => false,
                        'class' => 'small-box-footer',
                    ));
            ?>
        </div>
    </div>
    <?php 
            if( $status ){
    ?>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box">
            <?php 
                    echo $this->Html->tag('div', $this->Html->tag('p', __('Print HSO')), array(
                        'class' => 'inner',
                    ));
                    echo $this->Html->tag('div', $this->Common->icon('file'), array(
                        'class' => 'icon',
                    ));
                    echo $this->Html->link(sprintf(__('Lihat Laporan %s'), $this->Common->icon('arrow-circle-right')), array(
                        'controller' => 'revenues',
                        'action' => 'invoice_hso_print',
                        $id,
                        'print' => 'header',
                    ), array(
                        'escape' => false,
                        'class' => 'small-box-footer',
                    ));
            ?>
        </div>
    </div>
    <?php 
            }
    ?>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box">
            <?php 
                    echo $this->Html->tag('div', $this->Html->tag('p', __('Print Format MPM MD-D')), array(
                        'class' => 'inner',
                    ));
                    echo $this->Html->tag('div', $this->Common->icon('file'), array(
                        'class' => 'icon',
                    ));
                    echo $this->Html->link(sprintf(__('Lihat Laporan %s'), $this->Common->icon('arrow-circle-right')), array(
                        'controller' => 'revenues',
                        'action' => 'invoice_print',
                        'print' => 'mpm',
                        $id,
                    ), array(
                        'escape' => false,
                        'class' => 'small-box-footer',
                    ));
            ?>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box">
            <?php 
                    echo $this->Html->tag('div', $this->Html->tag('p', __('Print Format HSO Yogya')), array(
                        'class' => 'inner',
                    ));
                    echo $this->Html->tag('div', $this->Common->icon('file'), array(
                        'class' => 'icon',
                    ));
                    echo $this->Html->link(sprintf(__('Lihat Laporan %s'), $this->Common->icon('arrow-circle-right')), array(
                        'controller' => 'revenues',
                        'action' => 'invoice_hso_print',
                        'print' => 'hso-yogya',
                        $id,
                    ), array(
                        'escape' => false,
                        'class' => 'small-box-footer',
                    ));
            ?>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box">
            <?php 
                    echo $this->Html->tag('div', $this->Html->tag('p', __('Print detail per kota HSO.SMG MD-D')), array(
                        'class' => 'inner',
                    ));
                    echo $this->Html->tag('div', $this->Common->icon('file'), array(
                        'class' => 'icon',
                    ));
                    echo $this->Html->link(sprintf(__('Lihat Laporan %s'), $this->Common->icon('arrow-circle-right')), array(
                        'controller' => 'revenues',
                        'action' => 'invoice_print',
                        $id,
                        'print' => 'header',
                    ), array(
                        'escape' => false,
                        'class' => 'small-box-footer',
                    ));
            ?>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box">
            <?php 
                    echo $this->Html->tag('div', $this->Html->tag('p', __('Print Format Yamaha')), array(
                        'class' => 'inner',
                    ));
                    echo $this->Html->tag('div', $this->Common->icon('file'), array(
                        'class' => 'icon',
                    ));
                    echo $this->Html->link(sprintf(__('Lihat Laporan %s'), $this->Common->icon('arrow-circle-right')), array(
                        'controller' => 'revenues',
                        'action' => 'invoice_yamaha_print',
                        $id,
                    ), array(
                        'escape' => false,
                        'class' => 'small-box-footer',
                    ));
            ?>
        </div>
    </div>
    <?php
            }
    ?>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box">
            <?php 
                    echo $this->Html->tag('div', $this->Html->tag('p', __('Print Header')), array(
                        'class' => 'inner',
                    ));
                    echo $this->Html->tag('div', $this->Common->icon('file'), array(
                        'class' => 'icon',
                    ));
                    echo $this->Html->link(sprintf(__('More info %s'), $this->Common->icon('arrow-circle-right')), '#', array(
                        'escape' => false,
                        'class' => 'small-box-footer',
                    ));
            ?>
        </div>
    </div><!-- ./col -->
</div>