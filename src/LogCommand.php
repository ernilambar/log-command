<?php
/**
 * Class Nilambar\Log_Command\LogCommand
 *
 * @package log-command
 */

namespace Nilambar\Log_Command;

use DateTime;
use Exception;
use WP_CLI;

/**
 * Log command class.
 *
 * @since 1.0.0
 */
class LogCommand extends AbstractLog {

	/**
	 * Fields.
	 *
	 * @since 1.0.0
	 * @var string[]
	 */
	protected $obj_fields = [ 'log_date', 'date', 'time_ago', 'excerpt', 'description' ];

	/**
	 * Lists log entries.
	 *
	 * ## OPTIONS
	 *
	 * [--field=<field>]
	 * : Returns the value of a single field.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * [--page=<page>]
	 * : Page to display. Defaults to 1.
	 * ---
	 * default: 1
	 * ---
	 *
	 * [--per-page=<per-page>]
	 * : Number of entries to display. Defaults to 10.
	 * ---
	 * default: 10
	 * ---
	 *
	 * [--chronological]
	 * : If set, chronological order is used.
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each entry:
	 *
	 * * log_date
	 * * excerpt
	 *
	 * These fields are optionally available:
	 *
	 * * date
	 * * time_ago
	 * * description
	 *
	 * ## EXAMPLES
	 *
	 *     # List entries.
	 *     $ wp log list --format=csv
	 *     date,excerpt
	 *     "09-Nov-2024 06:01:31 UTC","Automatic updates complete."
	 *     "09-Nov-2024 06:01:29 UTC","Automatic updates starting..."
	 *     ...
	 *
	 * @subcommand list
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 */
	public function list_( $args, $assoc_args = [] ) {
		$default_fields = [ 'log_date', 'excerpt' ];

		if ( empty( $assoc_args['fields'] ) ) {
			$assoc_args['fields'] = $default_fields;
		}

		$chronological = WP_CLI\Utils\get_flag_value( $assoc_args, 'chronological', false );

		try {
			$entries = $this->parser->fetch( (int) $assoc_args['per-page'], (int) $assoc_args['page'], $chronological );

			if ( ! empty( $entries ) ) {
				$items = $this->prepare_data( $entries, $assoc_args );

				$formatter = $this->get_formatter( $assoc_args );
				$formatter->display_items( $items );
			}
		} catch ( Exception $e ) {
			WP_CLI::warning( $e->getMessage() );
		}
	}

	/**
	 * Gets log entries.
	 *
	 * ## OPTIONS
	 *
	 * [<number>]
	 * : Number of entries.
	 *
	 * [--all]
	 * : If set, all entries are displayed.
	 *
	 * [--chronological]
	 * : If set, chronological order is used.
	 *
	 * ## EXAMPLES
	 *
	 *     # Get 2 recent entries.
	 *     $ wp log get 2
	 *     [09-Nov-2024 06:01:31 UTC] Automatic updates complete.
	 *     [09-Nov-2024 06:01:29 UTC] Automatic updates starting...
	 *
	 * @subcommand get
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 */
	public function get( $args, $assoc_args = [] ) {
		$all           = WP_CLI\Utils\get_flag_value( $assoc_args, 'all', false );
		$chronological = WP_CLI\Utils\get_flag_value( $assoc_args, 'chronological', false );

		if ( empty( $args ) ) {
			if ( ! $all ) {
				WP_CLI::error( 'Please specify number, or use --all.' );
			} else {
				$number = -1;
			}
		} else {
			$number = intval( $args[0] );
		}

		try {
			$entries = $this->parser->fetch( $number, 1, $chronological );

			if ( ! empty( $entries ) ) {
				WP_CLI::line( implode( "\n", $entries ) );
			}
		} catch ( Exception $e ) {
			WP_CLI::warning( $e->getMessage() );
		}
	}

	/**
	 * Gets the log file.
	 *
	 * ## EXAMPLES
	 *
	 *     # Get log file.
	 *     $ wp log file
	 *     /Users/johndoe/Sites/staging/app/public/wp-content/debug.log
	 *
	 * @subcommand file
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 */
	public function file( $args, $assoc_args = [] ) {
		WP_CLI::line( $this->log_file );
	}

	/**
	 * Gets the path to the debug log file.
	 *
	 * ## EXAMPLES
	 *
	 *     # Get path.
	 *     $ wp log path
	 *     /Users/johndoe/Sites/staging/app/public/wp-content
	 *
	 * @subcommand path
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 */
	public function path( $args, $assoc_args = [] ) {
		WP_CLI::line( dirname( $this->log_file ) );
	}

	/**
	 * Clears debug log content.
	 *
	 * ## EXAMPLES
	 *
	 *     # Clear log.
	 *     $ wp log clear
	 *     Success: Debug log content cleared successfully.
	 *
	 * @subcommand clear
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 */
	public function clear( $args, $assoc_args = [] ) {
		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( ! $wp_filesystem->exists( $this->log_file ) ) {
			WP_CLI::warning( 'Debug log file does not exist.' );
			return;
		}

		if ( false === $wp_filesystem->put_contents( $this->log_file, '', FS_CHMOD_FILE ) ) {
			WP_CLI::error( 'Error clearing debug log content.' );
		}

		WP_CLI::success( 'Debug log content cleared successfully.' );
	}

	/**
	 * Deletes debug log file.
	 *
	 * ## EXAMPLES
	 *
	 *     # Delete log file.
	 *     $ wp log delete
	 *     Success: Debug log file deleted successfully.
	 *
	 * @subcommand delete
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 */
	public function delete( $args, $assoc_args = [] ) {
		if ( file_exists( $this->log_file ) ) {
			wp_delete_file( $this->log_file );
		}

		WP_CLI::success( 'Debug log file deleted successfully.' );
	}

	/**
	 * Gets number of entries.
	 *
	 * ## EXAMPLES
	 *
	 *     # Get log entries count.
	 *     $ wp log count
	 *     4
	 *
	 * @subcommand count
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 */
	public function count( $args, $assoc_args = [] ) {
		try {
			WP_CLI::line( $this->parser->count() );
		} catch ( Exception $e ) {
			WP_CLI::warning( $e->getMessage() );
		}
	}

	/**
	 * Returns prepared data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $entries Log entries.
	 * @return array Modified results.
	 */
	protected function prepare_data( $entries ) {
		$output = [];

		foreach ( $entries as $entry ) {
			$exploded = explode( 'UTC]', $entry );

			$log_date = $exploded[0] . 'UTC]';

			$item['log_date'] = trim( $log_date, '[] ' );

			$item['description'] = $exploded[1];

			$excerpt = LogUtils::get_excerpt( wp_strip_all_tags( $item['description'] ), 100 );
			$excerpt = preg_replace( '/\s+/', ' ', $excerpt );

			$item['excerpt'] = $excerpt;

			$date_time_obj = new DateTime( $item['log_date'] );

			$item['date'] = $date_time_obj->format( 'j M Y' );

			$item['time_ago'] = LogUtils::get_time_ago( strtotime( $item['log_date'] ) );

			$output[] = $item;
		}

		return $output;
	}
}
