
<?php
//flash messages
      if(isset($flash_message)){
        if($flash_message == TRUE)
        {
          echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">�</a>';
            echo '<strong>Well done!</strong> new supplier created with success.';
          echo '</div>';       
        }else{
          echo '<div class="alert alert-error">';
            echo '<a class="close" data-dismiss="alert">�</a>';
            echo '<strong>Oh snap!</strong> change a few things up and try submitting again.';
          echo '</div>';          
        }
      }
?>
<div class="row">
    <div class="col-sm-12">
   
<?php 
$attributes = array('class' => 'form-horizontal', 'role' => 'form','enctype'=>"multipart/form-data");
echo validation_errors();
echo form_open('setting/C_currencies/create',$attributes);

?>

<div class="form-group">
  <label class="control-label col-sm-2" for="name"><?php echo lang('name'); ?>:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" />
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="country"><?php echo lang('country'); ?>:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="country" name="country" placeholder="country" />
  </div>
</div>
 
 <div class="form-group">
  <label class="control-label col-sm-2" for="code"><?php echo lang('code'); ?>:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="code" name="code" placeholder="code" />
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="Symbol"><?php echo lang('symbol'); ?>:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="Symbol" name="symbol" placeholder="Currency Symbol" />
  </div>
</div>


<?php 

echo '<div class="form-group"><label class="control-label col-sm-2" for="submit"></label>';
echo '<div class="col-sm-10">';
echo form_submit('submit',lang('save'),'class="btn btn-success"');
echo '</div></div>';

echo form_close();
 
?>
</div>
    <!-- /.col-sm-12 -->
</div>
<!-- /.row -->