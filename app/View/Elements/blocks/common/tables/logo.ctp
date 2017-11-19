<style type="text/css">
    .print-header-logo {
      margin-top: 20px;
      border-bottom: 1px solid #ccc;
      padding: 0 0 15px;
      margin-bottom: 15px;
    }
    .print-header-logo .logo-preview {
      display: inline-block;
      margin-right: 14px;
      vertical-align: top;
    }
    .print-header-logo h2 {
      font-size: 24px;
      font-weight: 600;
      margin: 0 0 3px;
      text-transform: uppercase;
    }
    .print-header-logo h3 {
      font-size: 14px;
      font-weight: normal;
      margin: 0 0 2px;
      line-height: 20px;
    }
</style>
<?php 
        $logo = $this->Common->filterEmptyField($value, 'Company', 'logo');
        $address = $this->Common->filterEmptyField($value, 'Company', 'address');
        $name = $this->Common->filterEmptyField($value, 'Company', 'name');
        $phone_number = $this->Common->filterEmptyField($value, 'Company', 'phone_number');
        $fax = $this->Common->filterEmptyField($value, 'Company', 'fax');
        $module_title = !empty($module_title)?$module_title:false;

        if( !empty($logo) ){
            $address = $this->Common->getFormatDesc($address);
            $logo = $this->Common->photo_thumbnail(array(
                'save_path' => Configure::read('__Site.general_photo_folder'), 
                'src' => $logo, 
                'thumb'=>true,
                'size' => 'pm',
                'thumb' => true,
                'fullpath' => true,
            ));

            $logo = $this->Html->tag('div', $logo, array(
                'class' => 'logo-preview',
            ));

            $content = $this->Html->tag('h2', $name);
            $content .= $this->Html->tag('h3', $address);

            if( !empty($phone_number) ) {
                $contact = sprintf('%s. %s', $this->Html->tag('strong', __('Telp')), $phone_number);

                if( !empty($fax) ) {
                    $contact .= sprintf(', %s. %s', $this->Html->tag('strong', __('Fax')), $fax);
                }

                $content .= $this->Html->tag('span', $contact, array(
                    'style' => 'font-size: 12px;display:block;',
                ));
            }
            
            if( empty($action_print) ) {
                $content = $this->Html->tag('div', $content, array(
                    'style' => 'display: inline-block;vertical-align: top;'
                ));

                echo $this->Html->tag('div', $logo.$content, array(
                    'class' => 'print-header-logo',
                ));
            } else {
                $content = $this->Html->tag('div', $content, array(
                    'style' => 'margin-left: 90px;',
                ));
?>
<div class="print-header-logo">
    <table border="0" width="100%">
        <tr>
            <?php 
                    echo $this->Html->tag('td', $logo);
                    echo $this->Html->tag('td', $content, array(
                        'colspan' => 7,
                    ));
            ?>
        </tr>
    </table>
</div>
<?php
            }
        }
?>