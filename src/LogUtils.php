<?php
/**
 * Class Nilambar\Log_Command\LogUtils
 *
 * @package log-command
 */

declare(strict_types=1);

namespace Nilambar\Log_Command;

/**
 * Log Utils class.
 */
class LogUtils {

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
	public static function get_excerpt( string $text, int $max_length = 40, string $cut_off = '...', bool $keep_word = false ): string {
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
	 * @param int $time Timestamp.
	 * @return string Time ago string.
	 */
	public static function get_time_ago( int $time ): string {
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
