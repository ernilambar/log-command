<?php
/**
 * Class Nilambar\Log_Command\AbstractLog
 *
 * @package log-command
 */

namespace Nilambar\Log_Command;

use WP_CLI;
use WP_CLI\Formatter;
use WP_CLI\Utils;
use WP_CLI_Command;

/**
 * Abstract Log class.
 *
 * @since 1.0.0
 */
abstract class AbstractLog extends WP_CLI_Command {

	/**
	 * Log file.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $log_file;

	/**
	 * Parser object.
	 *
	 * @since 1.0.0
	 * @var Nilambar\Log_Command\LogParser
	 */
	protected $parser;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
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

	/**
	 * Returns Formatter object.
	 *
	 * @since 1.0.0
	 *
	 * @param array $assoc_args Associative arguments.
	 * @return WP_CLI\Formatter Formatter object.
	 */
	protected function get_formatter( &$assoc_args ) {
		return new Formatter( $assoc_args, $this->obj_fields );
	}
}
