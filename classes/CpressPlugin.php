<?php

class Cpress_Plugin {

    // class instance
    static $instance;

    // method WP_List_Table object
    public $configs_obj;


    // class constructor
    public function __construct() {
        add_action( 'admin_init', [ $this, 'cpress_settings_init'] );
        add_action( 'admin_menu', [ $this, 'cpress_plugin_menu' ] );
    }

    /**
     * custom option and settings
     */
    public function cpress_settings_init() {
        // Register a new setting for "cpress" page.
        register_setting( 'cpress', 'cpress_options' );
        register_setting( 'cpress', 'cpress_api' );
        register_setting( 'cpress', 'cpress_stripe_api' );
        register_setting( 'cpress', 'cpress_stripe_public' );
        register_setting( 'cpress', 'cpress_city' );
        register_setting( 'cpress', 'cpress_country' );
        register_setting( 'cpress', 'cpress_currency' );
        register_setting( 'cpress', 'cpress_mail' );

        // Register a new section in the "cpress" page.
        add_settings_section(
            'cpress_section_developers',
            __( 'Cabpress plugin.', 'cpress' ), [$this,'cpress_section_developers_callback'],
            'cpress'
        );

        // Register a new field in the "cpress_section_developers" section, inside the "cpress" page.
        add_settings_field(
            'cpress_field_currency', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
                __( 'Choose a currency', 'cpress' ),
            [$this ,'cpress_field_currency'],
            'cpress',
            'cpress_section_developers',
        );

        // Register a new field in the "cpress_section_developers" section, inside the "cpress" page.
        add_settings_field(
            'cpress_field_api', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
                __( 'google_api_key', 'cpress' ),
            [$this ,'cpress_field_api'],
            'cpress',
            'cpress_section_developers',
        );
        // Stripe api key
        add_settings_field(
            'cpress_field_stripe', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
                __( 'stripe_secret_api_key', 'cpress' ),
            [$this ,'cpress_field_stripe'],
            'cpress',
            'cpress_section_developers',
        );
        // Stripe api public key
        add_settings_field(
            'cpress_field_stripe_public_key', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
                __( 'stripe_public_api_key', 'cpress' ),
            [$this ,'cpress_field_stripe_public'],
            'cpress',
            'cpress_section_developers',
        );        
        // City map
        add_settings_field(
            'cpress_field_city', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
                __( 'city_map', 'cpress' ),
            [$this ,'cpress_field_city'],
            'cpress',
            'cpress_section_developers',
        );
        // Country choice
        add_settings_field(
            'cpress_field_country', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
                __( 'country', 'cpress' ),
            [$this ,'cpress_field_country'],
            'cpress',
            'cpress_section_developers',
            array(
                'label_for'         => 'cpress_country',
                'class'             => 'cpress_row',
                'cpress_custom_data' => 'custom',
            )
        );
        // Email
        add_settings_field(
            'cpress_field_mail', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
                __( 'notification_email', 'cpress' ),
            [$this ,'cpress_field_email'],
            'cpress',
            'cpress_section_developers',
        );


    }


    /**
     * Developers section callback function.
     *
     * @param array $args  The settings array, defining title, id, callback.
     */
    public function cpress_section_developers_callback( $args ) {

            ?>
            <p style="font-size:18px" id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( "Add this shortcode to your page [cabpress-form]", 'cpress' ); ?></p>
            <?php
    }

    /**
     * Pill field callbakc function.
     *
     * WordPress has magic interaction with the following keys: label_for, class.
     * - the "label_for" key value is used for the "for" attribute of the <label>.
     * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
     * Note: you can add custom key value pairs to be used inside your callbacks.
     *
     * @param array $args
     */
    public function cpress_field_currency( $args ) {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option( 'cpress_currency' );
        ?>
        <select
                name="cpress_currency">
            <option value="eur" <?php echo isset( $options) ? ( selected( $options, 'eur', false ) ) : ( '' ); ?>>
                <?php esc_html_e( 'eur', 'cpress' ); ?>
            </option>
            <option value="usd" <?php echo isset( $options) ? ( selected( $options, 'usd', false ) ) : ( '' ); ?>>
                <?php esc_html_e( 'usd', 'cpress' ); ?>
            </option>
        </select>
        <?php
    }

