<?php
if ($this->session->flashdata('message')) {
    echo "<div class='alert alert-success fade in'>";
    echo $this->session->flashdata('message');
    echo '</div>';
}
?>

<?php
echo validation_errors();
foreach ($Company as $values) :

    $attributes = array('class' => 'form-horizontal', 'role' => 'form', 'enctype' => "multipart/form-data");
    echo form_open('companies/C_company/unsubscribe/', $attributes);

?>

    <div class="form-group">
        <label class="control-label col-sm-2" for="business_name">Business Name:</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="business_name" value="<?php echo $values['name']; ?>" name="business_name" placeholder="" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-2" for="tax_no">Customer Name:</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="customer_name" value="<?php echo set_value('customer_name'); ?>" name="customer_name" placeholder="" />
        </div>
    </div>


    <div class="form-group">
        <label class="control-label col-sm-2" for="Email">Email:</label>
        <div class="col-sm-10">
            <input type="email" class="form-control" id="Email" value="<?php echo $values['email']; ?>" name="email" placeholder="" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-2" for="Contactno">Phone No:</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="contact_no" value="<?php echo $values['contact_no']; ?>" name="contact_no" placeholder="" />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2" for="logo">Comments:</label>
        <div class="col-sm-10">
            <textarea name="comments" class="form-control" id="" cols="20" rows="5"></textarea>
        </div>
    </div>

<?php
    echo '<div class="form-group"><label class="control-label col-sm-2" for="submit"></label>';
    echo '<div class="col-sm-10">';
    echo form_submit('submit', 'Send Request', 'class="btn btn-success"');
    echo '</div></div>';
endforeach;
?>