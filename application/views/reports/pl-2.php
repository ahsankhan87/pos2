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
                <form class="form-inline" method="post" action="<?php echo site_url('reports/C_profitloss') ?>" role="form">
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
                        <label for="exampleInputEmail2"><?php echo lang('from') . ' ' . lang('date') ?></label>
                        <input type="date" class="form-control" name="from_date" id="from_date" value="<?php echo date("Y-m-d"); ?>" placeholder="From Date">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword2"><?php echo lang('to') . ' ' . lang('date') ?></label>
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
            <?php echo anchor('reports/C_profitloss/printPDF/' . $from_date . '/' . $to_date, "<i class='fa fa-print'></i> Print", "target='_blank'"); ?>
            <h3><?php echo ucfirst($this->session->userdata("company_name")); ?></h3>
            <h4 style="margin-bottom:2px;"><?php echo $main; ?></h4>
            <p><?php echo date('d-m-Y', strtotime($from_date)) . ' to ' . date('d-m-Y', strtotime($to_date)); ?></p>
        </div>

        <table class="table table-condensed">
            <thead>
                <tr>
                    <th><?php echo lang('account') ?></th>
                    <th class="text-right"><?php echo lang('amount') ?></th>
                    <th class="text-right"><?php echo lang('total') ?></th>

                </tr>
            </thead>
            <tbody>

                <?php
                $total = 0;
                foreach ($proft_loss as $key => $list) {
                    echo '<tr><td colspan="3">';
                    echo '<strong>' . ($langs == 'en' ? $list['title'] : $list['title_ur']) . '</strong>';
                    echo '</td></tr>';

                    ///////
                    $pl_report = $this->M_reports->get_profit_loss($_SESSION['company_id'], $list['account_code'], $from_date, $to_date);
                    foreach ($pl_report as $key => $values) :

                        echo '<tr><td>';
                        echo '&nbsp;&nbsp;';
                        echo ($langs == 'en' ? $values['title'] : $values['title_ur']);
                        echo '</td>';
                        $balance = $values['credit'] - $values['debit'];
                        echo '<td class="text-right">';
                        echo number_format($balance, 2);
                        echo '</td>';
                        $total += $balance;
                        echo '<td class="text-right">';
                        echo number_format($total, 2);
                        echo '</td></tr>';
                    endforeach;
                    /////
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td><strong><?php echo lang('net_income'); ?></strong></td>
                    <td></td>
                    <td class="text-right"><strong><?php echo '<small>' . $_SESSION['home_currency_symbol'] . '</small>'; ?><?php echo number_format($total, 2); ?></strong></td>
                </tr>
            </tfoot>

        </table>

    </div>
    <!-- /.col-sm-6 -->
</div>
<!-- /.col-sm-6 -->
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