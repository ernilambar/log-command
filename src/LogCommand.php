<?php

namespace Nilambar\Log_Command;

use Exception;
use WP_CLI;
use WP_CLI\Utils;
use WP_CLI_Command;

class LogCommand extends WP_CLI_Command {

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

		$file = untrailingslashit( WP_CONTENT_DIR ) . '/debug.log';

		$file_path = Utils\normalize_path( $file );

		try {
			$parser = new LogParser( $file_path );

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

		$file = untrailingslashit( WP_CONTENT_DIR ) . '/debug.log';

		$file_path = Utils\normalize_path( $file );

		if ( ! $wp_filesystem->exists( $file_path ) ) {
			WP_CLI::warning( 'Debug log file does not exist.' );
			return;
		}

		if ( false === $wp_filesystem->put_contents( $file_path, '', FS_CHMOD_FILE ) ) {
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
		$file = untrailingslashit( WP_CONTENT_DIR ) . '/debug.log';

		$file_path = Utils\normalize_path( $file );

		if ( file_exists( $file_path ) ) {
			wp_delete_file( $file_path );
		}

		WP_CLI::success( 'Debug log file deleted successfully.' );
	}

	/**
	 * Get number of entries.
	 *
	 * @subcommand count
	 */
	public function count( $args, $assoc_args = [] ) {
		$file = untrailingslashit( WP_CONTENT_DIR ) . '/debug.log';

		$file_path = Utils\normalize_path( $file );

		try {
			$parser = new LogParser( $file_path );

			WP_CLI::line( $parser->count() );
		} catch ( Exception $e ) {
			WP_CLI::warning( $e->getMessage() );
		}
	}
}
