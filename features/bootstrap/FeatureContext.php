<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    protected $client = null;
    protected $results = null;
    protected $params = [];
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(array $parameters = [])
    {
        $this->params = $parameters;
        $this->client = new \Github\Client();
    }

    /**
     * @Given I am an authenticated user
     */
    public function iAmAnAuthenticatedUser()
    {
        $this->client->authenticate(
            $this->params['github_token'], null, Github\AuthMethod::ACCESS_TOKEN
        );
    }

   /**
     * @When I star my :arg1 repository
     */
    public function iStarMyRepository($arg1)
    {
        // Retrieving the login (username) of the current GitHub user
        $githubUser = $this->client->api('current_user')->show()['login'];

        // Starring the repository with the given repository name (arg1) for the current user
        $this->client->api('current_user')->starring()->star($githubUser, $arg1);
    }

    /**
     * @When I unstar my :arg1 repository
     */
    public function iUnstarMyRepository($arg1)
    {
        // Retrieving the login (username) of the current GitHub user
        $githubUser = $this->client->api('current_user')->show()['login'];

        // Unstarring the repository with the given repository name (arg1) for the current user
        $this->client->api('current_user')->starring()->unstar($githubUser, $arg1);
    }

    /**
     * @Then my :arg1 repository will list me as a stargazer
     */
    public function myRepositoryWillListMeAsAStargazer($arg1)
    {
        // Retrieving the login (username) of the current GitHub user
        $githubUser = $this->client->api('current_user')->show()['login'];

        // Checking if the current user is listed as a stargazer of the repository
        if (!$this->isAStargazer($githubUser, $arg1)) {
            throw new Exception("Expected current user to be a stargazer of the '$githubUser/$arg1' repository but they were not.");
        }
    }

    /**
     * @Then my :arg1 repository will not list me as a stargazer
     */
    public function myRepositoryWillNotListMeAsAStargazer($arg1)
    {
        // Retrieving the login (username) of the current GitHub user
        $githubUser = $this->client->api('current_user')->show()['login'];

        // Checking if the current user is not listed as a stargazer of the repository
        if ($this->isAStargazer($githubUser, $arg1)) {
            throw new Exception("Expected current user to not be a stargazer of the '$githubUser/$arg1' repository but they were.");
        }
    }

    protected function isAStargazer($user, $repo)
    {
        // Retrieve all stargazers for the specified repository
        $_stargazers = $this->client->api('repo')->stargazers()->all($user, $repo);

        // Extract login names of stargazers and create an associative array for easier lookup
        $stargazers = array_column($_stargazers, 'login', 'login');

        // Check if the current user is listed as a stargazer
        return isset($stargazers[$user]);
    }

}
