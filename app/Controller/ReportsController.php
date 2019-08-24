<?php
class ReportsController extends AppController {
	public $uses = array(
		'Report',
	);
	public $components	= array(
		'RmReport', 'RjImage',
	);
	public $helpers	= array(
		'Report',
	);
	
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow(array());
		
		$this->limit = 500;
		$this->limit_paging = 500;
	}

	function generate_excel ( $type = null ) {
		$data = $this->request->data;
		$limit = $this->limit;

		if( !empty($data) ) {
			$dataSave = $this->RmReport->_callAddBeforeSave($data, $type);
			// $dataSave = array();

			switch ($type) {
				case 'stock_cards':
					$limit = 30;
					$this->limit_paging = 30;
					break;
				case 'maintenance_cost_report':
					$limit = 30;
					$this->limit_paging = 30;
					break;
				case 'ttuj_outstanding':
					$limit = 50;
					$this->limit_paging = 50;
					break;
			}

			$type = ucwords($type);
			$funcName = __('_callData%s', $type);

			$dataReport = $this->RmReport->$funcName($dataSave, $limit);
			$modelName = Common::hashEmptyField($dataReport, 'model');
			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = Common::hashEmptyField($result, 'id');
			$status = Common::hashEmptyField($result, 'status');
			$title = Common::hashEmptyField($result, 'title', __('Laporan'));

			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( $modelName, $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			}

			$this->admin_detail($id);
		} else {
			echo __('Gagal generate laporan');
			die();
		}
	}

	function admin_detail( $id = false ) {
   		$value = $this->Report->getData('first', array(
   			'conditions' => array(
   				'Report.id' => $id,
			),
		));

		if( !empty($value) ) {
			$value = $this->Report->getMergeList($value, array(
				'contain' => array(
					'ReportDetail',
				),
			));
			$this->RmReport->_callDetailBeforeView($value);

			$title = Common::hashEmptyField($value, 'Report.title', __('Lihat Laporan'));
			$this->set(array(
				'title' => $title, 
			));
		} else {
			echo __('Gagal generate laporan');
			die();
		}
	}

	function report_execute( $id = false ){
		$options = array(
			'conditions' => array(
				'Report.on_progress' => 0,
			),
			'order' => array(
				'Report.created' => 'ASC',
				'Report.id' => 'ASC',
			),
			'limit' => 50,
		);

		if( !empty($id) ) {
			$options['conditions']['Report.id'] = $id;
		}

		$values = $this->Report->getData('all', $options, array(
			'status' => array(
				'pending', 'progress',
			),
		));
		$msg = false;

		if( !empty($values) ) {
			$reports_id = Set::extract('/Report/id', $values);
			$this->Report->updateAll(array(
				'Report.on_progress' => 1,
			), array(
				'Report.id' => $reports_id,
			));
			$limit = $this->limit;

			foreach ($values as $key => $value) {
				$value = $this->Report->getMergeList($value, array(
					'contain' => array(
						'ReportQueue' => array(
							'type' => 'first',
						),
						'ReportDetail',
					),
				));

				$report = $this->MkCommon->filterEmptyField($value, 'Report');
				$id = $this->MkCommon->filterEmptyField($report, 'id');
				$prefix = $this->MkCommon->filterEmptyField($report, 'session_id');
				$filename = $this->MkCommon->filterEmptyField($report, 'filename');
				$currency_total_data = $this->MkCommon->filterEmptyField($report, 'total_data');
				$fetched_data = $this->MkCommon->filterEmptyField($report, 'fetched_data');

				$title = $this->MkCommon->filterEmptyField($value, 'Report', 'title');
				$type = $this->MkCommon->filterEmptyField($value, 'Report', 'report_type_id');

				$last_id = $this->MkCommon->filterEmptyField($value, 'ReportQueue', 'last_id', 0);
				$report_queue_id = $this->MkCommon->filterEmptyField($value, 'ReportQueue', 'id');
				$previously_fetched_data = $this->MkCommon->filterEmptyField($value, 'ReportQueue', 'fetched_data');

				$params = $this->RmReport->_callDataSearch($value);

				switch ($type) {
					case 'stock_cards':
						$limit = 30;
						break;
					case 'maintenance_cost_report':
						$limit = 30;
						break;
				}

				$type = ucwords($type);
				$funcName = __('_callData%s', $type);
				$dataReport = $this->RmReport->$funcName($params, $limit, $fetched_data);
				
				$modelName = $this->MkCommon->filterEmptyField($dataReport, 'model');
				$resultReport = $this->RmReport->_callProcess( $modelName, $id, $value, $dataReport );
				$result = $this->RmReport->_callSaveDataExport( $title, $value, $dataReport, $resultReport );

				if( !empty($result) ) {
					$msg = __('Berhasil generate %s...<br><br>', $title);
				} else {
					$msg = __('Gagal melakukan generate %s...<br><br>', $title);
				}

				if( empty($id) ) {
					echo $msg;
				}
			}

			if( !empty($id) ) {
				$value = $this->Report->getData('first', $options);
				$value = $this->Report->getMergeList($value, array(
					'contain' => array(
						'ReportDetail',
					),
				));

				$this->set('value', $value);
				$this->render('/Reports/generate_excel');
			} else {
				echo $msg;
				die();
			}
		} else {
			$msg = __('No report to be processed...<br>');
			echo $msg;
			die();
		}
	}

	function admin_download( $id = false ) {
   		$value = $this->Report->getData('first', array(
   			'conditions' => array(
   				'Report.id' => $id,
			),
		));

		if( !empty($value) ) {
			$filename = $this->MkCommon->filterEmptyField($value, 'Report', 'filename');
			$basename = $this->MkCommon->filterEmptyField($value, 'Report', 'title');
			$path = Configure::read('__Site.report_folder');

			$filepath = $this->RjImage->_callGetFolderUploadPath($filename, $path);

			$this->set(compact(
				'filepath',
				'basename'
			));
			$this->layout = false;
			$this->render('/Elements/blocks/common/download');
		} else {
			$this->MkCommon->redirectReferer();
		}
	}
}
?>