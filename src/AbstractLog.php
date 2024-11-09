<?php

namespace Nilambar\Log_Command;

use WP_CLI\Utils;
use WP_CLI_Command;

abstract class AbstractLog extends WP_CLI_Command {

	protected $log_file;

	public function __construct() {
		$file = untrailingslashit( WP_CONTENT_DIR ) . '/debug.log';

		$this->log_file = Utils\normalize_path( $file );
	}
}
