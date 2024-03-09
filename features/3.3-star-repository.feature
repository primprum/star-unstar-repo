Feature: Star and Unstar a repository

  Scenario: I want to star an important repository
    Given I am an authenticated user
    When I star my "get-issues" repository
    Then my "get-issues" repository will list me as a stargazer

  Scenario: I want to unstar an important repository
    Given I am an authenticated user
    When I unstar my "get-issues" repository
    Then my "get-issues" repository will not list me as a stargazer
