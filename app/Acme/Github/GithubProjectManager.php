<?php

namespace app\Acme\Github;

use App\models\Projects;
use GrahamCampbell\GitHub\GitHubManager;

class GithubProjectManager
{
    /**
     * @var GitHubManager
     */
    private $gitHubManager;

    /**
     * @var array
     */
    protected $repos;
    /**
     * @var Projects
     */
    private $projects;

    public function __construct(GitHubManager $gitHubManager, Projects $projects){

        $this->gitHubManager = $gitHubManager;
        $this->projects = $projects;
    }

    public function getAllProjects(){
        return $this->projects->paginate(10);
    }

    public function getForCurrentUser(){

    }
    public function getRepos($type = 'owner', $sort = 'full_name', $dir = 'asc'){

        $this->repos = $this->gitHubManager->me()->repositories($type, $sort, $dir);

        return $this->repos;
    }

    public function initialize(){
        $data = $this->gitHubManager->me()->repositories();

        foreach ($data as $value) {
            $p_name = array_get($value, 'name');
            $p_slug = array_get($value, 'full_name');
            $p_desc = array_get($value, 'description');
            $p_owner = array_get(array_get($value, 'owner'), 'login');

            $project = Projects::create([
                'name' => $p_name,
                'slug' => $p_slug,
                'description' => $p_desc
            ]);

            $collaborators = $this->gitHubManager->getHttpClient()->get("/repos/{$p_owner}/{$p_name}/collaborators")->json();

            foreach ($collaborators as $project_user) {

                UserProjects::create([
                    'project_id' => $project->id,
                    'email' => array_get($project_user, 'email'),
                    'username' => array_get($project_user, 'login')
                ]);
            }

        }

        $projects = Projects::all();
    }
}