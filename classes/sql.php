<?php

function cpress_all_table() 
{

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
	
    $tableCars = $wpdb->prefix . 'cars';
    $tableOrders = $wpdb->prefix . 'orders';

    $sql = "CREATE TABLE " . $tableCars . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        brand tinytext NOT NULL,
        color tinytext NOT NULL,
        passagers_number int(2) NULL,
        description varchar(255) NULL,
        car_image varchar(255) NULL,
        price_km decimal(65,2),
        PRIMARY KEY  (id)
    )$charset_collate;";

    $sql .= "INSERT INTO " . $tableCars . "
    (brand, color, passagers_number, price_km)
    VALUES ('Ford', 'black', 4, 2.3);";

    $sql .= "CREATE TABLE " . $tableOrders . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        lastname tinytext NOT NULL,
        firstname tinytext NOT NULL,
        phone int(10) NOT NULL,
        mail varchar(255) NOT NULL,
        order_date DATETIME NOT NULL,
        order_time DATETIME NOT NULL,
        origin_input varchar(255) NOT NULL,
        destination_input varchar(255) NOT NULL,
        passenger int(2) NULL,
        luggage int(2) NULL,
        comment varchar(255) NULL,
        price int(5),
        PRIMARY KEY  (id)
    )$charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


function cpress_drop_all_table() 
{
    global $wpdb;
	
    $tableCars = $wpdb->prefix . 'cars';
    $tableOrders = $wpdb->prefix . 'orders';

    $sql = "DROP TABLE " . $tableCars . ";";
    $sql .= "DROP TABLE " . $tableOrders . ";";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

}