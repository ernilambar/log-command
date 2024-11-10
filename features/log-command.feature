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

  Scenario: Check get command
    When I run `wp log get 1`
    Then STDOUT should be:
      """
      [10-Nov-2024 04:22:05 UTC] Five
      """

    When I run `wp log get 2`
    Then STDOUT should be:
      """
      [10-Nov-2024 04:22:05 UTC] Five
      [07-Nov-2024 06:01:31 UTC] Four
      """

    When I run `wp log get 1 --chronological`
    Then STDOUT should be:
      """
      [01-Nov-2024 11:53:35 UTC] One
      """

    When I run `wp log get 2 --chronological`
    Then STDOUT should be:
      """
      [01-Nov-2024 11:53:35 UTC] One
      [03-Nov-2024 11:53:36 UTC] Two
      """

    When I run `wp log get --all`
    Then STDOUT should be:
      """
      [10-Nov-2024 04:22:05 UTC] Five
      [07-Nov-2024 06:01:31 UTC] Four
      [05-Nov-2024 01:01:29 UTC] Three
      More text for third entry
      [03-Nov-2024 11:53:36 UTC] Two
      [01-Nov-2024 11:53:35 UTC] One
      """

    When I run `wp log get --all --chronological`
    Then STDOUT should be:
      """
      [01-Nov-2024 11:53:35 UTC] One
      [03-Nov-2024 11:53:36 UTC] Two
      [05-Nov-2024 01:01:29 UTC] Three
      More text for third entry
      [07-Nov-2024 06:01:31 UTC] Four
      [10-Nov-2024 04:22:05 UTC] Five
      """

  Scenario: Check list command
    When I run `wp log list --format=csv`
    Then STDOUT should contain:
      """
      "10-Nov-2024 04:22:05 UTC",Five
      "07-Nov-2024 06:01:31 UTC",Four
      """

    When I run `wp log list --format=csv --field=excerpt`
    Then STDOUT should be:
      """
      Five
      Four
      Three More text for third entry
      Two
      One
      """

    When I run `wp log list --format=csv --field=excerpt --chronological`
    Then STDOUT should be:
      """
      One
      Two
      Three More text for third entry
      Four
      Five
      """

    When I run `wp log list --format=csv --field=excerpt --per-page=2`
    Then STDOUT should be:
      """
      Five
      Four
      """

    When I run `wp log list --format=csv --field=excerpt --per-page=2 --page=2`
    Then STDOUT should be:
      """
      Three More text for third entry
      Two
      """

    When I run `wp log list --format=csv --field=excerpt --per-page=2 --page=3`
    Then STDOUT should be:
      """
      One
      """

    When I run `wp log list --format=csv --field=excerpt --per-page=2 --page=3 --chronological`
    Then STDOUT should be:
      """
      Five
      """
