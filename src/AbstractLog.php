<?php

namespace Nilambar\Log_Command;

use WP_CLI;
use WP_CLI\Utils;
use WP_CLI_Command;

abstract class AbstractLog extends WP_CLI_Command {

	protected $log_file;

	protected $parser;

	public function __construct() {
		$file = untrailingslashit( WP_CONTENT_DIR ) . '/debug.log';

		$config_value = WP_CLI::runcommand(
			'config get WP_DEBUG_LOG',
			[
				'return'     => true,
				'launch'     => true,
				'exit_error' => false,
			]
		);

		$default_mode = ( '1' === $config_value );

		if ( ! $default_mode && strlen( $config_value ) > 0 ) {
			$file = $config_value;
		}

		$this->log_file = Utils\normalize_path( $file );
		$this->parser   = new LogParser( $this->log_file );
	}
}
