<div class="row hidden-print">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder"></i> <?php echo lang('report_period'); ?>
                </div>
                <div class="tools">
                    <a href="" class="collapse"></a>
                    <a href="#portlet-config" data-toggle="modal" class="config"></a>
                    <a href="" class="reload"></a>
                    <a href="" class="remove"></a>
                </div>
            </div>
            <div class="portlet-body">

                <form class="form-inline" method="post" action="<?php echo site_url('reports/C_accountPayable') ?>" role="form">
                    <div class="form-group">

                        <select class="form-control" id="report_period" name="report_period">
                            <option value="custom">Custom</option>
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="last_week">Last Week</option>
                            <option value="last_year">Last Year</option>
                            <option value="this_year">This Year</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail2">From Date</label>
                        <input type="date" class="form-control" name="from_date" id="from_date" value="<?php echo date("Y-m-d"); ?>" placeholder="From Date">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword2">To Date</label>
                        <input type="date" class="form-control" name="to_date" id="to_date" value="<?php echo date("Y-m-d"); ?>" placeholder="To Date">
                    </div>

                    <button type="submit" class="btn btn-default"><?php echo lang('search'); ?></button>
                </form>
            </div>
        </div>
        <!-- END SAMPLE FORM PORTLET-->
    </div>
</div>
<!-- END PAGE CONTENT-->

<div class="row">
    <div class="col-sm-8 col-sm-offset-2 border">
        <div class="text-center">
            <?php echo anchor('reports/C_accountReceivable/printPDF/' . $from_date . '/' . $to_date, "<i class='fa fa-print'></i> Print", "target='_blank'"); ?>
            <h3><?php echo ucfirst($this->session->userdata("company_name")); ?></h3>
            <h4 style="margin-bottom:2px;"><?php echo $main; ?></h4>
            <p><?php echo date('m/d/Y', strtotime($from_date)) . ' to ' . date('m/d/Y', strtotime($to_date)); ?></p>
        </div>

        <table class="table table-condensed" id="">
            <thead>
                <tr>
                    <th><?php echo lang('customer') ?></th>
                    <th><?php echo lang('type') ?></th>
                    <!-- <th class="text-right"><?php echo lang('debit') . ' ' . lang('amount') ?></th>
                        <th class="text-right"><?php echo lang('credit') . ' ' . lang('amount') ?></th> -->
                    <th class="text-right"><?php echo lang('amount') ?></th>

                </tr>
            </thead>

            <tbody>
                <?php
                //initialize
                $dr_total = 0.00;
                $cr_total = 0.00;
                $balance = 0.00;
                $net_total = 0;
                $dr_amount = 0.00;
                $cr_amount = 0.00;

                //var_dump($trialBalance);
                foreach ($customers as $key => $list) {
                    //if($balance != 0)
                    //{
                    $op_balance_dr = ($list['op_balance_dr']);
                    $op_balance_cr = ($list['op_balance_cr']);
                    $op_balance = (($op_balance_dr - $op_balance_cr));

                    $customer_Entries = $this->M_customers->get_customer_Entries($list['id'], $from_date, $to_date);

                    // var_dump($customer_Entries);

                    $balance_dr = (@$customer_Entries[0]['debit']);
                    $balance_cr = (@$customer_Entries[0]['credit']);
                    $balance = (($op_balance_dr + $balance_dr) - ($op_balance_cr + $balance_cr));

                    if ((float) $balance > 0) {
                        echo '<tr>';
                        echo '<td>' . $list['first_name'] . ' ' . $list["last_name"] . '</td>';
                        echo '<td></td>';
                        echo '<td></td>';
                        echo '</tr>';

                        foreach ($customer_Entries as $rows) {
                            $balance_row = (($op_balance_dr + @$customer_Entries[0]['debit']) - ($op_balance_cr + @$customer_Entries[0]['credit']));
                            $get_sales_inv_payment = $this->M_sales->get_sales_inv_total_balance($rows['invoice_no']);
                            $total_balance = ($balance_row - @$get_sales_inv_payment[0]['amount']);

                            if ((float) $total_balance > 0) {
                                echo '<tr>';
                                echo '<td></td>';
                                echo '<td>' . date("m/d/Y", strtotime($rows['due_date'])) . '</td>';
                                echo '<td class="text-right">' . number_format($total_balance, 2) . '</td>';
                                $net_total += $total_balance;
                                echo '</tr>';
                            }
                        }
                    }
                }
                echo '</tbody>';
                echo '<tfoot>';
                echo '<tr><td></td>';
                echo '<td><strong>' . lang('total') . '</strong></td>';
                // echo '<td class="text-right">' . '<strong><small>' . $_SESSION['home_currency_symbol'] . '</small>' .  number_format(abs($dr_amount), 2) . '</strong></td>';
                echo '<td class="text-right">' . '<strong><small>' . $_SESSION['home_currency_symbol'] . '</small>' .  number_format($net_total, 2) . '</strong></td>';

                echo '</tr>';
                echo '</tfoot>';
                echo '</table>';

                ?>

    </div>
    <!-- /.col-sm-12 -->
</div>
<script>
    $(document).ready(function() {

        const site_url = '<?php echo site_url($langs); ?>/';
        const path = '<?php echo base_url(); ?>';
        const current_date = '<?php echo date("Y-m-d") ?>';

        $('#report_period').on('change', function(event) {
            // event.preventDefault();
            if ($(this).val() == 'this_month') {
                var this_month = '<?php echo date("Y-m-01") ?>';
                $('#from_date').val(this_month);
                $('#to_date').val(current_date);

            } else if ($(this).val() == 'last_month') {
                const last_month_1_day = '<?php echo date("Y-m-01", strtotime('-1 month')) ?>';
                const last_month_last_day = '<?php echo date("Y-m-t", strtotime('-1 month')) ?>';
                $('#from_date').val(last_month_1_day);
                $('#to_date').val(last_month_last_day);
            } else if ($(this).val() == 'last_week') {
                const last_week_day = '<?php echo date("Y-m-d", strtotime('-1 week')) ?>';

                $('#from_date').val(last_week_day);
                $('#to_date').val(current_date);

            } else if ($(this).val() == 'last_year') {
                const last_year_1_day = '<?php echo date("Y-01-01", strtotime('-1 year')) ?>';
                const last_year_last_day = '<?php echo date("Y-12-t", strtotime('-1 year')) ?>';
                $('#from_date').val(last_year_1_day);
                $('#to_date').val(last_year_last_day);

            } else if ($(this).val() == 'this_year') {
                const last_year_1_day = '<?php echo date("Y-01-01") ?>';

                $('#from_date').val(last_year_1_day);
                $('#to_date').val(current_date);

            } else if ($(this).val() == 'custom') {

                $('#from_date').val(current_date)
                $('#to_date').val(current_date);

            }

        });
    });
</script>