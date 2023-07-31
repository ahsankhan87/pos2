<div class="row">

	<?php
	
	echo 'client id'. getenv("CLIENT_ID");

	//for expense
	//////////////////
	$exp_balance = '';
	$title = '';
	if (@$expenses) {
		foreach ($expenses as $exp_val) :  //Expenses 
			//$dr_total = $this->M_ledgers->get_dr_total($exp_val['id']);
			//$cr_total = $this->M_ledgers->get_cr_total($exp_val ['id']);

			//$expense_chart .= '{value:"'.$exp_val['balance'].'", color:"'.$exp_color[$count].'", highlight:"'.$exp_color_hover[$count].'", label:"'.$exp_val['title'].'"},';

			$title .= '"' . $exp_val['title'] . '",';
			$exp_balance .= abs($exp_val['balance']) . ',';

		//$count ++;
		endforeach;
	}

	//convert into JSON for Chart
	$title = '[' . $title . ']';
	$exp_balance = '[' . $exp_balance . ']';
	$exp_color = array('#F7464A', '#46BFBD', '#FDB45C', '#949FB1', '#4D5360', '#4080bf', '#6699cc', '#009933', '#ace600', '#ff9900');
	$exp_color_hover = array('#FF5A5E', '#5AD3D1', '#FFC870', '#A8B3C5', '#616774', '#6699cc', '#bbff99', '#00cc44', '#c6ff1a', '#ffad33');

	?>

	<script>
		

		///////////////////////////
		//EXPENSE CHART
		var title = <?php echo $title; ?>;
		var exp_balance = <?php echo $exp_balance; ?>;
		var ExpenseData = {
			labels: title,
			datasets: [{
				data: exp_balance,
				backgroundColor: <?php echo json_encode($exp_color); ?>,
				borderColor: <?php echo json_encode($exp_color_hover); ?>,
				borderWidth: 1
			}]

		};
		window.onload = function() {

			//Monthly sale
			// var ctx = document.getElementById("canvas").getContext("2d");
			// var myBarChart = new Chart(ctx, {
			// 	type: 'bar',
			// 	data: barChartData,
			// 	//options: options
			// });
			////

			//for expenses myDoughnut chart
			var ctx1 = document.getElementById("chart-area").getContext("2d");
			var myDoughnutChart = new Chart(ctx1, {
				type: 'doughnut',
				data: ExpenseData,
				//options: options
			});
		}
	</script>

	<div class="col-md-6 col-sm-12">
		<!-- BEGIN PORTLET-->
		<div class="portlet">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart"></i><?php echo lang('total') . ' ' . lang('expenses'); ?>
				</div>
				<!--
                <div class="actions">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-default btn-sm active">
						<input type="radio" name="options" class="toggle">New </label>
						<label class="btn btn-default btn-sm">
						<input type="radio" name="options" class="toggle">Returning </label>
					</div>
				</div>
                -->
			</div>
			<div class="portlet-body">
				<div class="lead text-center"><?php echo lang('total') . ' ' . lang('expenses'); ?>: <?php echo $_SESSION['home_currency_symbol'] . ' ' . number_format($total_expenses, 2); ?></div>
				<div id="site_statistics_loading" style="display: none;">
					<img src="<?php echo base_url(); ?>assets/img/loading.gif" alt="loading">
				</div>
				<div id="site_statistics_content" class="display-none" style="display: block;">
					<style>
						#canvas-holder {
							width: 100%;
						}

						#expense_chart tr {}
					</style>

					<div id="canvas-holder" class="center-block">
						<canvas id="chart-area"></canvas>
					</div>


				</div>
			</div>
		</div>
		<!-- END PORTLET-->
	</div>

	<div class="col-md-6 col-sm-6">
		<div class="portlet">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-bank"></i><?php echo lang('profit_loss'); ?>
				</div>
				<!--<div class="actions">-->
				<!--	<div class="btn-group">-->
				<!--		<a class="btn btn-default btn-sm dropdown-toggle" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">-->
				<!--		Filter By <i class="fa fa-angle-down"></i>-->
				<!--		</a>-->
				<!--		<div class="dropdown-menu hold-on-click dropdown-checkboxes pull-right">-->
				<!--			<label><div class="checker"><span><input type="checkbox"></span></div> Finance</label>-->
				<!--			<label><div class="checker"><span class="checked"><input type="checkbox" checked=""></span></div> Membership</label>-->
				<!--			<label><div class="checker"><span><input type="checkbox"></span></div> Customer Support</label>-->
				<!--			<label><div class="checker"><span class="checked"><input type="checkbox" checked=""></span></div> HR</label>-->
				<!--			<label><div class="checker"><span><input type="checkbox"></span></div> System</label>-->
				<!--		</div>-->
				<!--	</div>-->
				<!--</div>-->
			</div>
			<div class="portlet-body">
				<?php
				//echo date_default_timezone_get();
				$total_amount = (float) (($total_revenue + $total_expenses) == 0 ? 1 : ($total_revenue + $total_expenses));
				$income_percent = (float) round($total_revenue * 100 / $total_amount, 2);
				$expense_percent = (float) round($total_expenses * 100 / $total_amount, 2);
				?>
				<div class="lead text-center"><?php echo lang('net_income') ?> <?php echo $_SESSION['home_currency_symbol'] . ' ' . number_format($net_income, 2); ?></div>
				<?php echo lang('income') ?> <?php echo $_SESSION['home_currency_symbol'] . ' ' . number_format($total_revenue, 2); ?>
				<div class="progress">
					<div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $income_percent . '%' ?>">
						<span class="sr-only">
							<?php echo $income_percent . '% Income' ?>
						</span>
					</div>
				</div>
				<?php echo lang('expenses') ?> <?php echo $_SESSION['home_currency_symbol'] . ' ' . number_format($total_expenses, 2); ?>
				<div class="progress">
					<div class="progress-bar progress-bar-danger" role="progressbar"  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $expense_percent.'%'?>">
						<span class="sr-only">
							<?php echo $expense_percent . '% Expense' ?>
						</span>
					</div>
				</div>
			</div>

		</div>

	</div>

