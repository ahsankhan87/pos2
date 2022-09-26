<!-- END OVERVIEW STATISTIC BARS-->
<div class="clearfix">
</div>
<div class="row">
	<div class="col-md-6 col-sm-12">
		<!-- BEGIN PORTLET-->
		<div class="portlet">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart"></i><?php echo lang('monthly_sales'); ?>
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
				<?php
				//get total monthly unit price of sold items i.e total monthly sale for the chart
				$month = ""; //for chart
				$price = ""; //for chart
				//var_dump($monthlySaleReport);
				foreach ($monthly_sale as $values) :

					$month .= '"' . $values['month'] . '",';
					$price .= abs($values['amount']) . ',';

				endforeach;

				//convert into JSON for Chart
				$month = '[' . $month . ']';
				$price = '[' . $price . ']';
				///////////////////////////////////////

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
				<div id="site_statistics_loading" style="display: none;">
					<img src="<?php echo base_url(); ?>assets/img/loading.gif" alt="loading">
				</div>
				<div id="site_statistics_content" class="display-none" style="display: block;">
					<div>
						<canvas id="canvas"></canvas>
					</div>
					<script>
						////////////////////////////////////
						////MONTHLY SALES CHART
						var months = <?php echo $month; ?>;
						var price = <?php echo $price; ?>;
						var barChartData = {
							labels: months,
							datasets: [{
								label: 'Monthly Sales',
								//fillColor: "rgba(120,220,220,0.5)",
								//strokeColor: "rgba(140,220,220,0.8)",
								//highlightFill: "rgba(120,220,220,0.75)",
								//highlightStroke: "rgba(140,220,220,1)",
								data: price,
								backgroundColor: [
									'rgba(255, 99, 132, 0.2)',
									'rgba(54, 162, 235, 0.2)',
									'rgba(255, 206, 86, 0.2)',
									'rgba(75, 192, 192, 0.2)',
									'rgba(153, 102, 255, 0.2)',
									'rgba(255, 159, 64, 0.2)'
								],
								borderColor: [
									'rgba(255,99,132,1)',
									'rgba(54, 162, 235, 1)',
									'rgba(255, 206, 86, 1)',
									'rgba(75, 192, 192, 1)',
									'rgba(153, 102, 255, 1)',
									'rgba(255, 159, 64, 1)'
								],
								borderWidth: 1
							}]

						};

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
							var ctx = document.getElementById("canvas").getContext("2d");
							var myBarChart = new Chart(ctx, {
								type: 'bar',
								data: barChartData,
								//options: options
							});
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
				</div>
			</div>
		</div>
		<!-- END PORTLET-->
	</div>
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
				<div class="lead text-center">Total Expense: <?php echo number_format($total_expenses, 2); ?></div>
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
</div>
<div class="clearfix">

	<div class="row">

		<div class="col-md-6 col-sm-6">
			<div class="portlet">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-bank"></i>Banks
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
									<th>Banks</th>
									<th class="text-right">Balance</th>
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
									$cur_balance = $this->M_groups->get_account_balance($_SESSION['company_id'], FY_START_DATE, FY_END_DATE, $list['account_code']);
									// $balance_dr = (double) $cur_balance[0]['dr_balance'];
									// $balance_cr = (double) $cur_balance[0]['cr_balance'];

									echo '<tr>';
									echo '<td>';
									echo '<a href="' . site_url('pos/C_banking/bankDetail/' . $list['id']) . '">' . $list['title'] . '</a>';
									echo '</td>';

									echo '<td class="text-right">' . number_format(($op_balance_dr - $op_balance_cr) + ($cur_balance), 2) . '</td>';
									echo '</td></tr>';
								endforeach;
								?>
							</tbody>
						</table>
						<div class="pull-right "><a class="margin-bottom-5" href="<?php echo site_url('pos/C_banking/'); ?>">View all</a></div>
					</div>

				</div>

			</div>
		</div>


		<div class="col-md-6 col-sm-6">
			<div class="portlet">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-bank"></i>Profit & Loss
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

					<h3>Net Income <strong><?php echo number_format($net_income,2); ?></strong></h3>
					Income
					<div class="progress">
						<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
							<span class="sr-only">
								40% Complete (success) </span>
						</div>
					</div>
					Expenses
					<div class="progress">
						<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
							<span class="sr-only">
								20% Complete </span>
						</div>
					</div>
				</div>

			</div>

		</div>
	</div>
</div>