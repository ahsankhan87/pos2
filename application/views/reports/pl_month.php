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
                            <option value="custom"><?php echo lang('custom'); ?></option>
                            <option value="this_month"><?php echo lang('this_month'); ?></option>
                            <option value="last_month"><?php echo lang('last_month'); ?></option>
                            <option value="last_week"><?php echo lang('last_week'); ?></option>
                            <option value="last_year"><?php echo lang('last_year'); ?></option>
                            <option value="this_year"><?php echo lang('this_year'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail2">Display</label>
                        <select class="form-control" id="report_display" name="report_display">
                            <option value="">Select</option>
                            <option value="by_month">By month</option>
                            
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
                    <th><?php echo lang('jan'); ?></th>
                    <th><?php echo lang('feb'); ?></th>
                    <th><?php echo lang('mar'); ?></th>
                    <th><?php echo lang('apr'); ?></th>
                    <th><?php echo lang('may'); ?></th>
                    <th><?php echo lang('jun'); ?></th>
                    <th><?php echo lang('jul'); ?></th>
                    <th><?php echo lang('aug'); ?></th>
                    <th><?php echo lang('sep'); ?></th>
                    <th><?php echo lang('oct'); ?></th>
                    <th><?php echo lang('nov'); ?></th>
                    <th><?php echo lang('dec'); ?></th>
                    <th><?php echo lang('total'); ?></th>

                </tr>
            </thead>
            <tbody>

                <?php
                //FULL CALANDER VARIABLES
                $total_jan_report = 0;
                $total_apr_report = 0;
                $total_jul_report = 0;
                $total_oct_report = 0;
                $total_feb_report = 0;
                $total_may_report = 0;
                $total_aug_report = 0;
                $total_nov_report = 0;
                $total_mar_report = 0;
                $total_jun_report = 0;
                $total_sep_report = 0;
                $total_dec_report = 0;
                

                foreach ($proft_loss as $key => $list) {
                    
                    $fy_year = date('Y', strtotime($from_date));
                    $fy_month = date('m', strtotime($from_date));

                    echo '<tr><td colspan="3">';
                    echo '<strong>' .($langs == 'en' ? $list['title'] : $list['title_ur']) . '</strong>';
                    echo '</td></tr>';

                    ///////
                    $pl_report = $this->M_groups->get_accounts_by_parent($list['account_code'],$_SESSION['company_id']);
                    // $pl_report = $this->M_reports->get_profit_loss($_SESSION['company_id'], $list['account_code'], $from_date, $to_date);
                    foreach ($pl_report as $key => $list) :
                        $total = 0;
                        
                        $total += $jan_report = $this->M_reports->get_amount_by_month($_SESSION['company_id'], '01',  $list['account_code']);
                        $total += $feb_report = $this->M_reports->get_amount_by_month($_SESSION['company_id'], '02',  $list['account_code']);
                        $total += $mar_report = $this->M_reports->get_amount_by_month($_SESSION['company_id'], '03',  $list['account_code']);
                        $total += $apr_report = $this->M_reports->get_amount_by_month($_SESSION['company_id'], '04',  $list['account_code']);
                        $total += $may_report = $this->M_reports->get_amount_by_month($_SESSION['company_id'], '05',  $list['account_code']);
                        $total += $jun_report = $this->M_reports->get_amount_by_month($_SESSION['company_id'], '06',  $list['account_code']);
                        $total += $jul_report = $this->M_reports->get_amount_by_month($_SESSION['company_id'], '07',  $list['account_code']);
                        $total += $aug_report = $this->M_reports->get_amount_by_month($_SESSION['company_id'], '08',  $list['account_code']);
                        $total += $sep_report = $this->M_reports->get_amount_by_month($_SESSION['company_id'], '09',  $list['account_code']);
                        $total += $oct_report = $this->M_reports->get_amount_by_month($_SESSION['company_id'], '10',  $list['account_code']);
                        $total += $nov_report = $this->M_reports->get_amount_by_month($_SESSION['company_id'], '11',  $list['account_code']);
                        $total += $dec_report = $this->M_reports->get_amount_by_month($_SESSION['company_id'], '12',  $list['account_code']);
                        
                        echo '<tr>';
                        echo '<td><a href="'.site_url('accounts/C_groups/accountDetail/'. $list['account_code']).'">'.($langs == 'en' ? $list['title'] : $list['title_ur']).'</a></td>';
                        //echo '<td>' . ($langs == 'en' ? $list['title'] : $list['title_ur']) . '</td>';
                        //$report = $this->M_dashboard->monthlySaleReport($_SESSION["company_id"],$fy_year,$list['name']);
                        echo '<td class="text-right">' . number_format($jan_report,1) . '</td>';
                        echo '<td class="text-right">' . number_format($feb_report,1) . '</td>';
                        echo '<td class="text-right">' . number_format($mar_report,1) . '</td>';
                        echo '<td class="text-right">' . number_format($apr_report,1) . '</td>';
                        echo '<td class="text-right">' . number_format($may_report,1) . '</td>';
                        echo '<td class="text-right">' . number_format($jun_report,1) . '</td>';
                        echo '<td class="text-right">' . number_format($jul_report,1) . '</td>';
                        echo '<td class="text-right">' . number_format($aug_report,1) . '</td>';
                        echo '<td class="text-right">' . number_format($sep_report,1) . '</td>';
                        echo '<td class="text-right">' . number_format($oct_report,1) . '</td>';
                        echo '<td class="text-right">' . number_format($nov_report,1) . '</td>';
                        echo '<td class="text-right">' . number_format($dec_report,1) . '</td>';
                        echo '<td class="text-right"><strong>' . number_format($total,1) . '</strong></td>';
                        echo '</tr>';

                        $total_jan_report += abs($jan_report);
                        $total_may_report += abs($may_report);
                        $total_sep_report += abs($sep_report);
                        $total_feb_report += abs($feb_report);
                        $total_jun_report += abs($jun_report);
                        $total_oct_report += abs($oct_report);
                        $total_mar_report += abs($mar_report);
                        $total_jul_report += abs($jul_report);
                        $total_nov_report += abs($nov_report);
                        $total_apr_report += abs($apr_report);
                        $total_aug_report += abs($aug_report);
                        $total_dec_report += abs($dec_report);
                        $total += abs($this->M_groups->get_account_total_balance($_SESSION['company_id'], FY_START_DATE, FY_END_DATE, $list['account_code']));
                    endforeach;
                    /////
                }
                ?>
            </tbody>
            <tfoot>
                
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