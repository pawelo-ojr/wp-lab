<?php
/**
 * Plugin Name: Mailpit SMTP (tylko lokalnie)
 * Description: Kieruje wp_mail() do kontenera Mailpit (SMTP 1025, bez TLS i bez logowania).
 *
 * Obraz „wordpress” nie ma programu sendmail, więc bez tej wtyczki wp_mail() zwraca false.
 * Pliki z katalogu mu-plugins ładują się automatycznie — nie trzeba ich aktywować.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Wysyłka przez SMTP do usługi „mailpit” z compose.yaml (nazwa usługi = nazwa hosta w sieci Dockera).
add_action( 'phpmailer_init', function ( $phpmailer ) {
	$phpmailer->isSMTP();
	$phpmailer->Host     = 'mailpit';
	$phpmailer->Port     = 1025;
	$phpmailer->SMTPAuth = false;
} );

// Domyślny nadawca „wordpress@localhost” zostałby odrzucony jako niepoprawny adres.
add_filter( 'wp_mail_from', function () {
	return 'wordpress@wp-lab.test';
} );

add_filter( 'wp_mail_from_name', function () {
	return 'WP Lab';
} );

// Diagnostyka: przyczyna nieudanej wysyłki trafia do „docker compose logs wordpress”.
add_action( 'wp_mail_failed', function ( $error ) {
	error_log( 'wp_mail_failed: ' . $error->get_error_message() );
} );
