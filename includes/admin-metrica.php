<?php
if ( !class_exists( 'ABWP_Admin_Metrica' ) ) {
	class ABWP_Admin_Metrica
	{
		public $yandex_metrika_token = '';
		public $yandex_metrika_counter_id = '';
		public $yandex_metrika_client_id = '598a46c6a37243b0846f9b1a965c91a9';
		public $date1 = '';
		public $date2 = '';
		public $group = '';
		public $period = '';
		public $base_url = '';

		public function __construct ()
		{
			$this->yandex_metrika_token = get_option('abwp_eym_token');
			$this->yandex_metrika_counter_id = get_option('abwp_eym_counter_id');
			//
			if (isset($_GET['period']) && !empty($_GET['period'])) {
				if (preg_match('/^[a-zA-Z][a-zA-Z0-9-_]{1,10}$/i', $_GET['period'])) {
					$period = $_GET['period'];
				} else {
					$period = 'week';	
				}
			} else {
				$period = 'week';
			}
			//
			if (isset($_GET['group']) && !empty($_GET['group'])) {
				if (preg_match('/^[a-zA-Z][a-zA-Z0-9-_]{1,10}$/i', $_GET['group'])) {
					$group = $_GET['group'];
				} else {
					$group = '';	
				}
			} else {
				$group = '';
			}
			//
			if (isset($_GET['page']) && !empty($_GET['page'])) {
				if (preg_match('/^[a-zA-Z][a-zA-Z0-9-_]{1,30}$/i', $_GET['page'])) {
					$page = $_GET['page'];
				} else {
					$page = '';	
				}
			} else {
				$page = '';
			}
			$this->base_url = get_admin_url(null, 'admin.php?page='.$page);
			//
			$this->init_date($period, $group);
		}

		public function view() {
			if ('' == $this->yandex_metrika_counter_id) {
				$this->view_settings();
			} else {
				$this->view_data();
			}
		}

		private function view_data()
		{
			$array_url_data = array(
				'preset' => 'traffic',
				//'metrics' => 'ym:s:visits,ym:s:users,ym:s:pageviews,ym:s:percentNewVisitors,ym:s:bounceRate,ym:s:pageDepth,ym:s:avgVisitDurationSeconds',
				'group' => $this->group,
				'date1' => $this->date1,
				'date2' => $this->date2,
				'limit' => 366,
				'ids' => $this->yandex_metrika_counter_id,
				'oauth_token' => $this->yandex_metrika_token,
			);
			$url = 'https://api-metrika.yandex.net/stat/v1/data/bytime?'. http_build_query($array_url_data);
			//
			
			$data = json_decode( $this->get_json_data($url), true);
			ob_start();
			if (!$data) {
				include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-error.php';
			} else {
				// 
				$view_data_title = '';
				$view_data = $this->create_data($data);
				$view_data_gr = $this->create_data_columnchart($view_data);
				//
				include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-view-data.php';
			}
			$content = ob_get_clean();
			echo $content;	
		}

		public function viewDashboard()
		{
			if (empty($this->yandex_metrika_token)) {
				echo '<p>'.__('The plugin is not configured!', 'easy-yandex-metrica').'</p>';
				echo '<a href="'.admin_url( 'admin.php' ).'?page=abwp_eym_settings'.'" class="button button-primary">'.__('Go to Settings', 'easy-yandex-metrica').'</a>';
				return;
			}
			$array_url_data = array(
				'preset' => 'traffic',
				//'metrics' => 'ym:s:visits,ym:s:users,ym:s:pageviews,ym:s:percentNewVisitors,ym:s:bounceRate,ym:s:pageDepth,ym:s:avgVisitDurationSeconds',
				'group' => $this->group,
				'date1' => $this->date1,
				'date2' => $this->date2,
				'limit' => 366,
				'ids' => $this->yandex_metrika_counter_id,
				'oauth_token' => $this->yandex_metrika_token,
			);
			$url = 'https://api-metrika.yandex.net/stat/v1/data/bytime?'. http_build_query($array_url_data);
			//
			
			$data = json_decode( $this->get_json_data($url), true);
			ob_start();
			if (!$data) {
				include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-error.php';
			} else {
				// 
				$view_data_title = '';
				$view_data = $this->create_data($data);
				$view_data_gr = $this->create_data_columnchart($view_data);
				$visitsToday = 0;
				$visitsTotal = 0;
				for ($i=0;$i<count($view_data);$i++) {
					$visitsTotal += $view_data[$i]['metrics'][0];
					if ($i == (count($view_data)-1)) {
						$visitsToday = $view_data[$i]['metrics'][0];
					}
				}
				
				//
				include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-view-data-dashboard.php';
			}
			$content = ob_get_clean();
			echo $content;	
		}

		/***
		 * View Sources data
		 */
		public function view_data_sources()
		{
			$page_title = __('Sources, Summary', 'easy-yandex-metrica');
			$array_url_data = array(
				'preset' => 'sources_summary',
				'group' => $this->group,
				'date1' => $this->date1,
				'date2' => $this->date2,
				'limit' => 366,
				'ids' => $this->yandex_metrika_counter_id,
				'oauth_token' => $this->yandex_metrika_token,
			);
			$url = 'https://api-metrika.yandex.net/stat/v1/data/bytime?'. http_build_query($array_url_data);
			//
			$data = json_decode( $this->get_json_data($url), true);
			ob_start();
			if (!$data) {
				include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-error.php';
			} else {
				$view_data = $this->create_data_sources($data);
				$view_data_gr = $this->create_data_source_columnchart($data);
				//
				include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-view-data-sources.php';	
			}
			$content = ob_get_clean();
			echo $content;
		}

		/***
		 * View Sources engines
		 */
		public function view_data_sources_engines()
		{
			$page_title = __('Sources, Search engine', 'easy-yandex-metrica');
			$array_url_data = array(
				'preset' => 'search_engines',
				'group' => $this->group,
				'date1' => $this->date1,
				'date2' => $this->date2,
				'limit' => 366,
				'ids' => $this->yandex_metrika_counter_id,
				'oauth_token' => $this->yandex_metrika_token,
			);
			$url = 'https://api-metrika.yandex.net/stat/v1/data/bytime?'. http_build_query($array_url_data);
			//
			$data = json_decode( $this->get_json_data($url), true);
			ob_start();
			if (!$data) {
				include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-error.php';
			} else {
				$view_data = $this->create_data_sources($data);
				$view_data_gr = $this->create_data_source_columnchart($data);
				//
				include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-view-data-sources.php';
			}
			$content = ob_get_clean();
			echo $content;	
		}

		/***
		 * View Sources sites
		 */
		public function view_data_sources_sites()
		{
			$page_title = __('Sources, Sites', 'easy-yandex-metrica');
			$array_url_data = array(
				'preset' => 'sources_sites',
				'group' => $this->group,
				'date1' => $this->date1,
				'date2' => $this->date2,
				'limit' => 366,
				'ids' => $this->yandex_metrika_counter_id,
				'oauth_token' => $this->yandex_metrika_token,
			);
			$url = 'https://api-metrika.yandex.net/stat/v1/data/bytime?'. http_build_query($array_url_data);
			//
			$data = json_decode( $this->get_json_data($url), true);
			ob_start();
			if (!$data) {
				include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-error.php';
			} else {
				$view_data = $this->create_data_sources($data);
				$view_data_gr = $this->create_data_source_columnchart($data);
				//
				include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-view-data-sources.php';
			}
			$content = ob_get_clean();
			echo $content;	
		}

		/***
		 * View Sources Social Network
		 */
		public function view_data_sources_social()
		{
			$page_title = __('Sources, Social Network', 'easy-yandex-metrica');
			$array_url_data = array(
				'preset' => 'sources_social',
				'group' => $this->group,
				'date1' => $this->date1,
				'date2' => $this->date2,
				'limit' => 366,
				'ids' => $this->yandex_metrika_counter_id,
				'oauth_token' => $this->yandex_metrika_token,
			);
			$url = 'https://api-metrika.yandex.net/stat/v1/data/bytime?'. http_build_query($array_url_data);
			//
			$data = json_decode( $this->get_json_data($url), true);
			ob_start();
			if (!$data) {
				include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-error.php';
			} else {
				$view_data = $this->create_data_sources($data);
				$view_data_gr = $this->create_data_source_columnchart($data);
				//	
				include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-view-data-sources.php';
			}
			$content = ob_get_clean();
			echo $content;	
		}

		public function view_settings()
		{
			ob_start();
			include plugin_dir_path( __FILE__ ) . 'page-admin-metrica-settings.php';
			$content = ob_get_clean();
			echo $content;	
		}

		public function get_metric_title ($metric_code)
		{
			$title = array(
				'ym:s:visits' => __( 'Visits', 'easy-yandex-metrica' ),
				'ym:s:pageviews' => __( 'Views', 'easy-yandex-metrica' ), 
				'ym:s:users' => __( 'Visitors', 'easy-yandex-metrica' ),
				'ym:s:percentNewVisitors' => __( 'Proportion of new visitors', 'easy-yandex-metrica' ),
				'ym:s:bounceRate' => __( 'Refusal', 'easy-yandex-metrica' ),
				'ym:s:pageDepth' => __( 'Depth of view', 'easy-yandex-metrica' ),
				'ym:s:avgVisitDurationSeconds' => __( 'Time, min.', 'easy-yandex-metrica' ), 
				);
			if (isset($title[$metric_code])) {
				return $title[$metric_code];	
			} else {
				return $metric_code;
			}	
		}

		public function get_data_title ($data_code)
		{
			$title = array(
				'Search engine traffic' => __( 'Search engine traffic', 'easy-yandex-metrica' ),
				'Direct traffic' => __( 'Direct traffic', 'easy-yandex-metrica' ), 
				'Internal traffic' => __( 'Internal traffic', 'easy-yandex-metrica' ),
				'Link traffic' => __( 'Link traffic', 'easy-yandex-metrica' ),
				'Social network traffic' => __( 'Social network traffic', 'easy-yandex-metrica' ),
				'Cached page traffic' => __( 'Cached page traffic', 'easy-yandex-metrica' ),
				'Messenger traffic' => __( 'Messenger traffic', 'easy-yandex-metrica' ), 
				);
			if (isset($title[$data_code])) {
				return $title[$data_code];	
			} else {
				return $data_code;
			}	
		}

		/*
		* создаем массив для визуализации
		*/
		private function create_data_columnchart($data)
		{

			$return  = array('categories' => '', 'data' => '', 'color'=> '', 'title' => '', 'subtitle' => '');
			if ('year' == $this->period) {
				$return['title'] = __( 'Visits for the year', 'easy-yandex-metrica' );
			} elseif ('quarter' == $this->period) {
				$return['title'] = __( 'Visits for the quarter', 'easy-yandex-metrica' );
			} elseif ('month' == $this->period) {
				$return['title'] = __( 'Visits for the month', 'easy-yandex-metrica' );
			} elseif ('week' == $this->period) {
				$return['title'] = __( 'Visits per week', 'easy-yandex-metrica' );
			} else {
				if ('yesterday' == $this->period) {
					$day = date('d.m.Y', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
				} else {
					$day = date('d.m.Y');
				}
				$return['title'] = __( 'Visits', 'easy-yandex-metrica' ).' '.$day;
			} 
			$return['subtitle'] = __( 'Source: <a href="https://metrica.yandex.com" target="_blank">metrica.yandex.com</a>', 'easy-yandex-metrica' );
			/***/
			for ($i=0;$i<count($data);$i++) {
				if ('hour' == $data[$i]['time_intervals_type']) {
					$time_intervals_tmp = explode(' ', $data[$i]['time_intervals']);
					$time_intervals = $time_intervals_tmp[1];
				} else {
					$time_intervals = $data[$i]['time_intervals'];
				}
				$return['categories'] .= "'".$time_intervals."',";
				$return['data'] .= $data[$i]['metrics'][0].",";
				if (('day' == $data[$i]['time_intervals_type']) && ((0==$data[$i]['time_intervals_day_type']) || (6==$data[$i]['time_intervals_day_type']))){
					$return['color'] .= "'#e8b0fa',";
				} else {
					$return['color'] .= "'#c3b0fa',";
				}
			}
			return $return;
		}

		private function create_data_source_columnchart($data)
		{
			$colors = array('#98c363', '#f6cd56', '#e95437');
			$return  = array('title' => '', 'labels' => '', 'datasets' => array());
			// Create title
			if ('year' == $this->period) {
				$return['title'] = __( 'Visits for the year', 'easy-yandex-metrica' );
			} elseif ('quarter' == $this->period) {
				$return['title'] = __( 'Visits for the quarter', 'easy-yandex-metrica' );
			} elseif ('month' == $this->period) {
				$return['title'] = __( 'Visits for the month', 'easy-yandex-metrica' );
			} elseif ('week' == $this->period) {
				$return['title'] = __( 'Visits per week', 'easy-yandex-metrica' );
			} else {
				if ('yesterday' == $this->period) {
					$day = date('d.m.Y', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
				} else {
					$day = date('d.m.Y');
				}
				$return['title'] = __( 'Visits', 'easy-yandex-metrica' ).' '.$day;
			}
			//Create labels
			if (isset($data['time_intervals'])) {
				$num_time_intervals = count($data['time_intervals']);
			} else {
				$num_time_intervals = 0;
			}
			for ($i=0;$i<$num_time_intervals;$i++) {
				$time_intervals = $data['time_intervals'][$i][0];
				$return['labels'] .= "'".$time_intervals."',";
			}
			//Create datasets
			if (isset($data['data'])) {
				$i_count_dataset = (3<= count($data['data']))?3:count($data['data']);
			} else {
				$i_count_dataset = 0;
			}
			for ($i_data_result = 0; $i_data_result <= ($i_count_dataset-1); $i_data_result++) {
				$return['datasets'][$i_data_result]['label'] = "'".$this->get_data_title($data['data'][$i_data_result]['dimensions'][0]['name'])."'";
				for($i=0;$i<(count($data['data'][$i_data_result]['metrics'][0]));$i++) {
					if (!isset($return['datasets'][$i_data_result]['data'])) {
						$return['datasets'][$i_data_result]['data'] = '';
					}
					$return['datasets'][$i_data_result]['data'] .= $data['data'][$i_data_result]['metrics'][0][$i].',';
				}
				$return['datasets'][$i_data_result]['color'] = "'".$colors[$i_data_result]."'";
			}
			return $return;
		}

		private function create_data($data)
		{
			$return = array();
			$num_metrics = count($data['query']['metrics']);
			$num_time_intervals = count($data['time_intervals']);
			for ($i_time_intervals=0;$i_time_intervals<=($num_time_intervals-1);$i_time_intervals++) {
				$return[$i_time_intervals]['time_intervals'] = $this->format_time_intervals_name(
													$data['time_intervals'][$i_time_intervals][0],
													$data['time_intervals'][$i_time_intervals][1],
													$data['query']['group']
													);
				for ($i_num_metrics=0;$i_num_metrics<$num_metrics;$i_num_metrics++) {
					$return[$i_time_intervals]['metrics'][$i_num_metrics] = $this->format_data(
						$data['totals'][$i_num_metrics][$i_time_intervals],
						$data['query']['metrics'][$i_num_metrics]);
				}
				$return[$i_time_intervals]['time_intervals_type'] = $data['query']['group'];
				if ('day' == $data['query']['group']) {
					$return[$i_time_intervals]['time_intervals_day_type'] = date('w',strtotime($data['time_intervals'][$i_time_intervals][0]));
				}
			}
			return $return;
		}

		private function create_data_sources($data)
		{
			$return = array();
			//
			if (isset($data['query']) && isset($data['query']['metrics'])) {
				$count_metrics = count($data['query']['metrics']);
			} else {
				$count_metrics = 0;
			}
			//
			if (isset($data['time_intervals'])) {
				$num_time_intervals = count($data['time_intervals']);
			} else {
				$num_time_intervals = 0;
			}
			//
			if (isset($data['data'])) {
				$count_data_row = count($data['data']);
			} else {
				$count_data_row = 0;
			}
			//
			for ($i_metrics = 0; $i_metrics <= ($count_metrics-1); $i_metrics++) {
				$return['title']['metrics_name'][$i_metrics] = $data['query']['metrics'][$i_metrics];
			}	
			//
			for ($i_data_result = 0; $i_data_result <= ($count_data_row-1); $i_data_result++) {
				$return['data'][$i_data_result]['name'] = $this->get_data_title($data['data'][$i_data_result]['dimensions'][0]['name']);
				for ($i_metrics = 0; $i_metrics <= ($count_metrics-1); $i_metrics++) {
					if (('ym:s:bounceRate' == $return['title']['metrics_name'][$i_metrics]) || 
					('ym:s:pageDepth' == $return['title']['metrics_name'][$i_metrics]) ||
					('ym:s:avgVisitDurationSeconds' == $return['title']['metrics_name'][$i_metrics])) {
						//$value = array_sum($data['data'][$i_data_result]['metrics'][$i_metrics])/count($data['data'][$i_data_result]['metrics'][$i_metrics]);
						$numNotNull=0;
						$sum = 0;
						for ($i=0;$i<count($data['data'][$i_data_result]['metrics'][$i_metrics]);$i++) {
							$sum += $data['data'][$i_data_result]['metrics'][$i_metrics][$i];
							if (0 < $data['data'][$i_data_result]['metrics'][$i_metrics][$i]) {
								$numNotNull++;
							}
							if (0 < $numNotNull) {
								$value = $sum/$numNotNull;
							} else {
								$value = 0;
							}
						}
					} else {
						$value = array_sum($data['data'][$i_data_result]['metrics'][$i_metrics]);
					}
					$return['data'][$i_data_result]['metrics_total'][$i_metrics] = $this->format_data($value, $return['title']['metrics_name'][$i_metrics]);
				}	
			}
			return $return;
		}

		/***
		* Преобразовываем интервал времени в зависимости от типа для красоты
		***/
		private function format_time_intervals_name ($data1, $data2, $type)
		{
			if ('day' == $type) {
				$time=strtotime($data1);
				$data1 = date('d.m.Y',$time);
				$time_intervals_name = $data1;
			} elseif ('hour' == $type) {
				$time=strtotime($data1);
				$data1 = date('d.m.Y H:i',$time);
				$time_intervals_name = $data1;
				//$time_intervals_name = $time1[1];
			} elseif ('month' == $type) {
				$time=strtotime($data1);
				$data1 = date('m.Y',$time);
				$time_intervals_name = $data1;
				//$time_intervals_name = $time1[1];
			} else {
				$time=strtotime($data1);
				$data1 = date('d.m.Y',$time);
				$time=strtotime($data2);
				$data2 = date('d.m.Y',$time);
				$time_intervals_name = $data1.' - '.$data2;
			}
			return $time_intervals_name;
		}

		/***
		* Преобразовываем данные в зависимости от типа для красоты
		***/
		public function format_data ($data, $type)
		{
			if (('ym:s:percentNewVisitors' == $type) ||
				('ym:s:bounceRate' == $type)) {
				$data = round($data, 1). ' %';
			} elseif ('ym:s:pageDepth' == $type) {
				$data = round($data, 2);
			} elseif ('ym:s:avgVisitDurationSeconds' == $type) {
				$data = date("i:s", mktime(0, 0, round($data)));
			}
			return $data;
		}

		/***
		* Инициализируем даты
		***/
		private function init_date($period, $group)
		{
			//$this->date2 = date('Ymd');
			$this->date2 = 'today';
			switch ($period) {
				case 'today':
					$this->date1 = 'today';
					$this->date2 = 'today';
					$this->period = 'today';
					break;

				case 'yesterday':
					$this->date1 = 'yesterday';
					$this->date2 = 'yesterday';
					$this->period = 'yesterday';
					break;

				case 'month':
					$this->date1 = date('Ymd',strtotime("-30 day"));
					$this->period = 'month';
					break;

				case 'quarter':
					$this->date1 = date('Ymd',strtotime("-90 day"));
					$this->period = 'quarter';
					break;

				case 'year':
					$this->date1 = date('Ymd',strtotime("-365 day"));
					$this->period = 'year';
					break;

				default: //по умолчанию week
					$this->date1 = date('Ymd',strtotime("-6 day"));
					$this->period = 'week';
					break;
			}

			if (('today' == $this->period) || ('yesterday' == $this->period)) {
				$this->group = 'hour';
			} elseif ((('year' == $this->period) || ('quarter' == $this->period)) && empty($group)) { //Для года и квартала по умолчанию неделя
				$this->group = 'week';
			} else {
				switch ($group) {		

					case 'week':
						$this->group = 'week';
						break;

					case 'month':
						$this->group = 'month';
						break;

					default: //по умолчанию день
						$this->group = 'day';
						break;
				}
			}
		}

		public function get_filters ()
		{
			$filter = '<div class="wp-filter"><ul class="filter-links">';
			
			$class = ('today' == $this->period)?' class="current"':'';
			$filter .= '<li><a href="'.$this->base_url.'&period=today"'.$class.'>'.__('Today', 'easy-yandex-metrica').'</a></li>';

			$class = ('yesterday' == $this->period)?' class="current"':'';
			$filter .= '<li><a href="'.$this->base_url.'&period=yesterday"'.$class.'>'.__('Yesterday', 'easy-yandex-metrica').'</a></li>';

			$class = ('week' == $this->period)?' class="current"':'';
			$filter .= '<li><a href="'.$this->base_url.'&period=week"'.$class.'>'.__('Week', 'easy-yandex-metrica').'</a></li>';

			$class = ('month' == $this->period)?' class="current"':'';
			$filter .= '<li><a href="'.$this->base_url.'&period=month"'.$class.'>'.__('Month', 'easy-yandex-metrica').'</a></li>';

			$class = ('quarter' == $this->period)?' class="current"':'';
			$filter .= '<li><a href="'.$this->base_url.'&period=quarter"'.$class.'>'.__('Quarter', 'easy-yandex-metrica').'</a></li>';

			$class = ('year' == $this->period)?' class="current"':'';
			$filter .= '<li><a href="'.$this->base_url.'&period=year"'.$class.'>'.__('Year', 'easy-yandex-metrica').'</a></li>';

			$base_url = $this->base_url.'&period='.$this->period;
			$filter .= '<li style="margin-left:40px;"><b>'.__('Detail', 'easy-yandex-metrica').':</b></li>';

			if (('today' == $this->period) || ('yesterday' == $this->period)) {
				$class = ('hour' == $this->group)?' class="current"':'';
				$filter .= '<li><a href="'.$base_url.'&group=hour"'.$class.'>'.__('by hours', 'easy-yandex-metrica').'</a></li>';
			}

			if (('today' != $this->period) && ('yesterday' != $this->period)) {
				$class = ('day' == $this->group)?' class="current"':'';
				$filter .= '<li><a href="'.$base_url.'&group=day"'.$class.'>'.__('by day', 'easy-yandex-metrica').'</a></li>';
			}

			if (('today' != $this->period) && ('yesterday' != $this->period) && ('day' != $this->period) && ('week' != $this->period)) {
				$class = ('week' == $this->group)?' class="current"':'';
				$filter .= '<li><a href="'.$base_url.'&group=week"'.$class.'>'.__('by week', 'easy-yandex-metrica').'</a></li>';
			}

			if (('quarter' == $this->period) || ('year' == $this->period)) {
				$class = ('month' == $this->group)?' class="current"':'';
				$filter .= '<li><a href="'.$base_url.'&group=month"'.$class.'>'.__('by months', 'easy-yandex-metrica').'</a></li>';
			}

			$filter .= '</ul>';

			$filter .= '</div>';

			return $filter;
		}

		public function get_all_counters_select()
		{
			if ('' == $this->yandex_metrika_token) {
				return false;
			} else {
				$url ='https://api-metrika.yandex.net/management/v1/counters';
				$json = $this->get_json_data($url);
				$data = json_decode($json, true);
				if (!empty($data)) {
					$return = '<select name="abwp_eym_counter_id">';
					foreach($data['counters'] as $key => $value) {
						$select = ($this->yandex_metrika_counter_id == $value['id'])?' selected="selected"':'';
						$return .= '<option value="'.$value['id'].'"'.$select.'>'.$value['site'].'</option>';
					}
					$return .= '</select>';
				} else {
					$return = __('The service is temporarily not available', 'easy-yandex-metrica' );
				}
				return $return;
			}
		}

		private function get_json_data($url)
		{
			$args = array(
				'headers' => array(
					'Host' => 'api-metrika.yandex.net',
					'Authorization' => 'OAuth ' . $this->yandex_metrika_token,
					'Content-Type' => 'application/x-yametrika+json',
					'Access-Control-Allow-Origin' => '*',
				)
			);
			$data = wp_remote_get( $url, $args );
			if( wp_remote_retrieve_response_code( $data ) === 200 ){
				$return = wp_remote_retrieve_body( $data );
			} else {
				$return = '';
			}
			return $return;
		}
	}
}