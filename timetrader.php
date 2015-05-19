<?php
/*
Plugin Name: Time Trader
Plugin URI: http://agenciacion.com/
Description: Marcador de horário
Version: 0.1
Author: Indiorlei de Oliveira (Agência Cion)
Author URI: http://agenciacion.com/
Requires at least: 4.0
Tested up to: 4.2
License: GPLv2
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists( 'TimeTraderPlugin' ) ) :

    class TimeTraderPlugin {
        public $version = '0.1';

        public static function init() {
            $timetrader = new self();
        }

        
        public function __construct() {
            $this->define_constants();
            $this->includes();
            $this->setup_actions();
            $this->create_tables();
        }

        
        private function define_constants() {
            define( 'TIMETRADER_VERSION',    $this->version );
            define( 'TIMETRADER_BASE_URL',   trailingslashit( plugins_url( 'timetrader' ) ) );
            define( 'TIMETRADER_ASSETS_URL', trailingslashit( TIMETRADER_BASE_URL . 'assets' ) );
            define( 'TIMETRADER_PATH',       plugin_dir_path( __FILE__ ) );
        }

        
        private function plugin_classes() {
            return array(
                'timetradersystemcheck'  => TIMETRADER_PATH . 'includes/timetrader.systemcheck.class.php',
                // 'simple_html_dom'        => TIMETRADER_PATH . 'includes/simple_html_dom.php'
                );
        }

        
        private function includes() {
            $autoload_is_disabled = defined( 'TIMETRADER_AUTOLOAD_CLASSES' ) && TIMETRADER_AUTOLOAD_CLASSES === false;
            if ( function_exists( "spl_autoload_register" ) && ! ( $autoload_is_disabled ) ) {
                // >= PHP 5.2 - Use auto loading
                if ( function_exists( "__autoload" ) ) {
                    spl_autoload_register( "__autoload" );
                }
                spl_autoload_register( array( $this, 'autoload' ) );
            } else {
                // < PHP5.2 - Require all classes
                foreach ( $this->plugin_classes() as $id => $path ) {
                    if ( is_readable( $path ) && ! class_exists( $id ) ) {
                        require_once( $path );
                    }
                }
            }
        }


        /**
        * Autoload Timetrader classes to reduce memory consumption
        */
        public function autoload( $class ) {
            $classes = $this->plugin_classes();
            $class_name = strtolower( $class );
            if ( isset( $classes[$class_name] ) && is_readable( $classes[$class_name] ) ) {
                require_once( $classes[$class_name] );
            }
        }


        /**
        * Hook Time Trader into WordPress
        */
        private function setup_actions() {
            add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 9554 );
            add_action( 'init', array( $this, 'register_post_type' ) );
            add_action( 'init', array( $this, 'register_taxonomy' ) );
            add_action( 'widgets_init', array( $this, 'register_timetrader_widget' ) );
            if ( defined( 'TIMETRADER_ENABLE_RESOURCE_MANAGER' ) && TIMETRADER_ENABLE_RESOURCE_MANAGER === true ) {
                add_action( 'template_redirect', array( $this, 'start_resource_manager'), 0 );
            }
        }


        /**
        * Register Time Trader widget
        */
        public function register_timetrader_widget() {
            // register_widget( 'TimeTrader_Widget' );
        }


        /**
        * Register Timetrader post type
        */
        public function register_post_type() {
            register_post_type( 'timetrader', array(
                'query_var' => false,
                'rewrite' => false,
                'public' => true,
                'exclude_from_search' => true,
                'publicly_queryable' => false,
                'show_in_nav_menus' => false,
                'show_ui' => false,
                'labels' => array(
                    'name' => 'Time Trader'
                    )
                )
            );
        }


        /**
        * Register taxonomy to timetrader
        */
        public function register_taxonomy() {
            register_taxonomy( 'timetrader', 'attachment', array(
                'hierarchical' => true,
                'public' => false,
                'query_var' => false,
                'rewrite' => false
                )
            );
        }


        /**
        * Add the menu page
        */
        public function register_admin_menu() {
            global $user_ID;
            $title = apply_filters( 'timetrader_menu_title', 'Time Trader' );
            $capability = apply_filters( 'timetrader_capability', 'edit_others_posts' );
            
            $page = add_menu_page(
                $title,
                $title,
                $capability,
                'timetrader',
                array( $this, 'render_admin_page' ),
                TIMETRADER_ASSETS_URL . 'timetrader/logo-timetrader.png'
                );

            // ensure our JavaScript is only loaded on the Time Trader admin page
            add_action( 'admin_print_scripts-' . $page, array( $this, 'register_admin_scripts' ) );
            add_action( 'admin_print_styles-' . $page, array( $this, 'register_admin_styles' ) );
            add_action( 'load-' . $page, array( $this, 'help_tab' ) );


            $page = add_submenu_page(
                'timetrader',
                __( 'Algum SubMenu', 'timetrader' ),
                __( 'Algum SubMenu', 'timetrader' ),
                $capability,
                'timetrader-sub-menu',//aqui vai a function da nova pagina
                array( $this, 'sub_menu' ) //aqui vai a funcition que renderiza a pagina
                );
            add_action( 'admin_print_styles-' . $page, array( $this, 'register_admin_styles' ) );

        }


        /**
        * render pagina sub menu
        */
        public function sub_menu() {
            ?>
            <h2>Uma página de sub menu</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <?php
        }


        /**
        * Register admin styles
        */
        public function register_admin_styles() {
            wp_enqueue_style( 'timetrader-fullcalendar-styles', TIMETRADER_ASSETS_URL . 'calendar/css/fullcalendar.css', false, TIMETRADER_VERSION );
            do_action( 'timetrader_register_admin_styles' );
        }

        /**
        * Register admin JavaScript
        */
        public function register_admin_scripts() {
            // media library dependencies
            wp_enqueue_media();
            // plugin dependencies
            wp_enqueue_script( 'jquery-ui-core', array( 'jquery' ) );
            wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery', 'jquery-ui-core' ) );
            wp_dequeue_script( 'link' ); // WP Posts Filter Fix (Advanced Settings not toggling)
            wp_dequeue_script( 'ai1ec_requirejs' ); // All In One Events Calendar Fix (Advanced Settings not toggling)
            
            wp_enqueue_script( 'timetrader-calendar-moment', TIMETRADER_ASSETS_URL . 'calendar/js/moment.min.js', array( 'jquery' ), TIMETRADER_VERSION );
            wp_enqueue_script( 'timetrader-calendar-fullcalendar', TIMETRADER_ASSETS_URL . 'calendar/js/fullcalendar.min.js', array( 'jquery' ), TIMETRADER_VERSION );
            do_action( 'timetrader_register_admin_scripts' );
        }


        /**
        * Check our WordPress installation is compatible with Time Trader
        */
        public function do_system_check() {
            $systemCheck = new TimeTraderSystemCheck();
            $systemCheck->check();
        }


        /**
        * Return the users saved view preference.
        */
        public function get_view() {
            global $user_ID;
            if ( get_user_meta( $user_ID, "timetrader_view", true ) ) {
                return get_user_meta( $user_ID, "timetrader_view", true );
            }
            return 'tabs';
        }


        /**
        * Render the admin page
        */
        public function render_admin_page() {

            // ERRO AO USAR OS DOIS AJAX/JavaScript JUNTOS 

            
            // code php of admin page
            global $wpdb;

            if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
                require_once( ABSPATH . 'wp-load.php' );
                $date_available     = $_POST['date_available'];
                $time_available_str = $_POST['time_available'];
                $time_available     = explode( '|', $time_available_str, -1 );
                $this->insert_values( $date_available, $time_available );
            }

            if( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
                require_once( ABSPATH . 'wp-load.php' );
                global $calendar_time_available;
                if ( isset( $_GET['date_available'] ) ) { $calendar_time_available = $_GET['date_available']; }
            }

            ?>
            <script type='text/javascript'>
            // code javascript
            jQuery(document).ready(function() {

                jQuery('#calendar').fullCalendar({
                    dayClick: function(date) {
                        jQuery('.fc-day').css('background-color', '');
                        jQuery(this).css('background-color', '#b1c903');
                        jQuery('#reservation .date_available').attr('value', date.format());
                        // event.preventDefault();
                        var urlData = "&date_available=" + jQuery('#date_available').val();
                        // var urlData = "&date_available=" + date.format();
                        var urlThis = window.location.href;
                        jQuery.ajax({
                            type: "GET",
                            url: urlThis,
                            async: true,
                            data: urlData,
                            error: function(xhr, statusText) {
                                // console.log('calendar error');
                            },
                            success: function(data) {
                                // console.log('calendar success');
                                jQuery('#reservation').html(jQuery(data).find('#reservation'));
                                jQuery('#reservation').fadeIn('400');
                            },
                            beforeSend: function() {
                                // console.log('calendar beforeSend');
                                jQuery('#reservation').fadeOut('400');
                            },
                            complete: function(data) {
                                // console.log('calendar complete');
                            }
                        });
                    },
                    loading: function(bool) {
                        // funcao de loading para caregar
                        // jQuery('#loading').toggle(bool);
                    }
                });

                jQuery('#reservation').bind('submit', function(event) {
                    event.preventDefault();
                    var date_available = jQuery('#date_available').val();
                    var time_available = '';
                    jQuery(':checkbox').each(function () {
                        var ischecked = jQuery(this).is(':checked');
                        if ( ischecked ) { time_available += jQuery(this).val() + '|'; }
                    });
                    var urlData = "&date_available=" + date_available + "&time_available=" + time_available;
                    var urlThis = window.location.href;
                    jQuery.ajax({
                        type: "POST",
                        url: urlThis,
                        async: true,
                        data: urlData,
                        error: function(xhr, statusText) {
                            console.log( 'error' );
                            jQuery('.progress').text('Erro!');
                        },
                        success: function(data) {
                            console.log( 'success' );
                            jQuery('.progress').text('Salvo com Sucesso!');
                            setTimeout( function() { jQuery('.progress').fadeOut(); }, 5000 );
                        },
                        beforeSend: function() {
                            console.log( 'beforeSend' );
                            jQuery('.progress').text('Salvando...');
                        },
                        complete: function() {
                            console.log( 'complete' );
                        }
                    });
                });

            });
            </script>
            <!-- body plugin -->
            <div class="wrap timetrader">
                <div id="calendar"></div>
                <form id="reservation">
                    <input type="hidden" class='date_available' name='date_available' id='date_available'>
                    <?php
                    $get_calendar_time_available = $GLOBALS['wpdb']->get_results( "SELECT da.id, da.date_available, ri.date_available_id, ri.time_available_id FROM ". $wpdb->prefix ."timetrader_date_available da, ". $wpdb->prefix ."timetrader_reservation_info ri WHERE da.id = ri.date_available_id AND da.date_available LIKE '" . $calendar_time_available . "';", OBJECT );
                    $time_available = $GLOBALS['wpdb']->get_results( "SELECT id, TIME_FORMAT(time_available, '%H:%i') time_available FROM " . $wpdb->prefix . "timetrader_time_available", OBJECT );
                    foreach ($time_available as $key => $value) {
                        $checked = '';
                        foreach ($get_calendar_time_available as $value_calendar_time_available ) {
                            if ( $value_calendar_time_available->time_available_id == $value->id ) { $checked = 'checked="checked"'; }
                        }
                        echo '<input ' . $checked . 'class="time_available" id="'. $value->id . '"type="checkbox" name="time_available" value="' . $value->id . '"><label class="label_time_available" for="' . $value->id . '">' . $value->time_available . '</label></br>';
                    }
                    ?>
                    <input type="submit" value="Salvar">
                    <span class='progress'></span>
                </form>
            </div>
            <?php
        }


        /**
        * Insert values
        */
        public function insert_values( $date_available, $time_available ) {
            global $wpdb;

            $table_date_available           = $wpdb->prefix . 'timetrader_date_available';
            $timetrader_time_available      = $wpdb->prefix . 'timetrader_time_available';

            $timetrader_reservation         = $wpdb->prefix . 'timetrader_reservation';
            $timetrader_reservation_info    = $wpdb->prefix . 'timetrader_reservation_info';

            $timetrader_status              = $wpdb->prefix . 'timetrader_status';

            $has_date = $GLOBALS['wpdb']->get_results( "SELECT * FROM " . $wpdb->prefix . "timetrader_date_available WHERE date_available LIKE '" . $date_available . "';", OBJECT );

            // se ja tem uma data faz o update de infos naquela data
            if ( empty( $has_date ) ) {
                // insert
                $wpdb->insert( $table_date_available, array(
                    'date_available' => $date_available
                    )
                );
                $date_available_get_id = $GLOBALS['wpdb']->get_results( "SELECT * FROM " . $wpdb->prefix . "timetrader_date_available WHERE date_available LIKE '" . $date_available . "';", OBJECT );
                foreach ( $date_available_get_id as $key => $date ) { $date_available_id = $date->id; }
                foreach ( $time_available as $value ) {
                    $wpdb->insert( $timetrader_reservation_info, array(
                        'date_available_id' => $date_available_id,
                        'time_available_id' => $value
                        )
                    );
                }
            } else {
                //update

                // foreach ( $has_date as $key => $date ) { $date_available_id = $date->id; }
                // $has_reservation_info = $GLOBALS['wpdb']->get_results( "SELECT * FROM " . $wpdb->prefix . "timetrader_reservation_info WHERE date_available_id =" . $date_available_id . ";", OBJECT );

            }


        }


        /**
        * Start output buffering.
        *
        * Note: wp_ob_end_flush_all is called by default 
        *  - see shutdown action in default-filters.php
        */
        public function start_resource_manager() {
            ob_start( array( $this, 'resource_manager' ) );
        }


        /**
        * Process the whole page output. Move link tags with an ID starting
        * with 'timetrader' into the <head> of the page.
        */
        public function resource_manager( $buffer ) {
            // create dom document from buffer
            $html = new simple_html_dom();
            // Load from a string
            $html->load( $buffer, true, false );
            if ( ! $html->find( 'body link[id^="timetrader"]' ) )
                return $buffer;
                // selectors to find Time Trader links
            $selectors = array( 
                'body link[id^="timetrader"]',
                );
            $selectors = apply_filters( "timetrader_resource_manager_selectors", $selectors );
            if ( $head = $html->find( 'head', 0 ) ) {
                // move Time Trader elemends to <head>
                foreach ( $selectors as $selector ) {
                    foreach ( $html->find( $selector ) as $element ) {
                        $head->innertext .= "\t" . $element->outertext . "\n";
                        $element->outertext = '';
                    }
                }
            }
            return $html->save();
        }


        /**
        * Create Tables
        */
        private function create_tables() {
            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE {$wpdb->prefix}timetrader_reservation (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    client_id mediumint(9) NOT NULL,
                    subject varchar(200),
                    description varchar(1000),
                    status_id mediumint(9) NOT NULL,
                    reservation_info mediumint(9) NOT NULL,
                    UNIQUE KEY id (id)
                ) $charset_collate;
                CREATE TABLE {$wpdb->prefix}timetrader_status (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    description varchar(45),
                    UNIQUE KEY id (id)
                ) $charset_collate;
                CREATE TABLE {$wpdb->prefix}timetrader_client (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    name varchar(45),
                    email varchar(45),
                    telephone varchar(45),
                    age int,
                    gender varchar(45),
                    country varchar(45),
                    states varchar(45),
                    skype varchar(45),
                    UNIQUE KEY id (id)
                ) $charset_collate;
                CREATE TABLE {$wpdb->prefix}timetrader_date_available (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    date_available DATE,
                    UNIQUE KEY id (id)
                ) $charset_collate;
                CREATE TABLE {$wpdb->prefix}timetrader_time_available (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    time_available DATETIME,
                    UNIQUE KEY id (id)
                ) $charset_collate;
                CREATE TABLE {$wpdb->prefix}timetrader_reservation_info (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    time_available_id mediumint(9) NOT NULL,
                    date_available_id mediumint(9) NOT NULL,
                    UNIQUE KEY id (id)
                ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            // inserts default
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 1,  'time_available' => '0000-00-00 08:00:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 2,  'time_available' => '0000-00-00 08:30:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 3,  'time_available' => '0000-00-00 09:00:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 4,  'time_available' => '0000-00-00 09:30:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 5,  'time_available' => '0000-00-00 10:00:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 6,  'time_available' => '0000-00-00 10:30:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 7,  'time_available' => '0000-00-00 11:00:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 8,  'time_available' => '0000-00-00 11:30:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 9,  'time_available' => '0000-00-00 12:00:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 10, 'time_available' => '0000-00-00 12:30:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 11, 'time_available' => '0000-00-00 13:00:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 12, 'time_available' => '0000-00-00 13:30:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 13, 'time_available' => '0000-00-00 14:00:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 14, 'time_available' => '0000-00-00 14:30:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 15, 'time_available' => '0000-00-00 15:00:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 16, 'time_available' => '0000-00-00 15:30:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 17, 'time_available' => '0000-00-00 16:00:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 18, 'time_available' => '0000-00-00 16:30:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 19, 'time_available' => '0000-00-00 17:00:00', ) );
            // $wpdb->insert( $wpdb->prefix . 'timetrader_time_available', array('id' => 20, 'time_available' => '0000-00-00 17:30:00', ) );

        }


        /**
        * Add the help tab to the screen.
        */
        public function help_tab() {
            $screen = get_current_screen();
            // documentation tab
            $screen->add_help_tab( array(
                'id'    => 'documentation',
                'title' => __( 'Documentation', 'timetrader' ),
                'content'   => "<p><a href='http://www.timetrader.com/documentation/' target='blank'>Time Trader Documentation</a></p>",
                )
            );
        }

    }

endif;
add_action( 'plugins_loaded', array( 'TimeTraderPlugin', 'init' ), 10 );
