<div class="container-fluid block-hide">
    <?php echo $this->session->flashdata('confirmation'); ?>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panal-header-title pull-left">
                    <h1>Add Area</h1>
                </div>
            </div>

            <div class="panel-body">
                <?php
                    $attribute = array(
                        'name' => '',
                        'class' => 'row',
                        'id' => ''
                    );
                    echo form_open('', $attribute);
                ?>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="name" class="form-control" placeholder="Area Name" required>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <input class="btn btn-primary" type="submit" name="add" value="Add New">
                    </div>
                </div>
                <?php echo form_close(); ?>
                <hr>
                
                <!-- Print banner Start Here -->
                <?php  $this->load->view('print'); ?>
                
                <h4 class="hide">All Area</h4>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 40px;">SL</th>
                        <th>Area Name</th>
                        <th class="none text-right" style="width: 100px;">Action</th>
                    </tr>
                    <?php
                        if(!empty($areas)){
                            foreach($areas as $key => $item){
                    ?>
                    <tr>
                        <td><?= ++$key ?></td>
                        <td><?= $item->name; ?></td>
                        <td class="none text-right">
                            <a title="edit" class="btn btn-warning" href="#" data-toggle="modal" data-target="#editarea" onclick="getEditId('<?= $item->id;?>')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            <a title="Delete" class="btn btn-danger" href="<?= site_url('field_doctorsk/field_doctorsk/area_delete/'.$item->id); ?>" ><i class="fa fa-trash-o" aria-hidden="true"></i></a>                    
                        </td>
                    </tr>
                    <?php }} ?>
                </table>
            </div>
            <div class="panel-footer">&nbsp;</div>
        </div>
    </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editarea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Area</h4>
            </div>
            <div class="modal-body">
                <?php
                    $attribute = array(
                        'name' => '',
                        'class' => 'row',
                        'id' => ''
                    );
                    echo form_open('field_doctorsk/field_doctorsk/areaUpdate', $attribute);
                ?>
                <div class="col-md-9">
                    <div class="form-group"> 
                        <input type="hidden" name="id" id="areaId" class="form-control" required>
                        <input type="text" name="name" id="areaName" class="form-control" placeholder="Area Name" required>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <input class="btn btn-primary" type="submit" name="add" value="Update">
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    function getEditId(id) {
        var id = id;
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('field_doctorsk/field_doctorsk/getAreaNameById'); ?>",
            data: {
                id: id
            }
        }).then(function(response) {
            if (response.length) {
                var data = JSON.parse(response);
                $('#areaId').val(data.id);
                $('#areaName').val(data.name);
            } 
        });
    }
</script>