Feature: Test log commands

  Background:
    Given a WP install
    And a wp-content/debug.log file:
      """
      [01-Nov-2024 11:53:35 UTC] One
      [03-Nov-2024 11:53:36 UTC] Two
      [05-Nov-2024 01:01:29 UTC] Three
      More text for third entry
      [07-Nov-2024 06:01:31 UTC] Four
      [10-Nov-2024 04:22:05 UTC] Five
      """

  Scenario: Check basic commands
    When I run `wp log count`
    Then STDOUT should be:
      """
      5
      """

    When I run `wp log file`
    Then STDOUT should contain:
      """
      {RUN_DIR}/wp-content/debug.log
      """

    When I run `wp log path`
    Then STDOUT should contain:
      """
      {RUN_DIR}/wp-content
      """
    And STDOUT should not contain:
      """
      debug.log
      """

  Scenario: Check delete command
    When I run `wp log delete`
    Then STDOUT should contain:
      """
      Debug log file deleted successfully.
      """
    And the {RUN_DIR}/wp-content/debug.log file should not exist

  Scenario: Check clear command
    When I run `wp log clear`
    Then STDOUT should contain:
      """
      Debug log content cleared successfully.
      """
    And the {RUN_DIR}/wp-content/debug.log file should exist
    And the {RUN_DIR}/wp-content/debug.log file should be:
      """
      """
