<?php

/* 
 * Core Functions
 * This file handles the simple, central PHP elements. 
 * 
 * @developers Complex or one-off functions are probably best put elsewhere. 
 */

function logged_in() {
    return isset($_SESSION['loggedIn']) AND $_SESSION['loggedIn'] === true;
}

function f_data_list($conn, $table, $primary_key, $label, $detail_url = "#", $get_var = "row_id", $order_by = false, $where = "") {
    if($order_by === false AND is_array($label)) {
        $order_by = $label[0] . " ASC ";
    } else if ($order_by === false) {
        $order_by = $label . " ASC ";
    }
    if(is_array($label)) {
        $fields = " $primary_key, ";
        foreach($label as $str) {
            $fields .= "$str, ";
        }
        $fields = substr($fields, 0, -2);
    } else {
        $fields = " $primary_key, $label ";
    }
    $results = db_query($conn, "SELECT $fields FROM $table $where ORDER BY $order_by");
    $string = "<ul>";
    if($detail_url === "#") {
        $detail_url = "";
    }
    foreach ($results as $item) {
        $string .= "<li><a href='$detail_url?$get_var=".$item[$primary_key]."'>";
        if(is_array($label)) {
            foreach($label as $str) {
                $string .= $item[$str].", ";
            }
            $string = substr($string, 0, -2);
        } else {
            $string .= $item[$label];
        }
        $string .= "</a></li>\n";
    }
    $string .= "</ul>";
    return $string;
}

function imree_file($file_id, $size=false) {
	$conn = db_connect();
	$results = db_query($conn, "SELECT * FROM asset_data WHERE asset_data_id = ".db_escape($file_id, $conn));
	
	//file not found
	if(!$results) {
		header("HTTP/1.0 404 Not Found");
		die();
	} 

	
	header('Content-type: '.$results[0]['asset_data_type']);
	header('Content-Disposition: inline; filename="'.addslashes($results[0]['asset_data_name']).'"');
	if($size AND is_numeric($size)) {
		echo imree_resize_image($results[0]['asset_data_contents'], $size);
	} else {
		if($results[0]['asset_data_size'] > 0) {
			header("Content-Length: " .$results[0]['asset_data_size']);
		}
		echo $results[0]['asset_data_contents'];
	}
}

function imree_resize_image($image,$new_height){ 
    $im = new Imagick();
    $im->readimageblob($image);
    $im->scaleImage(0, $new_height);
    $str = $im->getimageblob();
    
    $im->destroy();
    return $str;
}