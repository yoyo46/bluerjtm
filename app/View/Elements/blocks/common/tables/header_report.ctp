<?php 
        $company = $this->Common->getDataSetting( $setting, 'company_name' );
        $company_address = $this->Common->filterEmptyField( $setting, 'Setting', 'company_address' );
        $company_email = $this->Common->filterEmptyField( $setting, 'Setting', 'company_email' );
        $company_phone = $this->Common->filterEmptyField( $setting, 'Setting', 'company_phone' );
?>
<div class="page-header" style="margin-bottom: 20px;border-bottom: 2px solid #000;">
    <?php 
            echo $this->Html->tag('h3', $company, array(
                'style' => 'font-family: \'Carter One\', cursive;text-transform: uppercase;margin: 0 0 5px;font-size: 24px;',
            ));

            if( !empty($company_address) ) {
                echo $this->Html->tag('p', $company_address, array(
                    'style' => 'font-size: 12px;margin: 0 0 5px;line-height: 15px;',
                ));
            }
            if( !empty($company_phone) || !empty($company_email) ) {
                $contact = '';

                if( !empty($company_phone) ) {
                    $contact .= sprintf('Tlp. %s', $company_phone);
                }
                if( !empty($company_email) ) {
                    if( !empty($company_phone) ) {
                        $contact .= ' / ';
                    }

                    $contact .= sprintf('email: %s', $company_email);
                }

                echo $this->Html->tag('p', $contact, array(
                    'style' => 'font-size: 12px;margin: 0 0 5px;line-height: 15px;',
                ));
            }
    ?>
</div>