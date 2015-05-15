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
        public $slider = null;

        
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
        * Register ML Slider post type
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
        * Register taxonomy to store slider => slides relationship
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
            $page = add_menu_page( $title, $title, $capability, 'timetrader', array( $this, 'render_admin_page' ), TIMETRADER_ASSETS_URL . 'timetrader/logo.png' );
            // ensure our JavaScript is only loaded on the Time Trader admin page
            add_action( 'admin_print_scripts-' . $page, array( $this, 'register_admin_scripts' ) );
            add_action( 'admin_print_styles-' . $page, array( $this, 'register_admin_styles' ) );
            add_action( 'load-' . $page, array( $this, 'help_tab' ) );
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
            wp_enqueue_script( 'timetrader-calendar-moment', TIMETRADER_ASSETS_URL . 'calendar/js/moment.min.js', array( 'jquery' ), TIMETRADER_VERSION );
            wp_enqueue_script( 'timetrader-calendar-fullcalendar', TIMETRADER_ASSETS_URL . 'calendar/js/fullcalendar.min.js', array( 'jquery' ), TIMETRADER_VERSION );
            wp_dequeue_script( 'link' ); // WP Posts Filter Fix (Advanced Settings not toggling)
            wp_dequeue_script( 'ai1ec_requirejs' ); // All In One Events Calendar Fix (Advanced Settings not toggling)
            $this->localize_admin_scripts();
            do_action( 'timetrader_register_admin_scripts' );
        }


        /**
        * Localise admin script
        */
        public function localize_admin_scripts() {
            wp_localize_script( 'timetrader-admin-script', 'timetrader', array(
                'url' => __( "URL", "timetrader" ),
                'caption' => __( "Caption", "timetrader" ),
                'new_window' => __( "New Window", "timetrader" ),
                'confirm' => __( "Are you sure?", "timetrader" ),
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'change_image' => __( "Select replacement image", "timetrader"),
                'resize_nonce' => wp_create_nonce( 'timetrader_resize' ),
                'addslide_nonce' => wp_create_nonce( 'timetrader_addslide' ),
                'changeslide_nonce' => wp_create_nonce( 'timetrader_changeslide' ),
                'iframeurl' => admin_url( 'admin-post.php?action=timetrader_preview' ),
                'useWithCaution' => __( "Caution: This setting is for advanced developers only. If you're unsure, leave it checked.", "timetrader" )
                )
            );
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
            global $wpdb;
            // code php of admin page
            if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
                require_once( ABSPATH . 'wp-load.php' );
                
                $date_available = $_POST['date_available'];
                $time_available_str = $_POST['time_available'];
                $time_available = explode( '|', $time_available_str, -1 );

                $this->insert_values( $date_available, $time_available );
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
                            console.log('error');
                            jQuery('.progress').text('Erro!');
                        },
                        success: function(data) {
                            console.log('success');
                            jQuery('.progress').text('Salvo com Sucesso!');
                            setTimeout( function() { jQuery('.progress').fadeOut(); }, 5000 );
                        },
                        beforeSend: function() {
                            console.log('beforeSend');
                            jQuery('.progress').text('Salvando...');
                        },
                        complete: function() {
                            console.log('complete');
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
                    $time_available = $GLOBALS['wpdb']->get_results( "SELECT id, TIME_FORMAT(time_available, '%H:%i') time_available FROM " . $wpdb->prefix . "timetrader_time_available", OBJECT );
                    foreach ($time_available as $key => $value) {
                        echo '<input class="time_available" id="'. $value->id . '"type="checkbox" name="time_available" value="' . $value->id . '"><label class="label_time_available" for="' . $value->id . '">' . $value->time_available . '</label></br>';
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
                foreach ( $has_date as $key => $date ) { $date_available_id = $date->id; }

                $has_reservation_info = $GLOBALS['wpdb']->get_results( "SELECT * FROM " . $wpdb->prefix . "timetrader_reservation_info WHERE date_available_id =" . $date_available_id . ";", OBJECT );


                // var_dump($has_reservation_info);
                // die();

                foreach ( $time_available as $value ) {
                    foreach ( $has_reservation_info as $value_reservation_info ) {
                        // if ( $value_reservation_info['date_available_id'] == $date_available_id && $value_reservation_info['time_available_id'] == $time_available_id) {
                        //     exit;
                        // }else{
                        //     $wpdb->insert( $timetrader_reservation_info, array( 'date_available_id' => $date_available_id, 'time_available_id' => $value ) );
                        // }
                            
                    }
                }

                // foreach ( $time_available as $value ) {
                //     foreach ( $has_reservation_info as $value_reservation_info ) {
                //         $reservation_info_id = $value_reservation_info->id;
                //         if ( $value == $value_reservation_info->time_available_id ) {
                //             $wpdb->update(
                //                 $timetrader_reservation_info,
                //                 array(
                //                     'date_available_id' => $date_available_id,
                //                     'time_available_id' => $value
                //                     ),
                //                 array( 'id' => $reservation_info_id )
                //                 );
                //         }
                //     }
                // }

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

            // $wpdb->insert( $table_name, array(
            //  'time' => current_time( 'mysql' ),
            //  'name' => $welcome_name,
            //  'text' => $welcome_text,
            //  )
            // );
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
