<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

class EasyYandexMetricaDashboard
{

    /**
	 * Class constructor.
	 *
	 * @return void
	 */
    public function __construct() 
    {
        add_action('wp_dashboard_setup', [ $this, 'dashboardSetup' ], 9 );
    }

    public function dashboardSetup()
    {
        wp_add_dashboard_widget('easy_yandex_metrica_widget', __( 'Yandex.Metrica', 'easy-yandex-metrica' ).'   '. __( 'Traffic', 'easy-yandex-metrica' ), [ $this, 'dashboardWidget' ], 'side', 'high');
    }

    public function dashboardWidget()
    {        
        $admin_metrica = new ABWP_Admin_Metrica();
        $admin_metrica->viewDashboard();
    }

    
}