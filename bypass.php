<?php
header( "Access-Control-Allow-Origin: *" );

if ( isset( $_REQUEST['request'] ) && ! empty( $_REQUEST['request'] ) ) {
	$ch           = curl_init();
	$option_array = array(
		CURLOPT_URL            => urldecode( str_rot13( $_REQUEST['request'] ) ),
		CURLOPT_RETURNTRANSFER => true,
	);
	curl_setopt_array( $ch, $option_array );
	echo $result = curl_exec( $ch );
	curl_close( $ch );
}