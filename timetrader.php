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
if (!defined('ABSPATH')) {
    exit; // disable direct access
}

if (!class_exists('TimeTraderPlugin')) :

    class TimeTraderPlugin {

        public $version = '0.1';

        public static function init() {
            $timetrader = new self();
        }

        public function __construct() {
            $this->define_constants();
            $this->includes();
            $this->setup_actions();
            // $this->create_tables();
        }

        private function define_constants() {
            define('TIMETRADER_VERSION', $this->version);
            define('TIMETRADER_BASE_URL', trailingslashit(plugins_url('timetrader')));
            define('TIMETRADER_ASSETS_URL', trailingslashit(TIMETRADER_BASE_URL . 'assets'));
            define('TIMETRADER_PATH', plugin_dir_path(__FILE__));
        }

        private function plugin_classes() {
            return array(
                'timetradersystemcheck' => TIMETRADER_PATH . 'includes/timetrader.systemcheck.class.php',
                    // 'simple_html_dom'        => TIMETRADER_PATH . 'includes/simple_html_dom.php'
            );
        }

        private function includes() {
            $autoload_is_disabled = defined('TIMETRADER_AUTOLOAD_CLASSES') && TIMETRADER_AUTOLOAD_CLASSES === false;
            if (function_exists("spl_autoload_register") && !( $autoload_is_disabled )) {
                // >= PHP 5.2 - Use auto loading
                if (function_exists("__autoload")) {
                    spl_autoload_register("__autoload");
                }
                spl_autoload_register(array($this, 'autoload'));
            } else {
                // < PHP5.2 - Require all classes
                foreach ($this->plugin_classes() as $id => $path) {
                    if (is_readable($path) && !class_exists($id)) {
                        require_once( $path );
                    }
                }
            }
        }

        /**
         * Autoload Timetrader classes to reduce memory consumption
         */
        public function autoload($class) {
            $classes = $this->plugin_classes();
            $class_name = strtolower($class);
            if (isset($classes[$class_name]) && is_readable($classes[$class_name])) {
                require_once( $classes[$class_name] );
            }
        }

        /**
         * Hook Time Trader into WordPress
         */
        private function setup_actions() {
            add_action('admin_menu', array($this, 'register_admin_menu'), 9554);
            add_action('init', array($this, 'register_post_type'));
            add_action('init', array($this, 'register_taxonomy'));
            add_action('widgets_init', array($this, 'register_timetrader_widget'));
            if (defined('TIMETRADER_ENABLE_RESOURCE_MANAGER') && TIMETRADER_ENABLE_RESOURCE_MANAGER === true) {
                add_action('template_redirect', array($this, 'start_resource_manager'), 0);
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
            register_post_type('timetrader', array(
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
            register_taxonomy('timetrader', 'attachment', array(
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
            $title = apply_filters('timetrader_menu_title', 'Time Trader');
            $capability = apply_filters('timetrader_capability', 'edit_others_posts');

            $page = add_menu_page(
                    $title, $title, $capability, 'timetrader', array($this, 'render_admin_page'), TIMETRADER_ASSETS_URL . 'timetrader/logo-timetrader.png'
            );

            // ensure our JavaScript is only loaded on the Time Trader admin page
            add_action('admin_print_scripts-' . $page, array($this, 'register_admin_scripts'));
            add_action('admin_print_styles-' . $page, array($this, 'register_admin_styles'));
            add_action('load-' . $page, array($this, 'help_tab'));




            $page = add_submenu_page('timetrader', __('Agendamentos', 'timetrader'), __('Agendamentos', 'timetrader'), $capability, 'timetrader-sub-menu', //aqui vai a function da nova pagina
                    array($this, 'sub_menu') //aqui vai a funcition que renderiza a pagina
            );


            $page = add_submenu_page('timetrader', __('Edita Reserva', 'timetrader'), __('', 'timetrader'), $capability, 'edit-reserva-sub-menu', //aqui vai a function da nova pagina
                    array($this, 'sub_edicaoreserva') //aqui vai a funcition que renderiza a pagina
            );
            //add_action( 'admin_print_styles-' . $page, array( $this, 'register_admin_styles' ) );
        }

        /**
         * render pagina sub menu
         */
        public function sub_menu() {
            ?>

            <style>
                
          #wpwrap {
	padding-right: 20px;
        background: #242f45 !important;
}

#reservation label{
 color: #FFF !important;   
}
                
                #time-trader .consulta-h{
                    font-weight: bold;
                    font-size: 26px;
                    margin-bottom: 40px;
                    text-align: center;
                    margin-bottom: 70px;
                    margin-top: 50px;
                    color: #b1c903;
                }
                
                #time-trader td p,td{
                    color: #FFF;
                      font-family: 'calibri';
                      font-size: 16px;
                      padding: 10px;
                      margin: 0;
                }
                
                #time-trader .apagarreserva{
                    width: 37px;
                    height: 39px;
                    background: url(<?php echo get_template_directory_uri() ?>/assets/img/exclude.png);
                      display: block;
                        margin: 0 auto;
                }
                
                 #time-trader .editarreserva{
                    width: 37px;
                    height: 38px;
                    background: url(<?php echo get_template_directory_uri() ?>/assets/img/edit.png);
                      display: block;
                        margin: 0 auto;
                }
                
                 #time-trader #paginador{
                  width: 100px;
                  margin: 30px auto;
                }
                
                
                #time-trader #paginador a{
                 color: #FFF;
                text-decoration: none;
                font-size: 16px;   
                }
                
                table tbody b{
                color: #b1c903;
                }
                
                table td p{
                color: #FFF;
                }
                
  #paginador .selected{
      background-color: #FFF;
      padding: 2px 4px 0px 4px;
      color: #000 !important;
  }    
  </style>
            <div id="time-trader">
            <?php
            
            
            
            
            
            if ($_GET['acao'] == "apagarreserva") {
                $GLOBALS['wpdb']->get_results("delete from ad_timetrader_reservation where id=" . $_GET['id'], OBJECT);
                $GLOBALS['wpdb']->get_results("delete from ad_timetrader_date_has_reservation where id_reservation=" . $_GET['id'], OBJECT);
                header("Location: ?page=timetrader-sub-menu");
            }
            
            
            $pagina = $_GET['pagina'];  //Pega a página atual
            if($pagina == null){  //Se não exstir um GET a página é a 1
                $pagina = 1;
            }
            $paginas = $GLOBALS['wpdb']->get_results("select count(id) as total from ad_timetrader_reservation", OBJECT); //Select que busca o total de itens do banco
            $paginas = $paginas[0]->total; //Pega do array apenas a primeira posição, que é o total de itens
            
            $itenspagina = 8;   //Quantidade de itens que vai aparecer na página
            $inicio = ($pagina - 1) * $itenspagina;  //Valor que inicia o limite
            $qtpaginas = $paginas / $itenspagina; //Quantidade de páginas que serão criadas, utilizada no for 
            $qtpaginas = ceil($qtpaginas); //Arredonda para cima a quantidade de páginas
            $final = $inicio + $itenspagina; //Final do LIMIT
            
            
         
            

            $reservas = $GLOBALS['wpdb']->get_results("select res.id, res.`subject`, res.description, res.reservation_info, sts.description as estado, cli.`Nome`, dtres.id_reservation,dtres.id_reservation_info,dta.date_available from ad_timetrader_reservation res 
INNER JOIN ad_timetrader_status sts ON sts.id=res.status_id 
INNER JOIN ad_clientes cli ON cli.idcliente=res.client_id
INNER JOIN ad_timetrader_date_has_reservation dtres ON dtres.id_reservation=res.id
INNER JOIN ad_timetrader_reservation_info resinfo ON resinfo.id=dtres.id_reservation_info
INNER JOIN ad_timetrader_date_available dta ON dta.id=resinfo.date_available_id GROUP BY res.id order by res.id DESC LIMIT ".$inicio.",".$itenspagina."", OBJECT);


            
            ?>
            <h2 class="consulta-h">Consulta de reservas</h2>
            <table width="100%">
                <tbody>
                    <tr>
                        <td><b>Código da compra</b></td>
                        <td><b>Horário</b></td>
                        <td><b>Assunto</b></td>
                        <td><b>Descrição</b></td>
                        <td><b>Status</b></td>
                        <td><b>Apagar</b></td>
                        <td><b>Editar</b></td>
                    </tr>
                    <?php
                    foreach ($reservas as $reserva) {
                        ?>
                        <tr style="<?php echo ($reserva->estado=="Pago" ? "background-color: #5b6984"  : "background-color: #2b3750") ?>  ">
                           <td><?php echo str_pad($reserva->id, 8, 0, STR_PAD_LEFT); ?></td>
                            <td>
                                <?php
                                
                                $data= explode("-",$reserva->date_available);
                                echo "<p><b>".$data[2]."-".$data[1]."-".$data[0]."</b></p>";
                                $horarios = $GLOBALS['wpdb']->get_results("select resinfo.id, time_available, resinfo.date_available_id, timeav.time_available,dtav.date_available,dtres.id_reservation from ad_timetrader_reservation_info resinfo
                                INNER JOIN ad_timetrader_time_available timeav ON timeav.id=resinfo.time_available_id
                                INNER JOIN ad_timetrader_date_available dtav ON dtav.id=resinfo.date_available_id
                                INNER JOIN ad_timetrader_date_has_reservation dtres ON dtres.id_reservation_info=resinfo.id
                                where dtres.id_reservation=" . $reserva->id, OBJECT);
                                
               
                                //var_dump($horarios);
                                foreach ($horarios as $horario) {
                                  echo "<p>" . $horario->time_available . "</p>";
                                }
                                ?>
                            </td>
                            <td><?php echo $reserva->subject; ?></td>
                            <td><?php echo $reserva->description; ?></td>
                            <td><?php echo $reserva->estado; ?></td>
                            <td><a iddel="<?php echo $reserva->id; ?>" class="apagarreserva" href="?page=timetrader-sub-menu&acao=apagarreserva&id=<?php echo $reserva->id; ?>"></a></td>
                            <td><a class="editarreserva" href="?page=edit-reserva-sub-menu&id=<?php echo $reserva->id; ?>"></a></td>
                        </tr>
                        <?php
                    }
                    ?>

                </tbody>
            </table>
            
            <div id="paginador">
                <?php
                for($i = 1; $i<=$qtpaginas; $i++){
                ?>
                <a <?php echo ($pagina==$i ? "class=selected" : "") ?> href="admin.php?page=timetrader-sub-menu&pagina=<?php echo $i?>"><?php echo $i ?></a>
                <?php
                }
                ?>
            </div>
            
        <script type='text/javascript'>
           jQuery(document).ready(function () {
            jQuery('.apagarreserva').bind('click', function(){   
                if (confirm("Você deseja mesmo Excluir esta reserva?")) {
                    window.location.href = "?page=timetrader-sub-menu&acao=apagarreserva&id="+jQuery(this).attr("iddel");
            }
                return false;
            });
        });
       </script>
       
            </div>
            <?php
        }

        public function sub_edicaoreserva() {
           
            ?>
            <style>
                
          #wpwrap {
	padding-right: 20px;
        background: #242f45 !important;
}
    
            #time-trader .apagarreserva{
                    width: 37px;
                    height: 38px;
                    background: url(<?php echo get_template_directory_uri() ?>/assets/img/exclude.png);
                      display: block;
                }
                
                
