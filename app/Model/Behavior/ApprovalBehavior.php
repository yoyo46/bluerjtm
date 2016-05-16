<?php
App::uses('ModelBehavior', 'Model');

class ApprovalBehavior extends ModelBehavior {
	function doApproval(Model $model, $data = array(), $id = false){
        $result = false;
        $defaul_msg = __('melakukan proses approval');

		if( !empty($data) ) {
			$document_id = $model->filterEmptyField($data, 'DocumentAuth', 'document_id');
			$user_id = $model->filterEmptyField($data, 'DocumentAuth', 'user_id');
			$nodoc = $model->filterEmptyField($data, 'DocumentAuth', 'nodoc');
			$user_position_id = $model->filterEmptyField($data, 'DocumentAuth', 'user_position_id');
			$approval_detail_id = $model->filterEmptyField($data, 'DocumentAuth', 'approval_detail_id');
			$status_document = $model->filterEmptyField($data, 'DocumentAuth', 'status_document');
			$approval_name = $model->filterEmptyField($data, 'DocumentAuth', 'approval_name');
			$document_url = $model->filterEmptyField($data, 'DocumentAuth', 'document_url');
			$document_revised_url = $model->filterEmptyField($data, 'DocumentAuth', 'document_revised_url');
			$priority = $model->DocumentAuth->ApprovalDetail->_callPriorityApproval($user_position_id, $approval_detail_id);

			$model->DocumentAuth->create();
			$model->DocumentAuth->set($data);

			if( $model->DocumentAuth->validates() ) {
            	switch ($status_document) {
                    case 'approve':
                        $msgRevision = sprintf(__('%s dengan No Dokumen %s telah disetujui'), $approval_name, $nodoc);
                        break;
                    case 'revise':
                        $msgRevision = sprintf(__('%s dengan No Dokumen %s memerlukan resivisi Anda'), $approval_name, $nodoc);
                        break;
                    case 'reject':
                        $msgRevision = sprintf(__('%s dengan No Dokumen %s telah ditolak'), $approval_name, $nodoc);
                        break;
                }

                if($model->DocumentAuth->save() && !empty($msgRevision)){
                	$data_arr = array();

                    if( !empty($priority) ){
                        switch ($status_document) {
                            case 'approve':
                                $data_arr = array(
                                    'transaction_status' => 'approved',
                                );
                                break;
                            case 'revise':
                                $data_arr = array(
                                    'transaction_status' => 'revised',
                                );
                        		$document_url = $document_revised_url;
                                break;
                            case 'reject':
                                $data_arr = array(
                                    'transaction_status' => 'rejected',
                                );
                                break;
                        }
                    } else if($status_document == 'revise'){
                        $data_arr = array(
                            'transaction_status' => 'revised',
                        );
                        $document_url = $document_revised_url;
                    }

                    if( !empty($data_arr) ) {
                        $model->id = $document_id;
                        $model->set($data_arr);
                        $model->save();
                    }

                    $result = array(
                        'msg' => __('Berhasil merubah status dokumen'),
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $msgRevision,
                            'document_id' => $document_id,
                        ),
                        'Notification' => array(
                        	'user_id' => $user_id,
                            'name' => $msgRevision,
                            'link' => $document_url,
                    	),
                        'data' => $data,
                    );
                } else {
					$defaul_msg = sprintf(__('Gagal %s'), $defaul_msg);
	                $result = array(
	                    'msg' => $defaul_msg,
	                    'status' => 'error',
	                );
				}
			} else {
				$defaul_msg = sprintf(__('Gagal %s'), $defaul_msg);
                $result = array(
                    'msg' => $defaul_msg,
                    'status' => 'error',
                );
			}
		}
		
		return $result;
	}
}
