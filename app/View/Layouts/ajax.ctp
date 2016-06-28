<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

	if( isset($layout_js) && !empty($layout_js) ) {
		echo $this->Html->script($layout_js);
	}

	if(isset($layout_css) && !empty($layout_css) ) {
		echo $this->Html->css($layout_css);
	}
?>
<?php
		echo $this->element('blocks/common/template_flash');	    
		echo $this->fetch('content');
?>
