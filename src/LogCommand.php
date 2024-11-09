<?php

namespace Nilambar\Log_Command;

use Exception;
use WP_CLI;

class LogCommand extends AbstractLog {

	/**
	 * Get log entries.
	 *
	 * ## OPTIONS
	 *
	 * [<number>]
	 * : Number of entries.
	 *
	 * [--all]
	 * : If set, all entries are displayed.
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
			$parser = new LogParser( $this->log_file );

			$entries = $parser->find( $number );

			if ( ! empty( $entries ) ) {
				WP_CLI::line( implode( "\n", $entries ) );
			}
		} catch ( Exception $e ) {
			WP_CLI::warning( $e->getMessage() );
		}
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
		WP_CLI::line( untrailingslashit( WP_CONTENT_DIR ) );
	}

	/**
	 * Clear debug log content.
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
	 * Delete debug log file.
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
	 * Get number of entries.
	 *
	 * @subcommand count
	 */
	public function count( $args, $assoc_args = [] ) {
		try {
			$parser = new LogParser( $this->log_file );

			WP_CLI::line( $parser->count() );
		} catch ( Exception $e ) {
			WP_CLI::warning( $e->getMessage() );
		}
	}
}
