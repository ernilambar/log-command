<?php
declare(strict_types=1);

namespace Nilambar\Log_Command;

use Exception;

class LogParser {

	private $log_file;

	private $entries = [];

	public function __construct( string $log_file ) {
		if ( file_exists( $log_file ) ) {
			$this->log_file = $log_file;
		} else {
			throw new Exception( 'Log file does not exist.' );
		}

		$this->parse();
	}

	public function fetch( int $num, bool $chronological ): array {
		$all_entries = $chronological ? $this->entries : array_reverse( $this->entries );

		if ( -1 === $num ) {
			return $all_entries;
		} elseif ( 0 === $num ) {
			return [];
		}

		return array_slice( $all_entries, 0, $num );
	}

	public function count() {
		return count( $this->entries );
	}

	private function parse() {
		$this->entries = $this->extract_entries( file_get_contents( $this->log_file ) ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

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
