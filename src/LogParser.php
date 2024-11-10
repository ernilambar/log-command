<?php
/**
 * Class Nilambar\Log_Command\LogParser
 *
 * @package log-command
 */

declare(strict_types=1);

namespace Nilambar\Log_Command;

use Exception;

/**
 * Log Parser class.
 *
 * @since 1.0.0
 */
class LogParser {

	/**
	 * Log file.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $log_file;

	/**
	 * Log entries.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $entries = [];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $log_file Log file.
	 *
	 * @throws Exception Throws exception.
	 */
	public function __construct( string $log_file ) {
		if ( file_exists( $log_file ) ) {
			$this->log_file = $log_file;
		} else {
			throw new Exception( 'Log file does not exist.' );
		}

		$this->parse();
	}

	/**
	 * Returns entries.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $number        Number of entries to fetch.
	 * @param bool $chronological Whether order should be chronological or not.
	 * @return array Array of log entries.
	 */
	public function fetch( int $number, bool $chronological ): array {
		$all_entries = $chronological ? $this->entries : array_reverse( $this->entries );

		if ( -1 === $number ) {
			return $all_entries;
		} elseif ( 0 === $number ) {
			return [];
		}

		return array_slice( $all_entries, 0, $number );
	}

	/**
	 * Returns entries count.
	 *
	 * @since 1.0.0
	 *
	 * @return int Entries count.
	 */
	public function count(): int {
		return count( $this->entries );
	}

	/**
	 * Parses log file.
	 *
	 * @since 1.0.0
	 */
	private function parse() {
		$this->entries = $this->extract_entries( file_get_contents( $this->log_file ) ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

	/**
	 * Extracts log entries from file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $log_content Content of log file.
	 * @return array Array of log entries.
	 */
	protected function extract_entries( string $log_content ): array {
		$log_entries   = [];
		$current_entry = '';

		$date_pattern = '/\[[0-9]{2}-[A-Za-z]{3}-[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2} UTC]/';

		foreach ( explode( "\n", $log_content ) as $line ) {
			if ( preg_match( $date_pattern, $line ) ) {
				// Start of a new log entry.
				if ( '' !== $current_entry ) {
					$log_entries[] = trim( $current_entry );
				}
				$current_entry = $line; // Start a new entry.
			} elseif ( '' !== $current_entry ) {
				$current_entry .= "\n" . $line;
			}
		}

		// Add the last log entry (if any).
		if ( '' !== $current_entry ) {
			$log_entries[] = trim( $current_entry );
		}

		return $log_entries;
	}
}
