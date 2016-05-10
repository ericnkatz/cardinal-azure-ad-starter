<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\GraphApi;
use App\ApiTransformer as Transform;
use App\Http\Controllers\GraphController;

class PagesController extends GraphController
{
	public function __construct(GraphApi $graph, Transform $transform)
    {
        $this->graph = $graph;
        $this->transform = $transform;
    }

	public function index() {
		return view('index');
	}

	public function login() {
		return view('login');
	}

	public function getProfile() {

		return view('profile');
	}

	public function updateProfile() {
		$user = Auth::user();

		$graph = $this->transform->endpointItem( $this->graph->getEndpointWithItem('users', $user->email) );
		$graph = json_decode($graph->getContent(), true);

		$update = [
          	'department' => $graph['data']['department'],
			'title' => $graph['data']['title'],
			'location' => $graph['data']['location']
			];

		User::where('email', $user->email)
          ->update($update);

        return redirect()->route('profile');
	}
    
}
