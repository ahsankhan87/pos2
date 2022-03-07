<div class="row">
    <div class="col-sm-12">
    <?php
    if($this->session->flashdata('message'))
    {
        echo "<div class='alert alert-success fade in'>";
        echo $this->session->flashdata('message');
        echo '</div>';
    }
    if($this->session->flashdata('error'))
    {
        echo "<div class='alert alert-danger fade in'>";
        echo $this->session->flashdata('error');
        echo '</div>';
    }
    ?>
    <p><?php echo anchor('pos/C_customers/create','Add New <i class="fa fa-plus"></i>','class="btn btn-success"'); ?></p>
    
    <?php
    if(count($customers))
    {
    ?>
    <div class="portlet">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-cogs"></i><?php echo $main; ?>
				</div>
				<div class="tools">
					<a href="javascript:;" class="collapse"></a>
					<a href="#portlet-config" data-toggle="modal" class="config"></a>
					<a href="javascript:;" class="reload"></a>
					<a href="javascript:;" class="remove"></a>
				</div>
			</div>
        <div class="portlet-body flip-scroll">
            
    <table class="table table-bordered table-striped table-condensed flip-content" id="sample_2">
        <thead class="flip-content">
        <tr>
            <th>S.No</th>
            <th>Passport No</th>
            <th>Name</th>
            <?php if(@$_SESSION['multi_currency'] == 1){
                echo '<th>Currency</th>';
                }
                ?>
            <th>Mobile</th>
            <th>Dr Bal</th>
            <th>Cr Bal</th>
            <th>Total Balance</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
    <?php
    $sno = 1;
    foreach($customers as $key => $list)
    {
        $exchange_rate = ($list['exchange_rate'] == 0 ? 1 : $list['exchange_rate']);
        
        echo '<tr valign="top">';
        //echo '<td>'.form_checkbox('p_id[]',$list['id'],false).'</td>';
        //echo '<td>'.$sno++.'</td>';
        echo '<td>'.$list['id'].'</td>';
        echo '<td><a href="'.site_url('pos/C_customers/customerDetail/'. $list['id']).'">'.$list['passport_no'].'</a></td>';
        echo '<td><a href="'.site_url('pos/C_customers/customerDetail/'. $list['id']).'">'.$list['first_name'].'</a></td>';
        //echo '<td><a href="'.site_url('accounts/C_groups/accountDetail/'. $list['account_code']).'">'.$list['first_name'] . ' '. $list['last_name'].'</a></td>';
        //echo '<td>'.$list['account_code'].'</td>';
        //echo '<td>'.$list['first_name'] . ' '. $list['last_name'].'</td>';
        //echo '<td>'.$list['first_name'].' <a href="mailto:'.$list['email'].'"><i class="fa fa-envelope-o fa-fw"></i></a></td>';
        if(@$_SESSION['multi_currency'] == 1){
            echo '<td>'.$this->M_currencies->get_currencyName($list['currency_id']).'</td>';
            }
        echo '<td>'.$list['mobile_no'].'</td>';
       // echo '<td>'.$list['address'].'</td>';
        //echo '<td>'.$list['city'].'</td>';
        
        //OPENING BALANCES
        $op_balance_dr = round($list['op_balance_dr']/$exchange_rate,2);
        $op_balance_cr = round($list['op_balance_cr']/$exchange_rate,2);
        $op_balance = round(($op_balance_dr-$op_balance_cr)/$exchange_rate,2);
        
        //CURRENT BALANCES
        $cur_balance = $this->M_customers->get_customer_total_balance($list['id'],FY_START_DATE,FY_END_DATE);
        $balance_dr = round($cur_balance[0]['dr_balance']/$exchange_rate,2);
        $balance_cr = round($cur_balance[0]['cr_balance']/$exchange_rate,2);
        
        echo '<td>'.($op_balance_dr+$balance_dr).'</td>';
        echo '<td>'.($op_balance_cr+$balance_cr).'</td>';
        echo '<td>'.(($op_balance_dr+$balance_dr)-($op_balance_cr+$balance_cr)).'</td>';
                 
       //echo '<td><a href="'.site_url('pos/C_customers/paymentModal/'. $list['id']).'" class="btn btn-warning btn-sm" >Receive Payment</a></td>';
       // echo '<td><button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#customer-payment-Modal">Receive Payment</button></td>';
            
        echo '<td>';
        //echo  anchor(site_url('up_customer_images/upload_images/'.$list['id']),' upload Images');
        echo anchor('pos/C_customers/edit/'.$list['id'],'<i class="fa fa-pencil-square-o fa-fw"></i>',' title="Edit"'); ?> | 
        <a href="<?php echo site_url('pos/C_customers/inactivate/'.$list['id'].'/'.$op_balance_dr.'/'.$op_balance_cr) ?>"
         onclick="return confirm('Are you sure you want to delete?')" title="Make Inactive"><i class="fa fa-trash-o fa-fw"></i></a>
    
    <?php
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '<tfoot>
            <tr>
                <th colspan="6" style="text-align:right">Total:</th>
                <th></th>
            </tr>
        </tfoot>';
    echo '</table>';
    
    }
    ?>
    </div>
    <!-- /.col-sm-12 -->
</div>
<!-- /.row -->
