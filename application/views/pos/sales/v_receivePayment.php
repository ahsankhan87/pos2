<div ng-controller="customersCtrl">
    <div class="row">
        <div class="col-sm-12">
            <?php
            if ($this->session->flashdata('message')) {
                echo "<div class='alert alert-success fade in'>";
                echo $this->session->flashdata('message');
                echo '</div>';
            }
            if ($this->session->flashdata('error')) {
                echo "<div class='alert alert-danger fade in'>";
                echo $this->session->flashdata('error');
                echo '</div>';
            }
            ?>
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-bell"></i><?php echo ucwords($customer[0]['title'] . ' ' . $customer[0]['first_name'] . ' ' . $customer[0]['middle_name'] . ' ' . $customer[0]['last_name']); ?>
                    </div>
                    <div class="tools">
                        <a href="" class="collapse"></a>
                        <a href="#portlet-config" data-toggle="modal" class="config"></a>
                        <a href="" class="reload"></a>
                        <a href="" class="remove"></a>
                    </div>
                </div>
                <div class="portlet-body">
                    <?php foreach ($customer as $list) : ?>

                        <form action="<?php echo site_url('pos/C_invoices/makePayment'); ?>" method="post" class="form-horizontal">
                            
                            <input type="hidden" name="cr_acc_code" value="<?php echo $sales[0]['deposit_to_acc_code']; ?>" />
                            <input type="hidden" name="invoice_no" value="<?php echo $sales[0]['invoice_no']; ?>" />
                            <input type="hidden" name="paid_amount" value="<?php echo $sales[0]['paid']; ?>" />

                            <div class="form-body">
                                <input type="hidden" name="customer_id" ng-model="customer_id" ng-init="customer_id=<?php echo $list['id']; ?>" value="<?php echo $list['id']; ?>" />

                                <div class="form-group">
                                    <label class="col-md-2 control-label">Customer Name</label>
                                    <div class="col-md-4">
                                        <p class="form-control-static">
                                            <?php echo ucwords($list['first_name']) . ' ' . ucwords($list['last_name']); ?>
                                        </p>
                                        
                                    </div>

                                    <label class="col-md-2 control-label">Payment Date</label>
                                    <div class="col-md-4">
                                        <p class="form-control-static">
                                            <input type="date" class="form-control" ng-model="payment_date" name="payment_date" />
                                        </p>
                                    </div>
                                </div>

                                <div class="form-group">

                                    <label class="col-md-2 control-label">Total Amount</label>
                                    <div class="col-md-4">

                                        <input type="text" class="form-control" required="" ng-model="amount" ng-change="update()" autofocus ng-init="amount=''" name="amount" placeholder="Enter Amount" autocomplete="off">

                                    </div>
                                    
                                    <label class="col-md-2 control-label">Balance Amount</label>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control"  name="balance" value="<?php echo number_format($sales[0]['total_amount']+$sales[0]['total_tax']-$sales[0]['paid'],2) ;?>" readonly autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="form-group">

                                    
                                    <label class="control-label col-sm-2" for="">Deposit To:</label>
                                    <div class="col-sm-4">
                                        <select name="deposit_to_acc_code" id="deposit_to_acc_code" class="form-control select2me"></select>
                                    </div>

                                    <label class="col-md-2 control-label">Amount Received</label>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control"  name="paid" value="<?php echo number_format($sales[0]['paid'],2) ;?>" readonly autocomplete="off">
                                    </div>
                                    
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label">Comment</label>
                                    <div class="col-md-4">

                                        <textarea name="narration" name="comment" ng-model="comment" ng-init="comment=''" class="form-control"></textarea>

                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                        <!-- this is transaction wise list of outstanding credit amount invoices
                        <br />
                        <h3>Outstanding Transactions</h3>
                        <table class="table table-striped table-hover" ng-init="GetCreditSales(<?php echo $customer[0]['id']; ?>)" id="sample_">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>Original Amount</th>
                                        <th>Balance</th>
                                        <th>Payment</th>
                                    </tr>
                                </thead>
                                <tr ng-repeat="credit in CreditSales">
                                    
                                    <td>{{credit.invoice_no}}</td>
                                    <td>{{credit.sale_date}}</td>
                                    <?php if (@$_SESSION['multi_currency'] == 1) {
                                    ?>
                                    <td>{{(credit.total_amount*credit.exchange_rate | currency:"<?php echo @$currency[0]['symbol'] ?>")}}</td>
                                    <td>{{(credit.total_amount-credit.paid)*credit.exchange_rate | currency:"<?php echo @$currency[0]['symbol'] ?>"}}<br />
                                    <small>{{(credit.total_amount-credit.paid) | currency:"PRs"}}</small>
                                    </td>
                                    <td>
                                        <input type="text" ng-model="cr_amount" ng-init="cr_amount=credit.balance" name="cr_amount[]" class="form-control" />
                                        <input type="hidden" ng-model="invoice_no" ng-init="invoice_no=credit.invoice_no" name="invoice_no[]"  value="{{credit.invoice_no}}"class="form-control" />
                                        <input type="hidden" ng-model="paid" ng-init="paid=credit.paid" name="paid[]"  value="{{credit.paid}}"class="form-control" />
                                        <input type="hidden" ng-model="prev_exchange_rate" ng-init="prev_exchange_rate=credit.exchange_rate" name="prev_exchange_rate[]"  value="{{credit.exchange_rate}}"class="form-control" />
                                    
                                    </td>
                                    <?php } else { ?>
                                    <td>{{(credit.total_amount | currency:"<?php echo @$_SESSION['home_currency_symbol'] ?>")}}</td>
                                    <td>{{(credit.total_amount-credit.paid | currency:"<?php echo @$_SESSION['home_currency_symbol'] ?>")}}</small>
                                    </td>
                                    <td>
                                        <input type="text" ng-model="cr_amount" ng-init="cr_amount=credit.balance" name="cr_amount[]" class="form-control" />
                                        <input type="hidden" ng-model="invoice_no" ng-init="invoice_no=credit.invoice_no" name="invoice_no[]"  value="{{credit.invoice_no}}"class="form-control" />
                                        <input type="hidden" ng-model="paid" ng-init="paid=credit.paid" name="paid[]"  value="{{credit.paid}}"class="form-control" />
                                        <input type="hidden" ng-model="prev_exchange_rate" ng-init="prev_exchange_rate=credit.exchange_rate" name="prev_exchange_rate[]"  value="{{credit.exchange_rate}}"class="form-control" />
                                    
                                    </td>
                                    <?php } ?>
                                    
                                </tr>
                                -->

                        <?php
                        //initialize
                        //$dr_amount = 0.00;
                        //                                $cr_amount = 0.00;
                        //                                $balance = 0.00;
                        //                                
                        //                                foreach($creditSales as $key => $list)
                        //                                {
                        //                                    $balance = round(($list['total_amount']-$list['paid'])*$list['exchange_rate'],2);
                        //                                    
                        //                                    echo '<tr>';
                        //                                    echo '<td>'.$list['invoice_no'].'</td>';
                        //                                    echo '<td>'.date('d-m-Y',strtotime($list['sale_date'])).'</td>';
                        //                                    echo '<td>'.round($list['total_amount']*$list['exchange_rate'],2).'</td>';
                        //                                    echo '<td>'.$balance.'</td>';
                        //                                    echo '<td><input type="text" ng-model="amount_'.$key.'" ng-init="amount_'.$key.'='.$balance.'" class="form-control" ></td>';
                        //                                    echo '</tr>';
                        //                                }
                        //                                echo '</tbody>';
                        //                                echo '</tfoot>';
                        //                                echo '<tr><th></th>';
                        //                                echo '<th>Total</th>';
                        //                                echo '<th>'.$dr_amount.'</th>';
                        //                                echo '<th>'.$cr_amount.'</th>';
                        //                                
                        //                                echo '</tr>';
                        //                                echo '</tfoot>';

                        ?>
                        <!--            
                        </table>
                     -->

                        <div class="form-group">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-4">

                                <button type="submit" class="btn btn-info" onclick="return confirm('Are you sure you want to save?')">Receive</button>
                                <button type="button" class="btn btn-default" onclick="window.history.back()">Back</button>

                            </div>
                        </div>



                        </form>
                        <!-- END FORM-->
                </div>
            </div> <!-- End Portlet -->

        </div>
        <!-- /.col-sm-12 -->
    </div>
    <!-- /.row -->
</div><!-- /.customerCTRL -->
<script>
$(document).ready(function() {

    const site_url = '<?php echo site_url($langs); ?>/';
    const path = '<?php echo base_url(); ?>';
    
        ////
        deposit_to_acc_codeDDL(1001);
        ////////////////////////
        //GET deposit_to_acc_code DROPDOWN LIST
        function deposit_to_acc_codeDDL(deposit_to_acc_code='') {

            let deposit_to_acc_code_ddl = '';
            var account_type = ['liability','equity','cos','revenue','expense','asset'];
            $.ajax({
                url: site_url + "accounts/C_groups/get_detail_accounts_by_type",
                type: 'POST',
                dataType: "JSON",
                data: {account_types:account_type},
                //dataType: 'json', // added data type
                success: function(data) {
                    //console.log(data);
                    let i = 0;
                    deposit_to_acc_code_ddl += '<option value="0">Select Account</option>';

                    $.each(data, function(index, value) {

                        deposit_to_acc_code_ddl += '<option value="' + value.account_code + '" '+(value.account_code == deposit_to_acc_code ? "selected=''": "")+'>' + value.title+ '</option>';

                    });

                    $('#deposit_to_acc_code').html(deposit_to_acc_code_ddl);

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        }
});
</script>