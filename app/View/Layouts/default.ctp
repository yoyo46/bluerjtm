<!DOCTYPE html>
<html>
<head>
<?php 
		if(empty($title_for_layout)) {
			$title_for_layout = __('ERP RJTM | Dashboard');	
		}

		echo $this->Html->charset().PHP_EOL;
		echo $this->Html->tag('title', $title_for_layout).PHP_EOL;
		echo $this->Html->meta(array(
			'name' => 'viewport', 
			'content' => 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'
		)).PHP_EOL;
		echo $this->Html->css(array(
			'bootstrap.min',
			'font-awesome.min', 
			'ionicons.min', 
			'morris/morris',
			'jvectormap/jquery-jvectormap-1.2.2',
			'datepicker/datepicker3',
			'daterangepicker/daterangepicker-bs3',
			'bootstrap-wysihtml5/bootstrap3-wysihtml5.min',
			'jquery',
			'style',
			'customs',
		)).PHP_EOL;

		if( !empty($this->params['controller']) && $this->params['controller'] == 'user_permissions' ) {
			echo $this->Html->css(array(
				'/css/acl/treeview'
			)).PHP_EOL;
		}

		if(isset($layout_css) && !empty($layout_css)){
			foreach ($layout_css as $key => $value) {
				echo $this->Html->css($value).PHP_EOL;
			}
		}
?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="/js/html5shiv.js"></script>
      <script src="/js/respond.min.js"></script>
    <![endif]-->
	<link rel="icon" href="/img/favicon.png" type="image/jpg" />
</head>
<body class="skin-red">
    <?php
			echo $this->element('headers/menu');
	?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php
				echo $this->element('sidebars/menu');
		?>
        <!-- Right side column. Contains the navbar and content of the page -->
        <aside class="right-side">
            <!-- Content Header (Page header) -->
            <section class="content-header">
            	<?php 
            			if( !empty($module_title) ) {
            				if( !empty($sub_module_title) ) {
            					$module_title .= $this->Html->tag('small', $sub_module_title);
            				}

            				echo $this->Html->tag('h1', $module_title);
            			}

            			echo $this->element('headers/breadcrumb');
            	?>
            </section>

            <!-- Main content -->
            <?php 
            		echo $this->Session->flash();
			        echo $this->Session->flash('auth');
			        echo $this->Session->flash('success');
			        echo $this->Session->flash('error');
			        echo $this->Session->flash('info');

			        $logContent = $this->element('blocks/logs/histories');			        
            		echo $this->Html->tag('section', $this->fetch('content').$logContent, array(
							'class' => 'content',
						));
            ?>
            <!-- /.content -->
        </aside><!-- /.right-side -->
    </div><!-- ./wrapper -->
	<div id="myModal" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content no-radius">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">close</button>
					<h4 id="myModalLabel" class="modal-title">&nbsp;</h4>
				</div>
				<div class="modal-body"></div>
			</div>
		</div>
	</div>

    <!-- add new calendar event modal -->
    <div id="ajaxLoading"><span>Sedang diproses... </span>Mohon tunggu...</div>
    <?php 
    		echo $this->Html->script(array(
				'jquery.2.0.2.min',
				'jquery-ui-1.10.3.min',
				'bootstrap.min', 
				'raphael.2.1.0.min',
				'plugins/morris/morris.min',
				'plugins/sparkline/jquery.sparkline.min',
				'plugins/jvectormap/jquery-jvectormap-1.2.2.min',
				'plugins/jvectormap/jquery-jvectormap-world-mill-en',
				'plugins/jqueryKnob/jquery.knob',
				'plugins/daterangepicker/daterangepicker',
				'plugins/datepicker/bootstrap-datepicker',
				'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min',
				'jquery.library',
			)).PHP_EOL;

			if(isset($layout_js) && !empty($layout_js)){
				foreach ($layout_js as $key => $value) {
					echo $this->Html->script($value).PHP_EOL;
				}
			}

			echo $this->Html->script(array(
				'functions/customs.library.js',
				'functions/app',
				'functions/functions',
				'functions/dashboard',
				'functions/demo',
			)).PHP_EOL;

			if( !empty($this->params['controller']) && $this->params['controller'] == 'user_permissions' ) {
				echo $this->Html->script(array(
					'/js/acl/jquery.cookie',
			        '/js/acl/treeview',
			        '/js/acl/acos',
			        '/js/bootstrap.min',
				)).PHP_EOL;
			}
	?>
</body>
</html>