</div>
<div class="clearfix">

	<div class="row">

		<div class="col-md-6 col-sm-6">
			<div class="portlet">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-bank"></i>Current Assets
					</div>
					<!--<div class="actions">-->
					<!--	<div class="btn-group">-->
					<!--		<a class="btn btn-default btn-sm dropdown-toggle" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">-->
					<!--		Filter By <i class="fa fa-angle-down"></i>-->
					<!--		</a>-->
					<!--		<div class="dropdown-menu hold-on-click dropdown-checkboxes pull-right">-->
					<!--			<label><div class="checker"><span><input type="checkbox"></span></div> Finance</label>-->
					<!--			<label><div class="checker"><span class="checked"><input type="checkbox" checked=""></span></div> Membership</label>-->
					<!--			<label><div class="checker"><span><input type="checkbox"></span></div> Customer Support</label>-->
					<!--			<label><div class="checker"><span class="checked"><input type="checkbox" checked=""></span></div> HR</label>-->
					<!--			<label><div class="checker"><span><input type="checkbox"></span></div> System</label>-->
					<!--		</div>-->
					<!--	</div>-->
					<!--</div>-->
				</div>
				<div class="portlet-body">
					<div class="table-scrollable" style="min-height:230px">
						<table class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th><?php echo lang('account');?></th>
									<th class="text-right"><?php echo lang('balance');?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($current_assets as $list) :
									//OPENING BALANCES
									$op_balance_dr = (float) $list['op_balance_dr'];
									$op_balance_cr = (float) $list['op_balance_cr'];
									$op_balance = ($op_balance_dr - $op_balance_cr);

									//CURRENT BALANCES
									// $cur_balance = $this->M_banking->get_bank_total_balance($list['id'], FY_START_DATE, FY_END_DATE);
									$cur_balance = $this->M_groups->get_account_total_balance($_SESSION['company_id'], FY_START_DATE, FY_END_DATE, $list['account_code']);
									// $balance_dr = (double) $cur_balance[0]['dr_balance'];
									// $balance_cr = (double) $cur_balance[0]['cr_balance'];

									echo '<tr>';
									echo '<td>';
									echo '<a href="' . site_url('accounts/C_groups/accountDetail/' . $list['account_code']) . '">' . $list['title'] . '</a>';
									echo '</td>';

									echo '<td class="text-right">' . number_format(($op_balance_dr - $op_balance_cr) + ($cur_balance), 2) . '</td>';
									echo '</td></tr>';
								endforeach;
								?>
							</tbody>
						</table>
						<div class="pull-right "><a class="margin-bottom-5" href="<?php echo site_url('accounts/C_groups'); ?>">View all</a></div>
					</div>

				</div>

			</div>
		</div>
	</div>
</div>