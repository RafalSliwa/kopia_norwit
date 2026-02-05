Feature: Hello World

  Scenario: Display hello world message
    Given I have a greeting
    When I say "Hello World"
    Then I should see "Hello World"