<div class="wrap">
	<h2><?php _e('Visits', 'easy-yandex-metrica') ?></h2>

	<table  style="width:100%;">
		<tr>
			<td style="width:50%;font-size:1rem;"><?php _e('Today', 'easy-yandex-metrica') ?>:</td>
			<td style="width:50%;font-size:1rem;"><?php _e('Week', 'easy-yandex-metrica') ?>:</td></tr>
		<tr>
			<td style="font-size:2rem;line-height:2.1rem;"><?php echo $visitsToday; ?></td>
			<td style="font-size:2rem;line-height:2.25rem;"><?php echo $visitsTotal; ?></td></tr>
	</table>
	<!-- -->
	<div style="margin:10px auto 20px;">
		<canvas id="char_ya" height="100" style="background:#fff;padding:10px 0;"></canvas>
		<div style="text-align:right;background:#fff;padding:10px;"><?php _e( 'Source: <a href="https://metrica.yandex.com" target="_blank">metrica.yandex.com</a>', 'easy-yandex-metrica' ) ?></div>
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
</div>