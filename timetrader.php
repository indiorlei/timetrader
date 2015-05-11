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
            $this->setup_filters();
            $this->setup_shortcode();
            $this->register_slide_types();
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

        private function setup_shortcode() {
            // add_shortcode( 'timetrader', array( $this, 'register_shortcode' ) );
            // add_shortcode( 'timetrader', array( $this, 'register_shortcode' ) );
        }


        /**
        * Hook Time Trader into WordPress
        */
        private function setup_actions() {

            add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 9554 );

            add_action( 'init', array( $this, 'register_post_type' ) );
            add_action( 'init', array( $this, 'register_taxonomy' ) );
            add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
            add_action( 'admin_footer', array( $this, 'admin_footer' ), 11 );

            add_action( 'widgets_init', array( $this, 'register_timetrader_widget' ) );
            
            add_action( 'admin_post_timetrader_preview', array( $this, 'do_preview' ) );
            add_action( 'admin_post_timetrader_switch_view', array( $this, 'switch_view' ) );
            add_action( 'admin_post_timetrader_delete_slide', array( $this, 'delete_slide' ) );
            add_action( 'admin_post_timetrader_delete_slider', array( $this, 'delete_slider' ) );
            add_action( 'admin_post_timetrader_create_slider', array( $this, 'create_slider' ) );
            add_action( 'admin_post_timetrader_update_slider', array( $this, 'update_slider' ) );
            
            if ( defined( 'TIMETRADER_ENABLE_RESOURCE_MANAGER' ) && TIMETRADER_ENABLE_RESOURCE_MANAGER === true ) {
                add_action( 'template_redirect', array( $this, 'start_resource_manager'), 0 );
            }

        }

        /**
        * Hook Time Trader into WordPress
        */
        private function setup_filters() {
            add_filter( 'media_upload_tabs', array( $this, 'custom_media_upload_tab_name' ), 998 );
            add_filter( 'media_view_strings', array( $this, 'custom_media_uploader_tabs' ), 5 );
            add_filter( 'media_buttons_context', array( $this, 'insert_timetrader_button' ) );

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
        * Register our slide types
        */
        private function register_slide_types() {
            // $image = new MetaImageSlide();
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
        * Shortcode used to display slideshow
        *
        * @return string HTML output of the shortcode
        */
        public function register_shortcode( $atts ) {
        
            extract( shortcode_atts( array(
                'id' => false,
                'restrict_to' => false
                ), $atts, 'timetrader' ) );
            
            if ( ! $id ) {
                return false;
            }

            // handle [timetrader id=123 restrict_to=home]
            if ($restrict_to && $restrict_to == 'home' && ! is_front_page()) {
                return;
            }

            if ($restrict_to && $restrict_to != 'home' && ! is_page( $restrict_to ) ) {
                return;
            }

            // we have an ID to work with
            $slider = get_post( $id );
            // check the slider is published and the ID is correct

            if ( ! $slider || $slider->post_status != 'publish' || $slider->post_type != 'timetrader' ) {
                return "<!-- Time Trader {$atts['id']} not found -->";
            }

            // lets go
            $this->set_slider( $id, $atts );
            $this->slider->enqueue_scripts();
            return $this->slider->render_public_slides();
        
        }


        /**
        * Initialise translations
        */
        public function load_plugin_textdomain() {
            load_plugin_textdomain( 'timetrader', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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

        /**
        * Rehister admin styles
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
        * Outputs a blank page containing a slideshow preview (for use in the 'Preview' iFrame)
        */
        public function do_preview() {
            remove_action('wp_footer', 'wp_admin_bar_render', 1000);
            
            if ( isset( $_GET['slider_id'] ) && absint( $_GET['slider_id'] ) > 0 ) {
                $id = absint( $_GET['slider_id'] );
                ?>
                <!DOCTYPE html>
                <html>
                <head>
                    <style type='text/css'>body, html {overflow:hidden;margin:0;padding:0;}</style>
                    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
                    <meta http-equiv="Pragma" content="no-cache" />
                    <meta http-equiv="Expires" content="0" />
                </head>
                <body>
                    <?php echo do_shortcode("[timetrader id={$id}]"); ?>
                    <?php wp_footer(); ?>
                </body>
                </html>
                <?php
            }
            die();
        }


        /**
        * Check our WordPress installation is compatible with Time Trader
        */
        public function do_system_check() {
            $systemCheck = new TimeTraderSystemCheck();
            $systemCheck->check();
        }


        /**
        * Update the tab options in the media manager
        */
        public function custom_media_uploader_tabs( $strings ) {
            
            //update strings

            if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'timetrader' ) ) {
                $strings['insertMediaTitle'] = __( "Image", "timetrader" );
                $strings['insertIntoPost'] = __( "Add to slider", "timetrader" );

                // remove options
                $strings_to_remove = array(
                    'createVideoPlaylistTitle',
                    'createGalleryTitle',
                    'insertFromUrlTitle',
                    'createPlaylistTitle'
                    );
                foreach ($strings_to_remove as $string) {
                    if (isset($strings[$string])) {
                        unset($strings[$string]);
                    }
                }
            }

            return $strings;
        }


        /**
        * Add extra tabs to the default wordpress Media Manager iframe
        *
        * @var array existing media manager tabs
        */
        public function custom_media_upload_tab_name( $tabs ) {
            // restrict our tab changes to the Time Trader plugin page
            if ( isset( $_GET['page'] ) && $_GET['page'] == 'timetrader' ) {
                if ( isset( $tabs['nextgen'] ) ) {
                    unset( $tabs['nextgen'] );
                }
            }

            return $tabs;
        }


        /**
        * Set the current slider
        */
        public function set_slider( $id, $shortcode_settings = array() ) {
            
            $type = 'flex';
            
            if ( isset( $shortcode_settings['type'] ) ) {
                $type = $shortcode_settings['type'];
            } else if ( $settings = get_post_meta( $id, 'timetrader_settings', true ) ) {
                if ( is_array( $settings ) && isset( $settings['type'] ) ) {
                    $type = $settings['type'];
                }
            }

            if ( ! in_array( $type, array( 'flex', 'coin', 'nivo', 'responsive' ) ) ) {
                $type = 'flex';
            }

            $this->slider = $this->load_slider( $type, $id, $shortcode_settings );
        }


        /**
        * Create a new slider based on the sliders type setting
        */
        private function load_slider( $type, $id, $shortcode_settings ) {
            
            // switch ( $type ) {
            //     case( 'coin' ): return new MetaCoinSlider( $id, $shortcode_settings );
            //     case( 'flex' ): return new MetaFlexSlider( $id, $shortcode_settings );
            //     case( 'nivo' ): return new MetaNivoSlider( $id, $shortcode_settings );
            //     case( 'responsive' ): return new MetaResponsiveSlider( $id, $shortcode_settings );
            //     default: return new MetaFlexSlider( $id, $shortcode_settings );
            // }

        }


        /**
        *
        */
        public function update_slider() {

            // check_admin_referer( "timetrader_update_slider" );
            // $capability = apply_filters( 'timetrader_capability', 'edit_others_posts' );

            // if ( ! current_user_can( $capability ) ) {
            //     return;
            // }
            // $slider_id = absint( $_POST['slider_id'] );

            // if ( ! $slider_id ) {
            //     return;
            // }

            // // update settings
            // if ( isset( $_POST['settings'] ) ) {
            //     $new_settings = $_POST['settings'];
            //     $old_settings = get_post_meta( $slider_id, 'timetrader_settings', true );
            //     // convert submitted checkbox values from 'on' or 'off' to boolean values
            //     $checkboxes = apply_filters( "timetrader_checkbox_settings", array( 'noConflict', 'fullWidth', 'hoverPause', 'links', 'reverse', 'random', 'printCss', 'printJs', 'smoothHeight', 'center', 'carouselMode', 'autoPlay' ) );

            //     foreach ( $checkboxes as $checkbox ) {
            //         if ( isset( $new_settings[$checkbox] ) && $new_settings[$checkbox] == 'on' ) {
            //             $new_settings[$checkbox] = "true";
            //         } else {
            //             $new_settings[$checkbox] = "false";
            //         }
            //     }

            //     $settings = array_merge( (array)$old_settings, $new_settings );

            //     // update the slider settings
            //     update_post_meta( $slider_id, 'timetrader_settings', $settings );
            // }

            // // update slideshow title
            // if ( isset( $_POST['title'] ) ) {
            //     $slide = array(
            //         'ID' => $slider_id,
            //         'post_title' => esc_html( $_POST['title'] )
            //         );
            //     wp_update_post( $slide );
            // }

            // // update individual slides
            // if ( isset( $_POST['attachment'] ) ) {
            //     foreach ( $_POST['attachment'] as $slide_id => $fields ) {
            //         do_action( "timetrader_save_{$fields['type']}_slide", $slide_id, $slider_id, $fields );
            //     }
            // }

        }


        /**
        * Delete a slide. This doesn't actually remove the slide from WordPress, simply untags
        * it from the slide taxonomy.
        */
        public function delete_slide() {

            // // check nonce
            // check_admin_referer( "timetrader_delete_slide" );
            // $capability = apply_filters( 'timetrader_capability', 'edit_others_posts' );
            // if ( ! current_user_can( $capability ) ) {
            // return;
            // }
            // $slide_id = absint( $_GET['slide_id'] );
            // $slider_id = absint( $_GET['slider_id'] );
            // // Get the existing terms and only keep the ones we don't want removed
            // $new_terms = array();
            // $current_terms = wp_get_object_terms( $slide_id, 'timetrader', array( 'fields' => 'ids' ) );
            // $term = get_term_by( 'name', $slider_id, 'timetrader' );
            // foreach ( $current_terms as $current_term ) {
            // if ( $current_term != $term->term_id ) {
            // $new_terms[] = absint( $current_term );
            // }
            // }
            // wp_set_object_terms( $slide_id, $new_terms, 'timetrader' );
            // wp_redirect( admin_url( "admin.php?page=timetrader&id={$slider_id}" ) );
        }


        /**
        * Delete a slider (send it to trash)
        */
        public function delete_slider() {
            // // check nonce
            // check_admin_referer( "timetrader_delete_slider" );
            // $capability = apply_filters( 'timetrader_capability', 'edit_others_posts' );
            // if ( ! current_user_can( $capability ) ) {
            // return;
            // }
            // $slider_id = absint( $_GET['slider_id'] );
            // // send the post to trash
            // $id = wp_update_post( array(
            // 'ID' => $slider_id,
            // 'post_status' => 'trash'
            // )
            // );
            // $slider_id = $this->find_slider( 'modified', 'DESC' );
            // wp_redirect( admin_url( "admin.php?page=timetrader&id={$slider_id}" ) );
        }


        /**
        *
        */
        public function switch_view() {

            global $user_ID;

            $view = $_GET['view'];
            $allowed_views = array('tabs', 'dropdown');

            if ( ! in_array( $view, $allowed_views ) ) {
                return;
            }

            delete_user_meta( $user_ID, "timetrader_view" );

            if ( $view == 'dropdown' ) {
                add_user_meta( $user_ID, "timetrader_view", "dropdown");
            }

            wp_redirect( admin_url( "admin.php?page=timetrader" ) );
        }


        /**
        * Create a new slider
        */
        public function create_slider() {
            // check nonce
            check_admin_referer( "timetrader_create_slider" );
            $capability = apply_filters( 'timetrader_capability', 'edit_others_posts' );
            if ( ! current_user_can( $capability ) ) {
                return;
            }
            $defaults = array();
            
            // if possible, take a copy of the last edited slider settings in place of default settings
            if ( $last_modified = $this->find_slider( 'modified', 'DESC' ) ) {
                $defaults = get_post_meta( $last_modified, 'timetrader_settings', true );
            }

            // insert the post
            $id = wp_insert_post( array(
                'post_title' => __( "New Slider", "timetrader" ),
                'post_status' => 'publish',
                'post_type' => 'timetrader'
                )
            );

            // use the default settings if we can't find anything more suitable.
            if ( empty( $defaults ) ) {
                $slider = new timetrader( $id, array() );
                $defaults = $slider->get_default_parameters();
            }

            // insert the post meta
            add_post_meta( $id, 'timetrader_settings', $defaults, true );

            // create the taxonomy term, the term is the ID of the slider itself
            wp_insert_term( $id, 'timetrader' );
            wp_redirect( admin_url( "admin.php?page=timetrader&id={$id}" ) );
        }


        /**
        * Find a single slider ID. For example, last edited, or first published.
        *
        * @param string $orderby field to order.
        * @param string $order direction (ASC or DESC).
        * @return int slider ID.
        */
        private function find_slider( $orderby, $order ) {
            $args = array(
                'force_no_custom_order' => true,
                'post_type' => 'timetrader',
                'num_posts' => 1,
                'post_status' => 'publish',
                'suppress_filters' => 1, // wpml, ignore language filter
                'orderby' => $orderby,
                'order' => $order
                );
            
            $the_query = new WP_Query( $args );

            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                return $the_query->post->ID;
            }

            wp_reset_query();
            return false;
        }


        /**
        * Get sliders. Returns a nicely formatted array of currently
        * published sliders.
        *
        * @param string $sort_key
        * @return array all published sliders
        */
        public function all_meta_sliders( $sort_key = 'date' ) {
            $sliders = array();
            // list the tabs
            $args = array(
            'post_type' => 'timetrader',
            'post_status' => 'publish',
            'orderby' => $sort_key,
            'suppress_filters' => 1, // wpml, ignore language filter
            'order' => 'ASC',
            'posts_per_page' => -1
            );
            $args = apply_filters( 'timetrader_all_meta_sliders_args', $args );
            // WP_Query causes issues with other plugins using admin_footer to insert scripts
            // use get_posts instead
            $all_sliders = get_posts( $args );
            foreach( $all_sliders as $slideshow ) {
            $active = $this->slider && ( $this->slider->id == $slideshow->ID ) ? true : false;
            $sliders[] = array(
            'active' => $active,
            'title' => $slideshow->post_title,
            'id' => $slideshow->ID
            );
            } 
            return $sliders;
        }


        /**
        * Compare array values
        *
        * @param array $elem1
        * @param array $elem2
        * @return bool
        */
        private function compare_elems( $elem1, $elem2 ) {
            return $elem1['priority'] > $elem2['priority'];
        }


        /**
        *
        * @param array $aFields - array of field to render
        * @return string
        */
        public function build_settings_rows( $aFields ) {

            // order the fields by priority
            uasort( $aFields, array( $this, "compare_elems" ) );
            $return = "";
            // loop through the array and build the settings HTML
            foreach ( $aFields as $id => $row ) {
                // checkbox input type
                if ( $row['type'] == 'checkbox' ) {
                    $return .= "<tr><td class='tipsy-tooltip' title=\"{$row['helptext']}\">{$row['label']}</td><td><input class='option {$row['class']} {$id}' type='checkbox' name='settings[{$id}]' {$row['checked']} />";
                    if ( isset( $row['after'] ) ) {
                        $return .= "<span class='after'>{$row['after']}</span>";
                    }
                    $return .= "</td></tr>";
                }

                // navigation row
                if ( $row['type'] == 'navigation' ) {
                    $navigation_row = "<tr class='{$row['type']}'><td class='tipsy-tooltip' title=\"{$row['helptext']}\">{$row['label']}</td><td><ul>";
                    foreach ( $row['options'] as $k => $v ) {
                        if ( $row['value'] === true && $k === 'true' ) {
                            $checked = checked( true, true, false );
                        } else if ( $row['value'] === false && $k === 'false' ) {
                            $checked = checked( true, true, false );
                        } else {
                            $checked = checked( $k, $row['value'], false );
                        }

                        $disabled = $k == 'thumbnails' ? 'disabled' : '';
                        $navigation_row .= "<li><label><input type='radio' name='settings[{$id}]' value='{$k}' {$checked} {$disabled}/>{$v['label']}</label></li>";
                    }
                    $navigation_row .= "</ul></td></tr>";
                    $return .= apply_filters( 'timetrader_navigation_options', $navigation_row, $this->slider );
                }

                // navigation row
                if ( $row['type'] == 'radio' ) {
                    $navigation_row = "<tr class='{$row['type']}'><td class='tipsy-tooltip' title=\"{$row['helptext']}\">{$row['label']}</td><td><ul>";
                    foreach ( $row['options'] as $k => $v ) {
                        $checked = checked( $k, $row['value'], false );
                        $class = isset( $v['class'] ) ? $v['class'] : "";
                        $navigation_row .= "<li><label><input type='radio' name='settings[{$id}]' value='{$k}' {$checked} class='radio {$class}'/>{$v['label']}</label></li>";
                    }

                    $navigation_row .= "</ul></td></tr>";
                    $return .= apply_filters( 'timetrader_navigation_options', $navigation_row, $this->slider );
                }

                // header/divider row
                if ( $row['type'] == 'divider' ) {
                    $return .= "<tr class='{$row['type']}'><td colspan='2' class='divider'><b>{$row['value']}</b></td></tr>";
                }

                // slideshow select row
                if ( $row['type'] == 'slider-lib' ) {
                    $return .= "<tr class='{$row['type']}'><td colspan='2' class='slider-lib-row'>";
                    foreach ( $row['options'] as $k => $v ) {
                        $checked = checked( $k, $row['value'], false );
                        $return .= "<input class='select-slider' id='{$k}' rel='{$k}' type='radio' name='settings[type]' value='{$k}' {$checked} />
                        <label for='{$k}'>{$v['label']}</label>";
                    }

                    $return .= "</td></tr>";
                }

                // number input type
                if ( $row['type'] == 'number' ) {
                    $return .= "<tr class='{$row['type']}'><td class='tipsy-tooltip' title=\"{$row['helptext']}\">{$row['label']}</td><td><input class='option {$row['class']} {$id}' type='number' min='{$row['min']}' max='{$row['max']}' step='{$row['step']}' name='settings[{$id}]' value='" . absint( $row['value'] ) . "' /><span class='after'>{$row['after']}</span></td></tr>";
                }
                
                // select drop down
                if ( $row['type'] == 'select' ) {
                    $return .= "<tr class='{$row['type']}'><td class='tipsy-tooltip' title=\"{$row['helptext']}\">{$row['label']}</td><td><select class='option {$row['class']} {$id}' name='settings[{$id}]'>";
                    foreach ( $row['options'] as $k => $v ) {
                        $selected = selected( $k, $row['value'], false );
                        $return .= "<option class='{$v['class']}' value='{$k}' {$selected}>{$v['label']}</option>";
                    }
                    $return .= "</select></td></tr>";
                }

                // theme drop down
                if ( $row['type'] == 'theme' ) {
                    $return .= "<tr class='{$row['type']}'><td class='tipsy-tooltip' title=\"{$row['helptext']}\">{$row['label']}</td><td><select class='option {$row['class']} {$id}' name='settings[{$id}]'>";
                    $themes = "";
                    foreach ( $row['options'] as $k => $v ) {
                        $selected = selected( $k, $row['value'], false );
                        $themes .= "<option class='{$v['class']}' value='{$k}' {$selected}>{$v['label']}</option>";
                    }
                    $return .= apply_filters( 'timetrader_get_available_themes', $themes, $this->slider->get_setting( 'theme' ) );
                    $return .= "</select></td></tr>";
                }

                // text input type
                if ( $row['type'] == 'text' ) {
                    $return .= "<tr class='{$row['type']}'><td class='tipsy-tooltip' title=\"{$row['helptext']}\">{$row['label']}</td><td><input class='option {$row['class']} {$id}' type='text' name='settings[{$id}]' value='" . esc_attr( $row['value'] ) . "' /></td></tr>";
                }

                // text input type
                if ( $row['type'] == 'textarea' ) {
                    $return .= "<tr class='{$row['type']}'><td class='tipsy-tooltip' title=\"{$row['helptext']}\" colspan='2'>{$row['label']}</td></tr><tr><td colspan='2'><textarea class='option {$row['class']} {$id}' name='settings[{$id}]' />{$row['value']}</textarea></td></tr>";
                }

                // text input type
                if ( $row['type'] == 'title' ) {
                    $return .= "<tr class='{$row['type']}'><td class='tipsy-tooltip' title=\"{$row['helptext']}\">{$row['label']}</td><td><input class='option {$row['class']} {$id}' type='text' name='{$id}' value='" . esc_attr( $row['value'] ) . "' /></td></tr>";
                }

            }
        
            return $return;
        
        }


        /**
        * Return an indexed array of all easing options
        *
        * @return array
        */
        private function get_easing_options() {
            $options = array(
            'linear', 'swing', 'jswing', 'easeInQuad', 'easeOutQuad', 'easeInOutQuad',
            'easeInCubic', 'easeOutCubic', 'easeInOutCubic', 'easeInQuart',
            'easeOutQuart', 'easeInOutQuart', 'easeInQuint', 'easeOutQuint',
            'easeInOutQuint', 'easeInSine', 'easeOutSine', 'easeInOutSine',
            'easeInExpo', 'easeOutExpo', 'easeInOutExpo', 'easeInCirc', 'easeOutCirc',
            'easeInOutCirc', 'easeInElastic', 'easeOutElastic', 'easeInOutElastic',
            'easeInBack', 'easeOutBack', 'easeInOutBack', 'easeInBounce', 'easeOutBounce',
            'easeInOutBounce'
            );
            foreach ( $options as $option ) {
            $return[$option] = array(
            'label' => ucfirst( preg_replace( '/(\w+)([A-Z])/U', '\\1 \\2', $option ) ),
            'class' => ''
            );
            }
            return $return;
        }

        /**
        * Output the slideshow selector.
        *
        * Show tabs or a dropdown list depending on the users saved preference.
        */
        public function print_slideshow_selector() {
            global $user_ID;
            $add_url = wp_nonce_url( admin_url( "admin-post.php?action=timetrader_create_slider" ), "timetrader_create_slider" );
            if ( $tabs = $this->all_meta_sliders() ) {
            if ( $this->get_view() == 'tabs' ) {
            echo "<div style='display: none;' id='screen-options-switch-view-wrap'>
            <a class='switchview dashicons-before dashicons-randomize tipsy-tooltip' title='" . __("Switch to Dropdown view", "timetrader") . "' href='" . admin_url( "admin-post.php?action=timetrader_switch_view&view=dropdown") . "'>" . __("Dropdown", "timetrader") . "</a></div>";
            echo "<h3 class='nav-tab-wrapper'>";
            foreach ( $tabs as $tab ) {
            if ( $tab['active'] ) {
            echo "<div class='nav-tab nav-tab-active'><input type='text' name='title'  value='" . esc_attr( $tab['title'] ) . "' onfocus='this.style.width = ((this.value.length + 1) * 9) + \"px\"' /></div>";
            } else {
            echo "<a href='?page=timetrader&amp;id={$tab['id']}' class='nav-tab'>" . esc_html( $tab['title'] ) . "</a>";
            }
            }
            echo "<a href='{$add_url}' id='create_new_tab' class='nav-tab'>+</a>";
            echo "</h3>";
            } else {
            if ( isset( $_GET['add'] ) && $_GET['add'] == 'true' ) {
            echo "<div id='message' class='updated'><p>" . __( "New slideshow created. Click 'Add Slide' to get started!", "timetrader" ) . "</p></div>";
            }
            echo "<div style='display: none;' id='screen-options-switch-view-wrap'><a class='switchview dashicons-before dashicons-randomize tipsy-tooltip' title='" . __("Switch to Tab view", "timetrader") . "' href='" . admin_url( "admin-post.php?action=timetrader_switch_view&view=tabs") . "'>" . __("Tabs", "timetrader") . "</a></div>";
            echo "<div class='dropdown_container'><label for='select-slider'>" . __("Select Slider", "timetrader") . ": </label>";
            echo "<select name='select-slider' onchange='if (this.value) window.location.href=this.value'>";
            $tabs = $this->all_meta_sliders( 'title' );
            foreach ( $tabs as $tab ) {
            $selected = $tab['active'] ? " selected" : "";
            if ( $tab['active'] ) {
            $title = $tab['title'];
            }
            echo "<option value='?page=timetrader&amp;id={$tab['id']}'{$selected}>{$tab['title']}</option>";
            }
            echo "</select> " . __( 'or', "timetrader" ) . " ";
            echo "<a href='{$add_url}'>" . __( 'Add New Slideshow', "timetrader" ) . "</a></div>";
            }
            } else {
            echo "<h3 class='nav-tab-wrapper'>";
            echo "<a href='{$add_url}' id='create_new_tab' class='nav-tab'>+</a>";
            echo "<div class='bubble'>" . __( "Create your first slideshow", "timetrader" ) . "</div>";
            echo "</h3>";
            }
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
            // code php of admin page

            ?>

            <script type='text/javascript'>
                // code javascript
                
                $(document).ready(function() {
                    $('#calendar').fullCalendar({
                        loading: function(bool) {
                            // funcao de loading para caregar
                            $('#loading').toggle(bool);
                            
                        }
                    });
                });
                
                $('#calendar').ready(function() {
                    $('.fc-day').bind('click', function(event) {
                        console.log($(this).attr('data-date'))
                    });
                });

            </script>

            <!-- body plugin -->
            <div class="wrap timetrader">
                <div id="calendar"></div>

            </div>

            <?php
        }


        /**
        * Append the 'Add Slider' button to selected admin pages
        */
        public function insert_timetrader_button( $context ) {
            $capability = apply_filters( 'timetrader_capability', 'edit_others_posts' );
            if ( ! current_user_can( $capability ) ) {
                return $context;
            }
            global $pagenow;
            if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) {
                $context .= '<a href="#TB_inline?&inlineId=choose-meta-slider" class="thickbox button" title="' .
                __( "Select slideshow to insert into post", "timetrader" ) .
                '"><span class="wp-media-buttons-icon" style="background: url(' . TIMETRADER_ASSETS_URL .
                    '/timetrader/logo.png); background-repeat: no-repeat; background-position: left bottom;"></span> ' .
        __( "Add slider", "timetrader" ) . '</a>';
        }
        return $context;
        }



        /**
        * Append the 'Choose Time Trader' thickbox content to the bottom of selected admin pages
        */
        public function admin_footer() {
            global $pagenow;
            // Only run in post/page creation and edit screens
            if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) {
            $sliders = $this->all_meta_sliders( 'title' );
            ?>
            <script type="text/javascript">
            jQuery(document).ready(function() {
            jQuery('#inserttimetrader').on('click', function() {
            var id = jQuery('#timetrader-select option:selected').val();
            window.send_to_editor('[timetrader id=' + id + ']');
            tb_remove();
            })
            });
            </script>
            <div id="choose-meta-slider" style="display: none;">
            <div class="wrap">
            <?php
            if ( count( $sliders ) ) {
            echo "<h3 style='margin-bottom: 20px;'>" . __( "Insert Time Trader", "timetrader" ) . "</h3>";
            echo "<select id='timetrader-select'>";
            echo "<option disabled=disabled>" . __( "Choose slideshow", "timetrader" ) . "</option>";
            foreach ( $sliders as $slider ) {
            echo "<option value='{$slider['id']}'>{$slider['title']}</option>";
            }
            echo "</select>";
            echo "<button class='button primary' id='inserttimetrader'>" . __( "Insert slideshow", "timetrader" ) . "</button>";
            } else {
            _e( "No slideshows found", "timetrader" );
            }
            ?>
            </div>
            </div>
            <?php
            }
        }

        /**
        * Add settings link on plugin page
        */
        public function upgrade_to_pro_link( $links ) {
            // if ( function_exists( 'is_plugin_active' ) && ! is_plugin_active( 'timetrader-pro/timetrader-pro.php' ) ) {
            //     $links[] = '<a href="http://www.timetrader.com/upgrade" target="_blank">' . __( "Go Pro", "timetrader" ) . '</a>';
            // }
            // return $links;
        }


        /**
        * Upgrade CTA.
        */
        public function upgrade_to_pro_cta() {
            // global $user_ID;
            // if ( function_exists( 'is_plugin_active' ) && ! is_plugin_active( 'timetrader-pro/timetrader-pro.php' ) ) {
            //     $link = apply_filters( 'timetrader_hoplink', 'http://www.timetrader.com/upgrade/' );
            //     $link .= '?utm_source=lite&amp;utm_medium=nag&amp;utm_campaign=pro';
            //     $text = "Time Trader v" . TIMETRADER_VERSION . " - " . __( 'Upgrade to Pro $19', "timetrader" );
            //     echo "<div style='display: none;' id='screen-options-link-wrap'><a target='_blank' class='show-settings dashicons-before dashicons-performance' href='{$link}'>{$text}</a></div>";
            // }
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


        private function create_tables() {
            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE {$wpdb->prefix}timetrader_reservation (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    client_id mediumint(9) NOT NULL,
                    subject varchar(200),
                    description varchar(1000),
                    status_id mediumint(9) NOT NULL,
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
                    relative varchar(45),
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
                    time_available DATE,
                    UNIQUE KEY id (id)
                ) $charset_collate;
                CREATE TABLE {$wpdb->prefix}timetrader_time_available (
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



    }

endif;

add_action( 'plugins_loaded', array( 'TimeTraderPlugin', 'init' ), 10 );
