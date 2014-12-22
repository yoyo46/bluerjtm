<?php
class CommonHelper extends AppHelper {
	var $helpers = array('Html', 'Number');

    function getImage($path_model, $name, $thumb = true, $size = false, $options = array()) {

        $cek_url = false;

        if(!empty($name)){
            if($thumb){
                if($size){
                    $cek_url = WWW_ROOT.'/images/'.$path_model.'/thumb/'.$size.'/'.$name;
                    $image = '/images/'.$path_model.'/thumb/'.$size.'/'.$name;
                }else{
                    $cek_url = WWW_ROOT.'/images/'.$path_model.'/'.$name;
                    $image = '/images/'.$path_model.'/thumb/'.$size.'/'.$name;
                }
            }else{
                $cek_url = WWW_ROOT.'/images/'.$path_model.'/'.$name;
                $image = '/images/'.$path_model.'/'.$name;
            }           
        }

        $result = '';
        if(file_exists($cek_url) && $cek_url){
            $result = $image;
        }else{
            if($thumb && $size && $size != 'logo'){
                $result = '/images/error/thumb/'.$size.'/error.png';    
            }else{
                if($size != 'logo'){
                    $result = '/images/error/error.png';    
                }else{
                    $result = '/images/error/thumb/small/error.png';    
                }
                
            }
        }
        
        return $this->Html->image($result, $options);
    }

	function customDate($dateString, $format = 'd F Y') {
		return date($format, strtotime($dateString));
	}

	/**
	*
	*	filterisasi content tag
	*
	*	@param string string : string
	*	@return string
	*/
	function safeTagPrint($string){
		return strip_tags($string);
	}

	function generateCoaTree ( $coas ) {
		$dataTree = '<ul>';
        if( !empty($coas) ) {
            foreach ($coas as $key => $coa) {
				$dataTree .= '<li class="parent_li">';
				$dataTree .= $this->Html->tag('span', $coa['Coa']['code'], array(
                    'title' => $coa['Coa']['code'],
                ));
                $dataTree .= $this->Html->link($coa['Coa']['name'], 'javascript:', array(
                    'escape' => false,
                ));
                $dataTree .= $this->Html->link('<i class="fa fa-plus-circle"></i>', array(
                    'controller' => 'settings',
                    'action' => 'coa_add',
                    $coa['Coa']['id'],
                ), array(
                    'escape' => false,
                    'class' => 'bg-green'
                ));
                $dataTree .= $this->Html->link('<i class="fa fa-minus-circle"></i>', array(
                    'controller' => 'settings',
                    'action' => 'coa_toggle',
                    $coa['Coa']['id'],
                ), array(
                    'escape' => false,
                    'class' => 'bg-red'
                ));

                if( !empty($coa['children']) ) {
                	$child = $coa['children'];
                	$dataTree .= $this->generateCoaTree($child);
                }

				$dataTree .= '</li>';
            }
        }
		$dataTree .= '</ul>';
		return $dataTree;
	}
}