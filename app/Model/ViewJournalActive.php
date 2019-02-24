<?php
class ViewJournalActive extends AppModel {
    var $belongsTo = array(
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
        'Cogs' => array(
            'className' => 'Cogs',
            'foreignKey' => 'cogs_id',
        ),
    );

    function getData( $find, $options = false, $elements = array() ){
        $default_options = array(
            'conditions'=> array(),
            'fields' => array(),
            'group' => array(),
            'contain' => array(),
            'order' => array(),
        );

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $title = !empty($data['named']['title'])?$data['named']['title']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $coa = !empty($data['named']['coa'])?$data['named']['coa']:false;
        $sort = !empty($data['named']['sort'])?$data['named']['sort']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $journalcoa = !empty($data['named']['journalcoa'])?$data['named']['journalcoa']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(ViewJournalActive.date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(ViewJournalActive.date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($coa) ) {
            $allChildren = $this->Coa->children($coa);
            $tmpId = Set::extract('/Coa/id', $allChildren);
            $tmpId[] = $coa;
            $default_options['conditions']['ViewJournalActive.coa_id'] = $tmpId;
        }
        if( !empty($sort) ) {
            switch ($sort) {
                case 'by-date-desc':
                    $default_options['order'] = array(
                        'ViewJournalActive.date' => 'DESC',
                        'ViewJournalActive.document_no' => 'ASC',
                    );
                    break;
                case 'by-date-asc':
                    $default_options['order'] = array(
                        'ViewJournalActive.date' => 'ASC',
                        'ViewJournalActive.document_no' => 'ASC',
                    );
                    break;
                case 'by-nodoc-desc':
                    $default_options['order'] = array(
                        'ViewJournalActive.document_no' => 'DESC',
                        'ViewJournalActive.date' => 'DESC',
                    );
                    break;
                case 'by-nodoc-asc':
                    $default_options['order'] = array(
                        'ViewJournalActive.document_no' => 'ASC',
                        'ViewJournalActive.date' => 'DESC',
                    );
                    break;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['ViewJournalActive.document_no LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($title) ) {
            $default_options['conditions']['ViewJournalActive.title LIKE'] = '%'.$title.'%';
        }
        if( !empty($journalcoa) && $journalcoa != 'all' ) {
            $default_options['conditions']['ViewJournalActive.coa_id'] = $journalcoa;
        }
        
        return $default_options;
    }
}
?>