    public function cpress_field_api( $args ) {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option( 'cpress_api' );
        ?>
        <p class="description">
            <?php esc_html_e( 'Put your Google API key and use google map', 'cpress' ); ?>
        </p>
        <input 
            type="text" 
            name="cpress_api"
            value="<?php echo isset( $options ) ? esc_attr( $options ) : ''; ?>"
        >
        <?php
    }

    public function cpress_field_stripe( $args ) {
        $stripeApi = get_option( 'cpress_stripe_api' );
        ?>
        <p class="description">
            <?php esc_html_e( 'Put your Stripe API key and get payments, PRO version only', 'cpress' ); ?>
        </p>
        <input 
            type="text" 
            name="cpress_stripe_api"
            value="<?php echo isset( $stripeApi ) ? esc_attr( $stripeApi ) : ''; ?>"
        disabled>
        <?php
    }

    public function cpress_field_stripe_public( $args ) {
        $stripePublic = get_option( 'cpress_stripe_public' );
        ?>
        <p class="description">
            <?php esc_html_e( 'Put your Stripe PUBLIC API key and get payments, PRO version only', 'cpress' ); ?>
        </p>
        <input 
            type="text" 
            name="cpress_stripe_public"
            value="<?php echo isset( $stripePublic ) ? esc_attr( $stripePublic ) : ''; ?>"
        disabled>
        <?php
    }

    public function cpress_field_city( $args ) {
        $city = get_option( 'cpress_city' );
        ?>
        <p class="description">
            <?php esc_html_e( 'Choose the city to point at with google map', 'cpress' ); ?>
        </p>
        <input 
            type="text" 
            name="cpress_city"
            value="<?php echo isset( $city ) ? esc_attr( $city ) : ''; ?>"
        >
        <?php
    }

    public function cpress_field_country( $args ) {
        $country = get_option( 'cpress_country' );
        ?>
        <p class="description">
            <?php esc_html_e( 'Choose the country that the map will display, us for USA', 'cpress' ); ?>
        </p>
        <select
                name="cpress_country[<?php echo esc_attr( $args['label_for'] ); ?>]">
            <option value="fr" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'fr', false ) ) : ( '' ); ?>>
                <?php esc_html_e( 'France', 'cpress' ); ?>
            </option>
            <option value="us" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'us', false ) ) : ( '' ); ?>>
                <?php esc_html_e( 'Etats unis', 'cpress' ); ?>
            </option>
        </select>
        <?php

    }

    public function cpress_field_email( $args ) {
        $email = get_option( 'cpress_mail' );
        ?>
        <p class="description">
            <?php esc_html_e( 'Email to receive you bookings', 'cpress' ); ?>
        </p>
        <input 
            type="text" 
            name="cpress_mail"
            value="<?php echo isset( $email ) ? esc_attr( $email ) : ''; ?>"
        >
        <?php
    }

    public function cpress_plugin_menu() {
        
        add_menu_page(
            'Settings',
            'Car booking',
            'manage_options',
            'cpress',
            [ $this, 'cpress_options_page_html'],
            'dashicons-businessperson',
            20
        );

        add_submenu_page(
            'cpress',
            'Cars listing',
            'Cars listing',
            'manage_options',
            'cars_listing',
            'cpress_cars_list_init',
            21
        );

        add_submenu_page(
            'cpress',
            __('Add a car', 'cpress'),
            __('Add a car', 'cpress'), 
            'manage_options',
            'cars_form',
            'cars_handler_add_form',
            22
        );

        add_submenu_page(
            'cpress',
            'Orders listing',
            'Orders listing',
            'manage_options',
            'orders_listing',
            'cpress_orders_list_init',
            22
        );


    }
    
    /**
     * Top level menu callback function
     */
    public function cpress_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// add error/update messages

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
		add_settings_error( 'cpress_messages', 'cpress_message', __( 'Settings Saved', 'cpress' ), 'updated' );
	}

	// show error/update messages
	settings_errors( 'cpress_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "cpress"
			settings_fields( 'cpress' );
			// output setting sections and their fields
			// (sections are registered for "cpress", each field is registered to a specific section)
			do_settings_sections( 'cpress' );
			// output save settings button
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
    }


    /** Singleton instance */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
}