#reservation label{
 color: #FFF !important;   
}
       
                
                #time-trader .consulta-h {
                    font-weight: bold;
                    font-size: 26px;
                    margin-bottom: 40px;
                    text-align: center;
                    margin-bottom: 70px;
                    margin-top: 50px;
                    color: #b1c903;
                  }
                  
                  #time-trader td p, td {
                    color: #FFF;
                    font-family: 'calibri';
                    font-size: 16px;
                    padding: 3px;
                    margin: 0;
                    min-height: 40px;
                  }
                  
                  #status-horario select{
                   background: none;
                   color: #FFF;
                   height: 34px;
                  }
         
                  #status-horario option{
                  background-color: #242f45;
                  }

                   #status-horario select:focus {
                    outline: none !important;
                  }
                  
                  #status-horario option:focus {
                    outline: none !important;
                  }
                   
                  #status-horario input[type="submit"]{
                        border: 2px solid #b1c903;
                        background: none;
                        color: #b1c903;
                        width: 140px;
                        height: 35px;
                        text-transform: uppercase;
                        cursor: pointer;
                          transition: 0.8s;
                  }
                  
                  #status-horario input[type="submit"]:hover{
                  background-color: #b1c903;
                  color: #242f45;
                  }
                  
                  #time-trader .data-cyuu{
                  color: #b1c903;
                  }
                  
                  #time-trader td{
                      padding-top: 20px;
                  }
                  
            </style>
            <div id="time-trader">
            <?php
            $id_reserva = $_GET['id'];
            
            if($_POST['idstatus']){
                //die("UPDATE ad_timetrader_reservation SET status_id=".$_POST['idstatus']." WHERE status_id=" . $id_reserva);
                $GLOBALS['wpdb']->get_results("UPDATE ad_timetrader_reservation SET status_id=".$_POST['idstatus']." WHERE id=" . $id_reserva, OBJECT);
            }
            
            $status = $GLOBALS['wpdb']->get_results("select id,description from ad_timetrader_status", OBJECT);
            
            $reservas = $GLOBALS['wpdb']->get_results("select res.id,res.`subject`, res.description as resdescription, cli.`Nome` nome, cli.Email, cli.Idade,cli.Sexo,cli.Pais,cli.Estado, cli.Skype, sts.description, sts.id as idstatus from ad_timetrader_reservation res INNER JOIN ad_clientes cli on cli.idcliente=res.client_id
INNER JOIN ad_timetrader_status sts ON sts.id=res.status_id where res.id=" . $id_reserva, OBJECT);

            ?>
            <h2 class="consulta-h">Consulta de reservas</h2>
            <table width="100%">


                <?php
                foreach ($reservas as $reserva) {
                    ?>
                    <tr style="">
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width="20%">
                                        Código da compra
                                    </td>
                                    
                                    <td width="80%">                                            
                                        <?php echo str_pad($reserva->id, 8, 0, STR_PAD_LEFT); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%">
                                        Cliente
                                    </td>
                                    
                                    <td width="80%">                                            
                                        <?php echo $reserva->nome; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%">
                                        E-mail
                                    </td>
                                    
                                    <td width="80%">                                            
                                        <?php echo $reserva->Email; ?>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td width="20%">
                                        Skype
                                    </td>
                                    
                                    <td width="80%">                                            
                                        <?php echo $reserva->Skype; ?>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td width="20%">
                                        Horários agendados
                                    </td>
                                    <td width="80%">
                                        <?php
                                        $horarios = $GLOBALS['wpdb']->get_results("select resinfo.id, resinfo.time_available_id, resinfo.date_available_id, timeav.time_available,dtav.date_available,dtres.id_reservation from ad_timetrader_reservation_info resinfo
                                                  INNER JOIN ad_timetrader_time_available timeav ON timeav.id=resinfo.time_available_id
                                                  INNER JOIN ad_timetrader_date_available dtav ON dtav.id=resinfo.date_available_id
                                                  INNER JOIN ad_timetrader_date_has_reservation dtres ON dtres.id_reservation_info=resinfo.id
                                                  where dtres.id_reservation=" . $reserva->id, OBJECT);
               
                                                              
                                        
                                        $data = explode("-",$horarios[0]->date_available);
                                         echo "<p class='data-cyuu'>".$data[2]."-".$data[1]."-".$data[0]."</p>";
                                        foreach ($horarios as $horario) {
                                            echo "<p>" . $horario->time_available . "</p>";
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="20%">
                                        Asunto da consuttoria
                                    </td>
                                    
                                    <td width="80%">                                            
                                        <?php echo $reserva->subject; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="20%">
                                        Dessrição
                                    </td>
                                    
                                    <td width="80%">
                                        <?php echo $reserva->resdescription; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="20%">
                                        Sitação da consultoria
                                    </td>
                                    <td width="80%">
                                        <form id="status-horario" acttion="#" method="POST">
                                             <select name="idstatus">
                                            <option>Selecione o estado</option>
                                             <?php 
                                             
                                                foreach ($status as $estado){
                                                    echo "<option ".(($reserva->idstatus==$estado->id) ? "selected" : "")." value='".$estado->id."'>".$estado->description."</option>";
                                                }
                                                echo $reserva->status; ?>
                                            
                                        </select>
                                            <input type="submit" value="Alterar status">
                                        </form>
                                       
                                       
                                    </td>
                                </tr>

                                <tr>
                                    <td width="20%">
                                        Apagar
                                    </td>
                                    <td width="80%"><a iddel="<?php echo $reserva->id; ?>" class="apagarreserva" href="?page=timetrader-sub-menu&acao=apagarreserva&id=<?php echo $reserva->id; ?>"></a></td>
                                </tr>
                            </table>




                        </td>
                    </tr>
                    <?php
                }
                ?>


            </table>
            </div>
          <script type='text/javascript'>
            jQuery(document).ready(function () {
            jQuery('.apagarreserva').bind('click', function(){   
                if (confirm("Você deseja mesmo Excluir esta reserva?")) {
                    window.location.href = "?page=timetrader-sub-menu&acao=apagarreserva&id="+jQuery(this).attr("iddel");
            }
                return false;
            });
        });
       </script>  
            

            <?php
        }

        /**
         * Register admin styles
         */
        public function register_admin_styles() {
            wp_enqueue_style('timetrader-fullcalendar-styles', TIMETRADER_ASSETS_URL . 'calendar/css/fullcalendar.css', false, TIMETRADER_VERSION);
            do_action('timetrader_register_admin_styles');
        }

        /**
         * Register admin JavaScript
         */
        public function register_admin_scripts() {
            // media library dependencies
            wp_enqueue_media();
            // plugin dependencies
            wp_enqueue_script('jquery-ui-core', array('jquery'));
            wp_enqueue_script('jquery-ui-sortable', array('jquery', 'jquery-ui-core'));
            wp_dequeue_script('link'); // WP Posts Filter Fix (Advanced Settings not toggling)
            wp_dequeue_script('ai1ec_requirejs'); // All In One Events Calendar Fix (Advanced Settings not toggling)

            wp_enqueue_script('timetrader-calendar-moment', TIMETRADER_ASSETS_URL . 'calendar/js/moment.min.js', array('jquery'), TIMETRADER_VERSION);
            wp_enqueue_script('timetrader-calendar-fullcalendar', TIMETRADER_ASSETS_URL . 'calendar/js/fullcalendar.min.js', array('jquery'), TIMETRADER_VERSION);
            do_action('timetrader_register_admin_scripts');
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
            if (get_user_meta($user_ID, "timetrader_view", true)) {
                return get_user_meta($user_ID, "timetrader_view", true);
            }
            return 'tabs';
        }

        /**
         * Render the admin page
         */
        public function render_admin_page() {
            
            ?>
            
             <style>
                
          #wpwrap {
	padding-right: 20px;
        background: #242f45 !important;
}


#reservation label{
 color: #FFF !important;   
}
       

.fc-widget-header{
 color: #73808c;
}

.fc-day-number{
 color: #859ab2;   
}

.fc .fc-toolbar > * >:first-child {
  /* margin-left: 0; */
  margin: 0 auto;
  width: 100%;
    color: #b1c903;
}

.fc-toolbar .fc-left {
  /* float: left; */
  margin: 0 auto;
  width: 100%;
}

#hr-manha{
    float: left;
  width: 210px;
}


