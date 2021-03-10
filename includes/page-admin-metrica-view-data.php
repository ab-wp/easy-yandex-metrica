<div class="wrap">
	<h2><?php _e('Traffic', 'easy-yandex-metrica') ?></h2>

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
		type: 'bar',
		data: {
			labels: [<?php echo $view_data_gr['categories']; ?>],
			datasets: [{
				label: '<?php _e( 'Visits', 'easy-yandex-metrica' ) ?>',
				data: [<?php echo $view_data_gr['data']; ?>],
				backgroundColor: [<?php echo $view_data_gr['color']; ?>],
				borderColor: [<?php echo $view_data_gr['color']; ?>],
				borderWidth: 1
			}]
		},
		options: {
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true
					}
				}]
			}
		}
	});
	</script>
	<!-- -->
	<table class="wp-list-table widefat fixed striped pages">
		<thead>
			<tr>
				<th></th>
				<?php 
				if (isset($data['query']['metrics'])):
					foreach ($data['query']['metrics'] as $key => $value): ?>
						<th><?php echo $this->get_metric_title($value); ?></th>
					<?php endforeach; 
				endif;	
				?>
			</tr>
		</thead>
		<tbody>
		<?php
			if (isset($view_data[0]['time_intervals'])) {
				$max_element = count($view_data)-1;
				for($i=0;$i<=$max_element;$i++) : 
					if ('hour' != $view_data[$i]['time_intervals_type']) { //если не часы надо отобразить данные в обратном порядке
						$i_num = $max_element-$i;
					} else {
						$i_num = $i;
					}?>
				<tr>
					<?php 
						if (('day' == $view_data[$i_num]['time_intervals_type']) && 
							((0==$view_data[$i_num]['time_intervals_day_type']) || (6==$view_data[$i_num]['time_intervals_day_type']))){
							$style = ' style="color:red;"';
						} else {
							$style ='';
						} ?>
					<td<?php echo $style; ?>><?php echo $view_data[$i_num]['time_intervals'];?>
					</td>
					<?php foreach ($view_data[$i_num]['metrics'] as $key => $value) : ?>
						<td><?php echo $value; ?></td>
					<?php endforeach; ?>
				</tr>
				<?php endfor; 
			} else {
				if (isset($view_data[0]['metrics'])) {
					$colspan = ' colspan="'.(count($view_data[0]['metrics'])+1).'"';
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