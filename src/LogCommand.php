<?php

namespace Nilambar\Log_Command;

use WP_CLI;
use WP_CLI_Command;

class LogCommand extends WP_CLI_Command {

	/**
	 * Gets the path to the log file.
	 *
	 * ## EXAMPLES
	 *
	 *     # Get path.
	 *     $ wp log path
	 *     /Users/johndoe/Sites/staging/app/public/wp-content
	 *
	 * @subcommand path
	 */
	public function path( $args, $assoc_args = [] ) {
		$path = untrailingslashit( WP_CONTENT_DIR );
		WP_CLI::line( $path );
	}
}
