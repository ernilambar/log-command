<?php

namespace Nilambar\Log_Command;

use WP_CLI;

if ( ! class_exists( 'WP_CLI', false ) ) {
	return;
}

$wpcli_log_autoload = __DIR__ . '/vendor/autoload.php';

if ( file_exists( $wpcli_log_autoload ) ) {
	require_once $wpcli_log_autoload;
}

WP_CLI::add_command( 'log', LogCommand::class );
