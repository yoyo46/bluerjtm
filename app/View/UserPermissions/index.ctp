<div class="row">
    <div class="col-sm-6">
        <div class="">
            <button id="generate-acl" class="btn danger" data-loading-text="loading..." >Generate</button>
        </div>
        <div id="acos">
            <?php 
                    echo $this->Tree->generate($results, array(
                        'alias' => 'alias', 
                        'model' => 'Aco', 
                        'id' => 'acos-ul', 
                        'element' => '/permission-node'
                    )); 
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div id="aco-edit"></div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() { 
    $("#acos").treeview({collapsed: true});
});
$(function() {
    var btn = $('#generate-acl').click(function () {
        btn.button('loading');
        $.get('<?php echo $this->Html->url('/user_permissions/sync');?>', {},
            function(data){
                btn.button('reset');
                $("#acos").html(data);
            }
        );        
    })
});
</script>
