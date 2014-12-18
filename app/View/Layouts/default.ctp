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
			'style',
			'customs',
		)).PHP_EOL;

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
</head>
<body class="skin-blue">
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
			        
            		echo $this->Html->tag('section', $this->fetch('content'), array(
							'class' => 'content',
						));
            ?>
            <!-- /.content -->
        </aside><!-- /.right-side -->
    </div><!-- ./wrapper -->

    <!-- add new calendar event modal -->

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
				'functions/app',
				'functions/functions',
				'functions/dashboard',
				'functions/demo',
				'jquery.library',
			)).PHP_EOL;

			echo $this->element('sql_dump');
	?>
</body>
</html>
