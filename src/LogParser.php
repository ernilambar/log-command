<?php
declare(strict_types=1);

namespace Nilambar\Log_Command;

use Exception;

class LogParser {

	private string $log_file;

	private array $entries = [];

	public function __construct( string $log_file ) {
		if ( file_exists( $log_file ) ) {
			$this->log_file = $log_file;
		} else {
			throw new Exception( 'Log file does not exist.' );
		}

		$this->parse();
	}

	public function find( int $num ) {
		if ( -1 === $num ) {
			return $this->entries;
		}

		$array_length = count( $this->entries );

		$start_index = max( 0, $array_length - $num );

		return array_slice( $this->entries, $start_index );
	}

	public function count() {
		return count( $this->entries );
	}

	private function parse() {
		$this->entries = $this->extract_entries( file_get_contents( $this->log_file ) );
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