#hr-tarde{
    float: left;
  width: 210px;
}

#container-horarios{
   width: 470px;
  margin: 0 auto;   
}

#container-horarios input[type="submit"] {
  border: 2px solid #b1c903;
  background: none;
  color: #b1c903;
  width: 140px;
  height: 35px;
  text-transform: uppercase;
  cursor: pointer;
  transition: 0.8s;
}

#container-horarios input[type="submit"]:hover {
  background-color: #b1c903;
  color: #242f45;
}

#container-horarios #enviar{
  float: left;
  width: 310px;
  text-align: center;
  margin-top: 30px;
  }
  
  .fc-right button.fc-today-button{
      background-color: #3b4863;
  } 
  
  .fc-button-group button{
      background-color: #3b4863;
  } 
  
  .fc-state-default {
  background: #3b4863!important;
  background-image: -moz-linear-gradient(top,#3b4863,#3b4863)!important;
  background-image: -webkit-gradient(linear,0 0,0 100%,from(#3b4863),to(#3b4863))!important;
  background-image: -webkit-linear-gradient(top,#3b4863,#3b4863)!important;
  background-image: -o-linear-gradient(top,#3b4863,#3b4863)!important;
  background-image: linear-gradient(to bottom,#3b4863,#3b4863)!important;
  background-repeat: repeat-x!important;
  border: 0!important;
  color: #fff!important;
  text-shadow: 0 0 0 rgba(255,255,255,0.75)!important;
  box-shadow: inset 0 0 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.0)!important;
  background-color: #000 !important;
  }
  
  .fc .fc-toolbar > * > * {
  float: left;
  margin-left: .0em;
  margin-top: 10px;
}

.fc-bg span{ 
  width: 50%;
  height: 50%;
  display: block;
  margin: 0 auto;
  vertical-align: middle;
  border-radius: 50%;
}

#calendar{
    width: 700px;
    margin: 0 auto;
}
        </style>
            <?php

            // ERRO AO USAR OS DOIS AJAX/JavaScript JUNTOS 
            // code php of admin page
            global $wpdb;

            if ('POST' == $_SERVER['REQUEST_METHOD']) {
                require_once( ABSPATH . 'wp-load.php' );
                $date_available = $_POST['date_available'];
                $time_available_str = $_POST['time_available'];
                $time_available_ocupado = $_POST['time_available_ocupado'];
                $time_available = explode('|', $time_available_str, -1);
                $time_available_ocupado = explode('|', $time_available_ocupado, -1);
                $this->insert_values($date_available, $time_available, $time_available_ocupado);
            }

            if ('GET' == $_SERVER['REQUEST_METHOD']) {
                if ($_GET['acao'] == "listahorarios") {
                    $horario_reservado_dia = $GLOBALS['wpdb']->get_results("select tr.id, tr.time_available_id, tr.date_available_id, da.date_available FROM " . $wpdb->prefix . "timetrader_reservation_info tr INNER JOIN " . $wpdb->prefix . "timetrader_date_available da on da.id=tr.date_available_id where date_available='" . $_GET['data'] . "'", OBJECT);
                    $horario_agendado = $GLOBALS['wpdb']->get_results("select res.id, res.date_available_id, dtres.id_reservation_info, dtav.date_available,res.time_available_id  from ad_timetrader_reservation_info res INNER JOIN ad_timetrader_date_has_reservation dtres
                    on res.id=dtres.id_reservation_info
                    INNER JOIN ad_timetrader_date_available dtav
                    ON dtav.id=res.date_available_id where  dtav.date_available='" . $_GET['data'] . "'", OBJECT);

                    // var_dump($horario_agendado);
                    $id_dia = $GLOBALS['wpdb']->get_results("select dt.id from ad_timetrader_date_available dt where dt.date_available='" . $_GET['data'] . "'", OBJECT);

                    if (empty($id_dia)) {

                        $time_available = $GLOBALS['wpdb']->get_results("SELECT id, time_available FROM " . $wpdb->prefix . "timetrader_time_available", OBJECT);
                        // $time_available = $GLOBALS['wpdb']->get_results( "SELECT timres.id, TIME_FORMAT(timres.time_available, '%H:%i') as time_available FROM ad_timetrader_time_available timres", OBJECT );
                    } else {
                        //   $time_available = $GLOBALS['wpdb']->get_results( "SELECT timres.id, TIME_FORMAT(timres.time_available, '%H:%i') as time_available,resinfo.date_available_id, dateav.date_available FROM ad_timetrader_time_available timres
                        // LEFT JOIN ad_timetrader_reservation_info resinfo ON timres.id=resinfo.time_available_id and resinfo.date_available_id=".$id_dia[0]->id."
                        //  LEFT JOIN ad_timetrader_date_available dateav ON dateav.id=resinfo.date_available_id
                        //  GROUP BY timres.id", OBJECT );


                        $time_available = $GLOBALS['wpdb']->get_results("select id,time_available from ad_timetrader_time_available", OBJECT);
                    }


                    $horarios = array();
                    $horarios[0] = $horario_reservado_dia;
                    // if(!($horarios[0])){
                    // }
                    $horarios[1] = $time_available;
                    $horarios[2] = $horario_agendado;
                    die(json_encode($horarios));
                    ?>

                    <?php
                }
            }
            ?>
            <script type='text/javascript'>
            // code javascript
            
                jQuery(document).ready(function () {

            jQuery('.apagarreserva').bind('click', function(){   
                alert("derfer");
                if (confirm("Você deseja mesmo excluir?")) {
                    window.location.href = "?page=timetrader-sub-menu&acao=apagarreserva&id="+jQuery(this).attr("iddel");
                   // $.post( "test.php" );
            }
                return false;
            });
            
                    jQuery('#calendar').fullCalendar({
                        dayClick: function (date) {
                            jQuery('.fc-day').css('background-color', '');
                            jQuery(".fc-bg span").css('background-color', 'transparent');
                            jQuery(this).find("span").css('background-color', '#b1c903');
                            jQuery('#reservation .date_available').attr('value', date.format());
                            
                            

                            var urlThis = window.location.href;
                            var urlData = "acao=listahorarios&data=" + date.format();
                            //var urlData = "&date_available=" + date.format();
                            //var acao = "listadatas"
                            jQuery.ajax({
                                type: "GET",
                                url: urlThis,
                                async: true,
                                data: urlData,
                                error: function (xhr, statusText) {
                                },
                                success: function (result) {


                                    var texto = result.split("[[").pop();
                                    var tex = texto.split("],");


                                    var horarios = (tex[1]);
                                    var horariosreservados = (tex[0]);
                                    var horariosagendado = (tex[2]);


                                    var novovalor = horarios.replace("]", "");



                                    novovalor = novovalor.replace("]", "");

                                    novovalor = novovalor.replace("[", "");



                                    var res = novovalor.split("},");




                                    var hres = horariosreservados.split("},");


                                    var novovalor = horariosagendado.replace("]", "");
                                    var hragendado = novovalor.replace("]", "");
                                    hragendado = hragendado.replace("[", "");

                                    var hag = hragendado.split("},");




                                    for ($i = 0; $i < ((res.length) - 1); $i++) {
                                        res[$i] = res[$i] + "}";
                                    }

                                    for ($i = 0; $i < ((hres.length) - 1); $i++) {
                                        hres[$i] = hres[$i] + "}";
                                    }


                                    for ($i = 0; $i < ((hag.length) - 1); $i++) {
                                        hag[$i] = hag[$i] + "}";
                                    }





                                    var myobj = [];
                                    for ($i = 0; $i < res.length; $i++) {
                                        myobj[$i] = JSON.parse(res[$i]);
                                    }


                                    if (horariosreservados === "") {
                                        hres = 0;
                                    }


                                    var hresmyobj = [];
                                    for ($i = 0; $i < hres.length; $i++) {
                                        hresmyobj[$i] = JSON.parse(hres[$i]);
                                    }

                                    if (horariosagendado === "[]]") {
                                        hag = 0;
                                    }
                                    var hragenda = [];
                                    for ($i = 0; $i < hag.length; $i++) {
                                        hragenda[$i] = JSON.parse(hag[$i]);
                                    }

                                    var html = "<input type='hidden' class='date_available' name='date_available' id='date_available' value=" + date.format() + "></br>";
                                    var check = "";
                                    var marcado = "";
                                    var manha = "";
                                    var tarde = "";
                                
                                    for ($i = 0; $i < res.length; $i++) {
                                        for ($y = 0; $y < hresmyobj.length; $y++) {
                                            if (myobj[$i]["id"] === hresmyobj[$y]["time_available_id"]) {
                                                check = "checked";
                                            }
                                        }

                                        for ($k = 0; $k < hragenda.length; $k++) {
                                            if (myobj[$i]["id"] === hragenda[$k]["time_available_id"]) {
                                                marcado = "disabled";
                                            }
                                        }
                                        
                                    if (myobj[$i]["id"] < 10) {
                                        manha += "<p><input " + (marcado !== "" ? "estado=desativado" : "estado=ativado") + " " + marcado + " " + check + " type='checkbox' value=" + myobj[$i]["id"] + "><label " + (marcado !== "" ? "title='Existe reserva para este horário. Para alterar você precisa excluir esta reserva'" : "") + " class='label_time_available' for=" + myobj[$i]["id"] + ">" + myobj[$i]["time_available"] + "</label></p>";
                                    } else {
                                        tarde += "<p><input " + (marcado !== "" ? "estado=desativado" : "estado=ativado") + " " + marcado + " " + check + " type='checkbox' value=" + myobj[$i]["id"] + "><label " + (marcado !== "" ? "title='Existe reserva para este horário. Para alterar você precisa excluir esta reserva'" : "") + " class='label_time_available' for=" + myobj[$i]["id"] + ">" + myobj[$i]["time_available"] + "</label></p>";
                                    }
                                    
                                        //html += "<input " + (marcado !== "" ? "estado=desativado" : "estado=ativado") + " " + marcado + " " + check + " type='checkbox' value=" + myobj[$i]["id"] + "><label " + (marcado !== "" ? "title='Existe reserva para este horário. Para alterar você precisa excluir esta reserva'" : "") + " class='label_time_available' for=" + myobj[$i]["id"] + ">" + myobj[$i]["time_available"] + "</label></br>";
                                        check = "";
                                        marcado = "";

                                    }
                                    html += "<div id='hr-manha'>"+manha+"</div>";
                                    html += "<div id='hr-tarde'>"+tarde+"</div>";
                                    
                                    html += "<div id='enviar'><input type='submit' value='Salvar'><span class='progress'></span></div>";

                                    jQuery("#reservation").html("<div id='container-horarios'>"+html+"</div>");

                                },
                            });

                        },
                        loading: function (bool) {
                            // funcao de loading para caregar
                            // jQuery('#loading').toggle(bool);
                        }
                    });
                    
                  
                    
                     var largura = jQuery(".fc-content-skeleton .fc-day-number").width();   
                    jQuery(".fc-content-skeleton .fc-day-number").height(largura);
                        
                        
                       jQuery("button").click(function(){
                        var largura = jQuery(".fc-content-skeleton .fc-day-number").width();   
                        jQuery(".fc-content-skeleton .fc-day-number").height(largura);
                       });

                    jQuery('#reservation').bind('submit', function (event) {
                        event.preventDefault();
                        var date_available = jQuery('#date_available').val();
                        var time_available = '';
                        var time_available_ocupado = '';
                        jQuery(':checkbox').each(function () {
                            var ischecked = jQuery(this).is(':checked');
                            // alert(jQuery(this).attr("estado"));

                            if ((jQuery(this).attr("estado")) !== "desativado") {
                                if (ischecked) {
                                    time_available += jQuery(this).val() + '|';
                                }
                            } else {
                                if (ischecked) {
                                    time_available_ocupado += jQuery(this).val() + '|';
                                }
                            }


                        });
                        var urlData = "&date_available=" + date_available + "&time_available=" + time_available + "&time_available_ocupado=" + time_available_ocupado;
                        var urlThis = window.location.href;
                        jQuery.ajax({
                            type: "POST",
                            url: urlThis,
                            async: true,
                            data: urlData,
                            error: function (xhr, statusText) {
                                //console.log( 'error' );
                                jQuery('.progress').text('Erro!');
                            },
                            success: function (data) {
                                //console.log( 'success' );
                                jQuery('.progress').text('Salvo com Sucesso!');
                                setTimeout(function () {
                                    jQuery('.progress').fadeOut();
                                }, 5000);
                            },
                            beforeSend: function () {
                                // console.log( 'beforeSend' );
                                 jQuery('.progress').fadeIn('slow');
                                jQuery('.progress').text('Salvando...');
                            },
                            complete: function () {
                                // console.log( 'complete' );
                            }
                        });
                    });

                });
            </script>

            <div id="cot-lk">

            </div>
            <!-- body plugin -->
            <div class="wrap timetrader">
                <div id="calendar" style=""></div>
                <form id="reservation">

                </form>
            </div>
            <?php
        }

        /**
         * Insert values
         */
        public function insert_values($date_available, $time_available, $time_available_ocupado) {

            global $wpdb;

            //var_dump($time_available_ocupado);die();

            $ocupado = "";
            foreach ($time_available_ocupado as $hocupado) {
                $ocupado = $ocupado . " time_available_id <> " . $hocupado . " AND ";
            }

            $ocupado = $ocupado . " 1=1 ";

            $table_date_available = $wpdb->prefix . 'timetrader_date_available';
            $timetrader_time_available = $wpdb->prefix . 'timetrader_time_available';

            $timetrader_reservation = $wpdb->prefix . 'timetrader_reservation';
            $timetrader_reservation_info = $wpdb->prefix . 'timetrader_reservation_info';

            $timetrader_status = $wpdb->prefix . 'timetrader_status';

            $has_date = $GLOBALS['wpdb']->get_results("SELECT * FROM " . $wpdb->prefix . "timetrader_date_available WHERE date_available LIKE '" . $date_available . "';", OBJECT);
            // se ja tem uma data faz o update de infos naquela data
            if (empty($has_date)) {
                // insert
                $wpdb->insert($table_date_available, array(
                    'date_available' => $date_available
                        )
                );

                $date_available_get_id = $GLOBALS['wpdb']->get_results("SELECT * FROM " . $wpdb->prefix . "timetrader_date_available WHERE date_available LIKE '" . $date_available . "';", OBJECT);

                foreach ($date_available_get_id as $key => $date) {
                    $date_available_id = $date->id;
                }

                //var_dump( $date_available_id); die();

                foreach ($time_available as $value) {
                    $wpdb->insert($timetrader_reservation_info, array(
                        'date_available_id' => $date_available_id,
                        'time_available_id' => $value
                            )
                    );
                }
            } else {

                $date_available_get_id = $GLOBALS['wpdb']->get_results("SELECT * FROM " . $wpdb->prefix . "timetrader_date_available WHERE date_available LIKE '" . $date_available . "';", OBJECT);
                foreach ($date_available_get_id as $key => $date) {
                    $date_available_id = $date->id;
                }
       
       
                $GLOBALS['wpdb']->get_results("delete from ad_timetrader_reservation_info where date_available_id=" . $date->id . " and  (" . $ocupado . ")", OBJECT);

       

                foreach ($time_available as $value) {
                    $wpdb->insert($timetrader_reservation_info, array(
                        'date_available_id' => $date_available_id,
                        'time_available_id' => $value
                            )
                    );
                }
            }
        }

        /**
         * Start output buffering.
         *
         * Note: wp_ob_end_flush_all is called by default 
         *  - see shutdown action in default-filters.php
         */
        public function start_resource_manager() {
            ob_start(array($this, 'resource_manager'));
        }

        /**
         * Process the whole page output. Move link tags with an ID starting
         * with 'timetrader' into the <head> of the page.
         */
        public function resource_manager($buffer) {
            // create dom document from buffer
            $html = new simple_html_dom();
            // Load from a string
            $html->load($buffer, true, false);
            if (!$html->find('body link[id^="timetrader"]'))
                return $buffer;
            // selectors to find Time Trader links
            $selectors = array(
                'body link[id^="timetrader"]',
            );
            $selectors = apply_filters("timetrader_resource_manager_selectors", $selectors);
            if ($head = $html->find('head', 0)) {
                // move Time Trader elemends to <head>
                foreach ($selectors as $selector) {
                    foreach ($html->find($selector) as $element) {
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
            dbDelta($sql);

            // inserts default
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 1, 'time_available' => '0000-00-00 08:00:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 2, 'time_available' => '0000-00-00 08:30:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 3, 'time_available' => '0000-00-00 09:00:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 4, 'time_available' => '0000-00-00 09:30:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 5, 'time_available' => '0000-00-00 10:00:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 6, 'time_available' => '0000-00-00 10:30:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 7, 'time_available' => '0000-00-00 11:00:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 8, 'time_available' => '0000-00-00 11:30:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 9, 'time_available' => '0000-00-00 12:00:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 10, 'time_available' => '0000-00-00 12:30:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 11, 'time_available' => '0000-00-00 13:00:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 12, 'time_available' => '0000-00-00 13:30:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 13, 'time_available' => '0000-00-00 14:00:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 14, 'time_available' => '0000-00-00 14:30:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 15, 'time_available' => '0000-00-00 15:00:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 16, 'time_available' => '0000-00-00 15:30:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 17, 'time_available' => '0000-00-00 16:00:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 18, 'time_available' => '0000-00-00 16:30:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 19, 'time_available' => '0000-00-00 17:00:00',));
            $wpdb->insert($wpdb->prefix . 'timetrader_time_available', array('id' => 20, 'time_available' => '0000-00-00 17:30:00',));
        }

        /**
         * Add the help tab to the screen.
         */
        public function help_tab() {
            $screen = get_current_screen();
            // documentation tab
            $screen->add_help_tab(array(
                'id' => 'documentation',
                'title' => __('Documentation', 'timetrader'),
                'content' => "<p><a href='http://www.timetrader.com/documentation/' target='blank'>Time Trader Documentation</a></p>",
                    )
            );
        }

    }

    endif;
add_action('plugins_loaded', array('TimeTraderPlugin', 'init'), 10);
