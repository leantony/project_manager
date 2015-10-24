<?php

namespace App\Http\Controllers\projects;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\models\Projects;
use App\models\UserProjects;
use GrahamCampbell\GitHub\GitHubManager;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{

    /**
     * @var GitHubManager
     */
    private $github;

    public function __construct(GitHubManager $github)
    {

        $this->github = $github;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->github->me()->repositories();

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

            $collaborators = $this->github->getHttpClient()->get("/repos/{$p_owner}/{$p_name}/collaborators")->json();

            foreach ($collaborators as $project_user) {

                UserProjects::create([
                    'project_id' => $project->id,
                    'email' => array_get($project_user, 'email'),
                    'username' => array_get($project_user, 'login')
                ]);
            }

        }

        $projects = Projects::all();

        return view('projects.view', compact('projects'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->github->me()->repositories();

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
