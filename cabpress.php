<?php
/**
 * Plugin Name:       Cabpress
 * Plugin URI:        
 * Description:       Cab Booking for Wordpress. Our plugin allow professional drivers to get booking form customers receiving order by email. Customers can also check their itinerary using google maps API.
 * Version:           1.0.9
 * Author:            themesntemplates.com
 * Author URI:        https://themesntemplates.fr/
 * Text Domain: cpress
 * Domain Path: /languages
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

define( 'CPRESS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CPRESS_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

define('CPRESS_PRICE_PER_KM', get_option( 'cpress_price' ));
define('CPRESS_GOOGLE_MAP_API_KEY', get_option( 'cpress_api' ));
define('CPRESS_CITY_MAP', get_option( 'cpress_city' ));
define('CPRESS_COUNTRY', get_option( 'cpress_country' ));
define('CPRESS_CURRENCY', get_option( 'cpress_currency' ));
define('ADMIN_CPRESS_MAIL', get_option( 'cpress_mail' ));


$cpress_db_version = 1; // it will be increased if any change in DB
define( 'CPRESS_DB_VERSION', $cpress_db_version );

require_once 'classes/sql.php';
register_activation_hook( __FILE__, 'cpress_all_table' );

require_once 'classes/CpressCarsTable.php';
require_once 'classes/CpressOrdersTable.php';
require_once 'classes/CpressCarsForm.php';
require_once 'classes/CpressPlugin.php';


add_action( 'plugins_loaded', function () {
    Cpress_Plugin::get_instance();

    add_shortcode('cabpress-form', 'cpress_show_booking_form');

});

function cpress_cars_list_init()
{
      // Creating an instance
      $table = new Cpress_cars_Table();

      echo ('<div class="wrap"><h2>Cars List Table');
      echo ('<a class="add-new-h2" href="');
      echo (get_admin_url(get_current_blog_id(), "admin.php?page=cars_form"));
      echo ('">');
      esc_html_e("Add new", "cpress");
      echo ('</a>');
      echo ('</h2>');
      // Prepare table
      $table->prepare_items();
      // Display table
      $table->display();
      echo ('</div>');
}

function cpress_orders_list_init()
{
      // Creating an instance
      $table = new Cpress_orders_Table();

      echo '<div class="wrap"><h2>Orders List Table</h2>';
      // Prepare table
      $table->prepare_items();
      // Display table
      $table->display();
      echo '</div>';
}

add_action('admin_enqueue_scripts', 'cpress_car_script');
 
function cpress_car_script() {
    if (isset($_GET['page']) && $_GET['page'] == 'cars_form') {
        wp_enqueue_media();
        wp_register_script('upload-media', CPRESS_PLUGIN_DIR_URL.'js/uploadMedia.js','', false,true);
        wp_enqueue_script('upload-media');
    }
}

// Frontend codes
function cpress_show_booking_form() {

    //Get order data
    global $wpdb;
    $table = $wpdb->prefix . 'cars';
    $result = $wpdb->get_results(
        "SELECT * from {$table}",
        ARRAY_A
    );
    

    //assets for distance calculation
    wp_enqueue_style( 'cpress-map-style', plugins_url( '/css/style.css', __FILE__ ) );

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return "<p>Backend says hello from php</p>";
    } else {
        ?>
            <script src="<?php echo esc_html(CPRESS_PLUGIN_DIR_URL) ; ?>/js/map.js"
                PRICE_PER_KM="<?php echo esc_html(CPRESS_PRICE_PER_KM) ; ?>" 
                CITY_MAP="<?php echo esc_html(CPRESS_CITY_MAP ); ?>" 
                COUNTRY="<?php echo esc_html(CPRESS_COUNTRY['cpress_country']) ; ?>" 
            >
            </script>

            <link rel="stylesheet" href="<?php echo esc_html(CPRESS_PLUGIN_DIR_URL) ; ?>/public/checkout.css" />
            
            <div id="regForm">

                    <!-- Display booking details form step 1-->

                    <div class="tab"> <h2 class="booking-title is-layout-flow wp-block-column"><?php echo esc_html__("Réservez:", "cpress"); ?></h2>
                        <form id="order-start">
                            <div class="flex-container">
                                <div>
                                    <label>Date</label>
                                    <input type="date" id="orderDate" name="date" placeholder="dd-mm-yyyy" min="now()" required>
                                </div>
                                <div>
                                    <label><?php echo esc_html__("Departure hour:", "cpress"); ?></label>
                                    <input type="time" id="orderTime"  name="time" placeholder="HH:mm"  min="now()" required>
                                </div>
                            </div>
                            <div style="display: block">
                                <input
                                    id="origin-input"
                                    class="controls"
                                    name="origin"
                                    type="text"
                                    placeholder='<?php echo esc_html__("Enter an origin location", "cpress"); ?>'
                                    required
                                >

                                <input
                                    id="destination-input"
                                    class="controls"
                                    type="text"
                                    name="destination"
                                    placeholder='<?php echo esc_html__("Enter a destination location", "cpress"); ?>'
                                    required
                                >
                            </div>

                            <div id="map" style="height: 300px"></div>
                            <div class="distance" ></div>
                            <input type="hidden" id="order-distance" name="order-distance">
                        </form>
                    </div>

                    <!-- Display car choice form step 2-->
                    <div class="tab"> <h2 class="booking-title"><?php echo esc_html__("Choose a car:", "cpress"); ?></h2>
                        <form id="order-car">
                            <div id="display-total" ></div>
                        </form>                
                    </div>

                    <!-- Display a payment form step 3-->
                    <div class="tab" id="paymentPage"> <h2>informations:</h2>

                        <div class="panel-heading">
                            
                        </div>
                        <form id="order-end">
                            <div class="panel-body">
                            <div class="flex-container">
                                <div>
                                    <label><?php echo esc_html__("Lastname", "cpress"); ?></label>
                                    <input type="text" id="nom" name="lastname" required>
                                </div>
                                <div>
                                    <label><?php echo esc_html__("Firstname", "cpress"); ?></label>
                                    <input type="text" id="prenom" name="firstname" required>
                                </div>
                            </div>
                                <label><?php echo esc_html__("Phone", "cpress") ; ?></label>
                                <input type="tel" id="phone" name="telephone" required>
                                <label><?php echo esc_html__("Email", "cpress") ; ?></label>
                                <input type="email" id="mail" name="mail" required>
                                <div>
                                    <label><?php echo esc_html__("Passager number" ,  "cpress") ; ?></label>
                                    <input type="number" id="passenger" name="passenger">
                                </div>
                                <div>
                                    <label><?php echo esc_html__("luggages number" , "cpress") ; ?></label>
                                    <input type="number" id="luggage" name="luggage">
                                </div>
                                <label><?php echo esc_html__("Comment", "cpress") ; ?></label>
                                <textarea id="comment" name="comment" rows="4" cols="50"></textarea>
                            </div>   
                        </form>
                    </div>

                    <!-- Display a payment form step 3-->
                    <div class="tab"> <?php echo esc_html__("Paiement:", "cpress") ; ?>
                        <div class="panel-heading">
                            <!-- <h3 class="panel-title">Charge <?php //echo '$'.$itemPrice; ?> with Stripe</h3> -->
                            
                            <p><b><?php echo esc_html__("Price:" , "cpress"); ?></b> <span class="priceEnd" ></span> <?php echo CURRENCY; ?></p>
                        </div>
                    </div>
                    <!-- nav buttons -->
                    <div style="overflow:auto;">
                        <div >
                            <button type="button" id="prevBtn" style="float: left; width: auto" onclick="nextPrev(-1)"><?php echo esc_html__('Previous', 'cpress') ?></button>
                            <button type="button" id="nextBtn" style="float: right; width: auto" onclick="nextPrev(1)" ><?php echo esc_html__('Next', 'cpress') ?></button>
                            <button type="button" id="payCash" style="display: none; float: right; width: auto" ><?php echo esc_html__('Pay Cash', 'cpress') ?></button>
                        </div>
                    </div>
                    <!-- Modal content -->
                    <div id="myModal" class="modal">    
                        <div class="modal-content">
                        <span class="close">&times;</span>
                        <p id="modal-text"></p>
                        </div>
                    </div>
                    <!-- Circles which indicates the steps of the form: -->
                    <div style="text-align:center;margin-top:40px;">
                        <span class="step"></span>
                        <span class="step"></span>
                        <span class="step"></span>
                        <span class="step"></span>
                    </div>
            </div>
            <script async
                src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_html(CPRESS_GOOGLE_MAP_API_KEY) ; ?>&loading=async&callback=initMap&libraries=places&v=weekly"
            >
            </script>
            <script>
                //get form datas
                const allForm = document.forms;
                let formData = [];
                let itemsDatas = {};
                let carDatas = <?php echo wp_json_encode($result); ?>;
                let renderCars = document.getElementById('display-total');


                function getFormData() {
                //console.log(itemsDatas);

                    for (let i = 0; i < allForm.length; i++) {
                        //formData[i]= allForm[i].id;
                        let car
                        for (let x = 0; x < allForm[i].length; x++) {

                            if (allForm[i].elements[x].name == 'total-price' && allForm[i].elements[x].checked){
                                itemsDatas[allForm[i].elements[x].name] = allForm[i].elements[x].value;
                            }
                            itemsDatas[allForm[i].elements[x].id] = allForm[i].elements[x].value;
                        }

                    }
                    console.log(itemsDatas);

                }        

                //add car details to the second tab
                function addCarSelection() {    

                    if (itemsDatas['order-distance']) {
                        console.log(carDatas);
                        carDatas.forEach(displayCars);

                    }

                    function displayCars(item, index) {
                        // calculate total price per car
                        let totalprice = itemsDatas['order-distance'] / 1000 * item.price_km;
                        console.log(totalprice);
                        if (item.car_image) {
                            renderCars.innerHTML += '<div class="car-choice" >Brand :' + item.brand + '<br> Color :' + item.color + '<br> Passengers number :' + item.passagers_number + '<br> <img src=' + item.car_image + ' style="width: 200px"> <br> Price :' + totalprice.toFixed(2) + '<input type="radio" name="total-price" value=' + totalprice.toFixed(2) +' required></div>';
                        }else{
                        renderCars.innerHTML += '<div class="car-choice" >Brand :' + item.brand + '<br> Color :' + item.color + '<br> Passengers number :' + item.passagers_number + '<br> Price :' + totalprice.toFixed(2) + '<input type="radio" name="total-price" value=' + totalprice.toFixed(2) +' required></div>';
                        }
                    }
                }
            </script>
        <?php
    };

        wp_enqueue_script( 'cpress-multistep', plugins_url( '/js/multistep.js', __FILE__ ) ); 
        wp_localize_script( 'cpress-multistep', 'mixed_object',
        array( 
            "emailFilePath" => CPRESS_PLUGIN_DIR_URL ."public/email.php",
            "currentDomainUrl" => get_site_url(),
            "currency" => CPRESS_CURRENCY,
            )
        );

        wp_enqueue_script( 'cpress-modals', plugins_url( '/js/modals.js', __FILE__ ) ); 
        wp_localize_script( 'cpress-modals', 'messages_object',
        array( 
            "stripe" => __("Payment succeeded", "cpress"),
            "cash" => __("Thank you for your order ! A email has been send to the driver", "cpress"),
            )
        );

        wp_enqueue_script( 'cpress-processCash', plugins_url( '/js/processCash.js', __FILE__ ) ); 
        wp_localize_script( 'cpress-processCash', 'email_file_path_object',
            array( 
               'url' => CPRESS_PLUGIN_DIR_URL . "public/email.php",
               "currentDomainUrl" => get_site_url(),
            )
        );

} 

function cpress_gutenberg_block () {
        // Register our block editor script.
	wp_register_script(
		'block',
		plugins_url( 'js/block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' )
	);

	// Register our block, and explicitly define the attributes we accept.
	register_block_type( 'js/block', array(
		'attributes'      => array(
			'foo' => array(
				'type' => 'string',
			),
		),
		'editor_script'   => 'block', // The script name we gave in the wp_register_script() call.
		'render_callback' => 'cpress_show_booking_form',
	) );

}
add_action('init', 'cpress_gutenberg_block');



load_theme_textdomain( 'cpress', CPRESS_PLUGIN_PATH . '/languages' );

