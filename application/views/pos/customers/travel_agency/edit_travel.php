<?php
//flash messages
      if(isset($flash_message)){
        if($flash_message == TRUE)
        {
          echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Well done!</strong> new supplier created with success.';
          echo '</div>';       
        }else{
          echo '<div class="alert alert-error">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Oh snap!</strong> change a few things up and try submitting again.';
          echo '</div>';          
        }
      }
?>
<?php 
foreach($customer as $values):
$attributes = array('class' => 'form-horizontal', 'role' => 'form', 'enctype'=>"multipart/form-data");
echo validation_errors();
echo form_open('pos/C_customers/edit',$attributes);

echo form_hidden('id',$values['id']);
?>
<div class="form-group">
  <label class="control-label col-sm-2" for="store Name">Posting Type:</label>
  <div class="col-sm-10">
    <?php echo form_dropdown('posting_type_id',$salesPostingTypeDDL,$values['posting_type_id'],'class="form-control select2me"'); ?>
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="opening">Opening Balance Debit:</label>
  <div class="col-sm-4">
    <input type="number" class="form-control" name="op_balance_dr" value="<?php echo $values['op_balance_dr']; ?>" min="0" placeholder="Opening Balance Amount" />
    <input type="hidden" name="op_balance_dr_old" value="<?php echo $values['op_balance_dr']; ?>"/>
  </div>
  
  <label class="control-label col-sm-2" for="opening">Opening Balance Credit:</label>
  <div class="col-sm-4">
    <input type="number" class="form-control" name="op_balance_cr" value="<?php echo $values['op_balance_cr']; ?>" min="0" placeholder="Opening Balance Amount" />
    <input type="hidden" name="op_balance_cr_old" value="<?php echo $values['op_balance_cr']; ?>"/>
  </div>
  
</div>

<?php if(@$_SESSION['multi_currency'] == 1)
{
?>
<div class="form-group">
  <label class="control-label col-sm-2" for="currency_id">Currency:</label>
  <div class="col-sm-4">
    <?php echo form_dropdown('currency_id',$currencyDropDown,$values['currency_id'],'class="form-control select2me" required=""'); ?>
  </div>
  
    <label class="col-md-2 control-label">Exchange Rate</label>
    <div class="col-md-4">
    	<input type="text" class="form-control" name="exchange_rate" value="<?php echo $values['exchange_rate']; ?>" placeholder="Enter Exchange Rate">
    
    </div>
</div>
<?php } ?> 

<div class="form-group">
  <label class="control-label col-md-2" for="store Name">Account:</label>
  <div class="col-md-4">
    <?php echo form_dropdown('acc_code',$accountDDL,$values['acc_code'],'class="form-control select2me"'); ?>
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="customer Name">First Name:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $values['first_name']; ?>" placeholder="Customer First Name"/>
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="customer Name">Last Name:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $values['last_name']; ?>" placeholder="Customer Last Name"/>
  </div>
</div>


<div class="form-group">
  <label class="control-label col-sm-2" for="father_name">Father Name:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="father_name" name="father_name" value="<?php echo $values['father_name']; ?>" />
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="passport_no">Passport No:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="passport_no" name="passport_no" value="<?php echo $values['passport_no']; ?>"placeholder="passport_no" />
  </div>
</div>


<div class="form-group">
  <label class="control-label col-sm-2" for="cnic">CNIC No:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="cnic" name="cnic" value="<?php echo $values['cnic']; ?>" />
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="country">Nationality:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="country" name="country" value="<?php echo $values['country']; ?>"/>
  </div>
</div>

 
<?php 
 
echo '<div class="form-group"><label class="control-label col-sm-2" for="">Gender</label>';
echo '<div class="col-sm-10">';
$option = array('0'=>'Please Select','male'=>'Male','female'=>'Female');
echo form_dropdown('gender',$option,set_value('gender'),'class="form-control"') . '</div></div>';
 ?>

<div class="form-group">
  <label class="control-label col-sm-2" for="dob">Date Of Birth:</label>
  <div class="col-sm-10">
    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $values['dob']; ?>" placeholder="dob" />
  </div>
</div>


<div class="form-group">
  <label class="control-label col-sm-2" for="passport_issue_date">Passport Issue Date:</label>
  <div class="col-sm-10">
    <input type="date" class="form-control" id="passport_issue_date" name="passport_issue_date" value="<?php echo $values['passport_issue_date']; ?>" />
  </div>
</div>


<div class="form-group">
  <label class="control-label col-sm-2" for="passport_expiry_date">Passport Expiry Date:</label>
  <div class="col-sm-10">
    <input type="date" class="form-control" id="passport_expiry_date" name="passport_expiry_date" value="<?php echo $values['passport_expiry_date']; ?>" />
  </div>
</div>


<div class="form-group">
  <label class="control-label col-sm-2" for="">Place of Birth:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="" name="city" value="<?php echo $values['city']; ?>"/>
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="Mobile">Mobile No:</label>
  <div class="col-sm-10">
    <input type="number" class="form-control" id="Mobile" name="mobile_no" value="<?php echo $values['mobile_no']; ?>"placeholder="Mobile No" />
  </div>
</div>


<?php 
echo '<div class="form-group"><label class="control-label col-sm-2" for="submit"></label>';
echo '<div class="col-sm-10">';
echo form_submit('submit','Update','class="btn btn-default"');
echo '</div></div>';
endforeach;
?>
 

