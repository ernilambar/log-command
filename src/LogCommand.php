<?php

namespace Nilambar\Log_Command;

use DateTime;
use Exception;
use WP_CLI;
use WP_CLI\Formatter;

class LogCommand extends AbstractLog {

	protected $obj_fields = [ 'log_date', 'date', 'time_ago', 'excerpt', 'description' ];

	/**
	 * Lists log entries.
	 *
	 * ## OPTIONS
	 *
	 * [--number=<number>]
	 * : Number of entries.
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
	 * ## EXAMPLES
	 *
	 *     # List entries.
	 *     $ wp log list --format=csv
	 *     date,excerpt
	 *     "09-Nov-2024 06:01:29 UTC","Automatic updates starting..."
	 *     "09-Nov-2024 06:01:31 UTC","Automatic updates complete."
	 *     ...
	 *
	 * @subcommand list
	 */
	public function list( $args, $assoc_args = [] ) {
		$default_fields = [ 'log_date', 'excerpt' ];

		if ( empty( $assoc_args['fields'] ) ) {
			$assoc_args['fields'] = $default_fields;
		}

		try {
			$entries = $this->parser->find( 10 );

			if ( ! empty( $entries ) ) {
				$items = $this->prepare_data( $entries, $assoc_args );

				$formatter = $this->get_formatter( $assoc_args );
				$formatter->display_items( $items );
			}
		} catch ( Exception $e ) {
			WP_CLI::warning( $e->getMessage() );
		}
	}

	protected function prepare_data( $entries ) {
		$output = [];

		foreach ( $entries as $entry ) {
			$exploded = explode( 'UTC]', $entry );

			$log_date         = $exploded[0] . 'UTC]';
			$item['log_date'] = trim( $log_date, '[] ' );

			$item['description'] = $exploded[1];

			$excerpt = $this->get_excerpt( wp_strip_all_tags( $item['description'] ), 100 );
			$excerpt = preg_replace( '/\s+/', ' ', $excerpt );

			$item['excerpt'] = $excerpt;

			$date_time_obj = new DateTime( $item['log_date'] );

			$item['date'] = $date_time_obj->format( 'j M Y' );

			$item['time_ago'] = $this->get_time_ago( strtotime( $item['log_date'] ) );

			$output[] = $item;
		}

		return $output;
	}

	protected function get_formatter( &$assoc_args ) {
		return new Formatter( $assoc_args, $this->obj_fields );
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
	 * ## EXAMPLES
	 *
	 *     # Get 2 recent entries.
	 *     $ wp log get 2
	 *     [09-Nov-2024 06:01:29 UTC] Automatic updates starting...
	 *     [09-Nov-2024 06:01:31 UTC] Automatic updates complete.
	 *
	 * @subcommand get
	 */
	public function get( $args, $assoc_args = [] ) {
		$all = WP_CLI\Utils\get_flag_value( $assoc_args, 'all', false );

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
			$entries = $this->parser->find( $number );

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
	 */
	public function count( $args, $assoc_args = [] ) {
		try {
			WP_CLI::line( $this->parser->count() );
		} catch ( Exception $e ) {
			WP_CLI::warning( $e->getMessage() );
		}
	}

	/**
	 * Returns excerpt from text.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text       Text.
	 * @param int    $max_length Max length.
	 * @param string $cut_off    Cut off string.
	 * @param bool   $keep_word  Whether to keep word.
	 * @return string Time ago string.
	 */
	private function get_excerpt( string $text, int $max_length = 40, string $cut_off = '...', bool $keep_word = false ): string {
		if ( strlen( $text ) <= $max_length ) {
			return $text;
		}

		if ( strlen( $text ) > $max_length ) {
			if ( $keep_word ) {
				$text = mb_substr( $text, 0, $max_length + 1 );

				$last_space = strrpos( $text, ' ' );

				if ( $last_space ) {
					$text  = mb_substr( $text, 0, $last_space );
					$text  = rtrim( $text );
					$text .= $cut_off;
				}
			} else {
				$text  = mb_substr( $text, 0, $max_length );
				$text  = rtrim( $text );
				$text .= $cut_off;
			}
		}

		return $text;
	}
	/**
	 * Returns time ago string from time.
	 *
	 * @since 1.0.0
	 *
	 * @param int $time Time.
	 * @return string Time ago string.
	 */
	private function get_time_ago( int $time ): string {
		$output = '';

		$time_difference = time() - $time;

		if ( $time_difference < 1 ) {
			$output = 'less than 1 second ago';
		} else {
			$condition = [
				12 * 30 * 24 * 60 * 60 => 'year',
				30 * 24 * 60 * 60      => 'month',
				24 * 60 * 60           => 'day',
				60 * 60                => 'hour',
				60                     => 'minute',
				1                      => 'second',
			];

			foreach ( $condition as $secs => $str ) {
				$d = $time_difference / $secs;

				if ( $d >= 1 ) {
					$t = round( $d );

					$output = $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
					break;
				}
			}
		}

		return $output;
	}
}
