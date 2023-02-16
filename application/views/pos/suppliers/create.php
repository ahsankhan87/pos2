<div class="row">
    <div class="col-md-12">
    <div class="portlet">
    <div class="portlet-title">
				<div class="caption">
					<i class="fa fa-reorder"></i><?php echo $main; ?>
				</div>
				<div class="tools">
					<a href="javascript:;" class="collapse"></a>
					<a href="#portlet-config" data-toggle="modal" class="config"></a>
					<a href="javascript:;" class="reload"></a>
					<a href="javascript:;" class="remove"></a>
				</div>
			</div>
   
   <div class="portlet-body form">
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
<?php 

$attributes = array('class' => 'form-horizontal', 'role' => 'form','enctype'=>"multipart/form-data");

echo validation_errors();

echo form_open($langs.'/trans/C_suppliers/create',$attributes);

?>

<div class="form-body">
<!--div class="alert alert-danger display-hide">
<button class="close" data-close="alert"></button>
<?php echo lang('error_msg'); ?>
</div-->
<!-- <div class="form-group">
  <label class="control-label col-md-3" for="store Name">Posting Type:<span class="required">* </span></label>
  <div class="col-md-4">
    <?php echo form_dropdown('posting_type_id',$purchasePostingTypeDDL,set_value('posting_type_id'),'class="form-control" required=""'); ?>
    <?php echo anchor('setting/PostingTypes/create_purchase','Add New <i class="fa fa-plus"></i>',''); ?>
    
  </div>
</div>

<div class="form-group">
   <label class="control-label col-sm-3" for="opening">Opening Balance Debit:</label>
  <div class="col-sm-4">
    <input type="number" class="form-control" name="op_balance_dr" value="<?php echo set_value('op_balance_dr') ?>"  min="0" step="0.01" placeholder="Opening Balance Amount" />
  </div>
  
</div>


<div class="form-group">
 
  <label class="control-label col-sm-3" for="opening">Opening Balance Credit:</label>
  <div class="col-sm-4">
    <input type="number" class="form-control" name="op_balance_cr" value="<?php echo set_value('op_balance_cr') ?>"  min="0" step="0.01" placeholder="Opening Balance Amount" />
  </div>
  
</div> -->

<div class="form-group">
  <label class="control-label col-md-3" for="supplier Name"><?php echo lang('supplier').' '.lang('name');?>:<span class="required">* </span></label>
  <div class="col-md-4">
    <input type="text" data-required="1" class="form-control" name="name" value="<?php echo set_value('name') ?>" required="" placeholder="Supplier Name"  />
  </div>
</div>

<div class="form-group">
  <label class="control-label col-md-3" for="Email"><?php echo lang('email');?>:</label>
  <div class="col-md-4">
    <input type="email" class="form-control" name="email" value="<?php echo set_value('email') ?>" placeholder="Supplier Email" />
    
  </div>
</div>

<div class="form-group">
  <label class="control-label col-md-3" for="Address"><?php echo lang('address');?>:</label>
  <div class="col-md-4">
    <textarea class="form-control" placeholder="Address" name="address"></textarea>
  </div>
</div>

<div class="form-group">
  <label class="control-label col-md-3" for="Contactno"><?php echo lang('contact_no');?>:</label>
  <div class="col-md-4">
    <input type="number" class="form-control" name="contact_no" value="<?php echo set_value('contact_no') ?>" placeholder="Contact No"  />
    
  </div>
</div>

<!--<div class="form-group">-->
<!--  <label class="control-label col-md-3" for="also">Also Customer:</label>-->
<!--  <div class="col-md-4">-->
<!--    <input type="checkbox" class="form-control" name="also_customer" value="1" />-->
    
<!--  </div>-->
<!--</div>-->
<!--<div class="form-group">-->
<!--  <label class="control-label col-md-3" for="">Sale Posting Type:<span class="required">* </span></label>-->
<!--  <div class="col-md-4">-->
<!--    <?php echo form_dropdown('sale_posting_type_id',$salesPostingTypeDDL,'','class="form-control"'); ?>-->
<!--  </div>-->
<!--</div>-->

</div>

<?php 
 
echo '<div class="form-actions fluid"><label class="control-label col-sm-2" for="submit"></label>';
echo '<div class="col-md-offset-3 col-md-9">';
echo form_submit('submit',lang('save'),'class="btn btn-success"');
echo '</div></div>';

echo form_close();
 
?>

    <!-- /.col-sm-12 -->
    </div>
</div>
</div>
</div>
<!-- /.row -->