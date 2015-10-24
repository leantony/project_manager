<?php

namespace app\Acme\Github;

use GrahamCampbell\GitHub\GitHubManager;

class GithubProject
{
    /**
     * @var GitHubManager
     */
    private $gitHubManager;

    /**
     * @var array
     */
    protected $repos;

    public function __construct(GitHubManager $gitHubManager){

        $this->gitHubManager = $gitHubManager;
    }

    public function getRepos($type = 'owner', $sort = 'full_name', $dir = 'asc'){

        $this->repos = $this->gitHubManager->me()->repositories($type, $sort, $dir);

        return $this->repos;
    }
}