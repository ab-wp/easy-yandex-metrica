<div class="wrap">
	<h2><?php echo $page_title; ?></h2>

	<?php echo $this->get_filters(); ?>
	<!-- -->
	<div style="margin:10px auto 20px;">
		<div style="text-align:center;background:#fff;padding:10px;"><h3><?php echo $view_data_gr['title']; ?></h3></div>
		<div style="text-align:right;background:#fff;padding:10px;"><?php _e( 'Source: <a href="https://metrica.yandex.com" target="_blank">metrica.yandex.com</a>', 'easy-yandex-metrica' ) ?></div>
		<canvas id="char_ya" height="100" style="background:#fff;padding:10px 0;"></canvas>
	</div>
	<!-- -->
	<script>
	var ctx = document.getElementById('char_ya').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: [<?php echo $view_data_gr['labels']; ?>],
			datasets: [
				<?php
				for ($i=0;$i<(count($view_data_gr['datasets']));$i++) {
					echo "
					{
						label: ".$view_data_gr['datasets'][$i]['label'].",
						backgroundColor: ".$view_data_gr['datasets'][$i]['color'].",
						borderColor: ".$view_data_gr['datasets'][$i]['color'].",
						data: [ ".$view_data_gr['datasets'][$i]['data']."],
						fill: false,
					},
					";
				}
				?>
				]
		},
	});
	</script>
	<!-- -->
	<table class="wp-list-table widefat fixed striped pages">
		<thead>
			<tr>
				<th></th>
				<?php 
				if (isset($view_data['title']['metrics_name'])) :
					foreach ($view_data['title']['metrics_name'] as $key => $value): ?>
					<th><?php echo $this->get_metric_title($value); ?></th>
				<?php 
					endforeach; 
				endif; ?>
			</tr>
		</thead>
		<tbody>
		<?php
			if (isset($view_data['data'])) {
				$max_element = count($view_data['data'])-1;
				for($i=0;$i<=$max_element;$i++) : 
					?>
				<tr>
					<td><?php echo $view_data['data'][$i]['name']; ?></td>
					<?php foreach ($view_data['data'][$i]['metrics_total'] as $key => $value) : ?>
						<td><?php echo $value; ?></td>
					<?php endforeach; ?>
				</tr>
				<?php endfor; ?>
				<?php
			} else {
				if (isset($view_data['title']['metrics_name'])) {
					$colspan = ' colspan="'.(count($view_data['title']['metrics_name'])+1).'"';
				} else {
					$colspan = '';
				}
				echo '<tr><td'.$colspan.'><b>';
				_e( 'No data for the selected period', 'easy-yandex-metrica' );
				echo '</b></td></tr>';
			}
			?>
		</tbody>
	</table>
</div>