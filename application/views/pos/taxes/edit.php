
<?php
if($this->session->flashdata('message'))
{
    echo "<div class='alert alert-danger fade in'>";
    echo $this->session->flashdata('message');
    echo '</div>';
}
?>

<?php 
foreach($tax as $values):
$attributes = array('class' => 'form-horizontal', 'role' => 'form', 'enctype'=>"multipart/form-data");
echo validation_errors();
echo form_open('setting/C_taxes/edit',$attributes);

echo form_hidden('id',$values['id']);
?>
<div class="form-group">
  <label class="control-label col-sm-2" for="account_code"><?php echo lang('account'); ?>:</label>
  <div class="col-sm-10">
    <?php echo form_dropdown('account_code',$accountDDL,$values['account_code'],'class="form-control select2me"'); ?>
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="Name"><?php echo lang('name'); ?>:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="Name" name="name" value="<?php echo $values['name']; ?>" placeholder="Name" />
  </div>
</div>
 
<div class="form-group">
  <label class="control-label col-sm-2" for="rate"><?php echo lang('rate'); ?> (%):</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="rate" name="rate" min="0" step="0.0001"  value="<?php echo $values['rate']; ?>"placeholder="rate" />
  </div>
</div>
 
 <div class="form-group">
  <label class="control-label col-sm-2" for="description"><?php echo lang('description'); ?>:</label>
  <div class="col-sm-10">
    <textarea name="description" class="form-control"><?php echo $values['description']; ?></textarea>
  </div>
</div>

<?php 
 
echo '<div class="form-group"><label class="control-label col-sm-2" for="status">'.lang("description").'</label>';
echo '<div class="col-sm-10">';
$option = array('1'=>'active','0'=>'inactive');
echo form_dropdown('status',$option,$values['status'],'class="form-control"') . '</div></div>';
 

echo '<div class="form-group"><label class="control-label col-sm-2" for="submit"></label>';
echo '<div class="col-sm-10">';
echo form_submit('submit',lang("update"),'class="btn"');
echo '</div></div>';
endforeach;
?>