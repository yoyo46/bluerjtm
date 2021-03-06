<?php
class ViewDebtCard extends AppModel {
    var $belongsTo = array(
        'Debt' => array(
            'foreignKey' => 'debt_id',
        ),
        'DebtDetail' => array(
            'foreignKey' => 'debt_detail_id',
        ),
        'ViewStaff' => array(
            'foreignKey' => 'employe_id',
            'conditions' => array(
                'ViewStaff.type = ViewDebtCard.type',
            ),
        )
    );

    function getData( $find, $options = false ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ViewDebtCard.id' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
        );

        return $this->full_merge_options($default_options, $options, $find);
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $default_options = $this->defaultOptionParams($data, $default_options, array(
            'DateFrom' => array(
                'field' => 'ViewDebtCard.transaction_date >=',
            ),
            'DateTo' => array(
                'field' => 'ViewDebtCard.transaction_date <=',
            ),
            'name' => array(
                'field' => 'ViewStaff.full_name',
                'type' => 'like',
                'contain' => array(
                    'ViewStaff',
                ),
            ),
            'note' => array(
                'field' => 'ViewDebtCard.note',
                'type' => 'like',
            ),
            'noref' => array(
                'field' => 'LPAD(ViewDebtCard.transaction_id, 6, 0)',
                'type' => 'like',
            ),
            'nodoc' => array(
                'field' => 'ViewDebtCard.nodoc',
                'type' => 'like',
            ),
        ));

        return $default_options;
    }
}
?>