Feature: Test log commands with custom log

  Background:
    Given a WP install
    And a custom-folder/debug-custom.log file:
      """
      [01-Nov-2024 11:53:35 UTC] One
      [03-Nov-2024 11:53:36 UTC] Two
      [05-Nov-2024 01:01:29 UTC] Three
      [07-Nov-2024 06:01:31 UTC] Four
      """
    And I run `wp config set WP_DEBUG true --raw`
    And I run `wp config set WP_DEBUG_LOG {RUN_DIR}/custom-folder/debug-custom.log`

  Scenario: Check basic commands for custom log file
    When I run `wp log file`
    Then STDOUT should contain:
      """
      {RUN_DIR}/custom-folder/debug-custom.log
      """

    When I run `wp log count`
    Then STDOUT should be:
      """
      4
      """
