<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\User;
use App\GraphApi;
use App\ApiTransformer as Transform;
use App\Http\Controllers\GraphController;
use GuzzleHttp\Client as http;

class SlackController extends GraphController
{
	public function __construct(Request $request, GraphApi $graph, Transform $transform, http $http)
    {
    	$this->request = $request;
        $this->graph = $graph;
        $this->transform = $transform;
        $this->http = $http;
    }

	public function lookup() {
		$request = $this->request;

		if( $request->input('token', 'failtoken') === env('SLACK_LOOKUP_TOKEN') ) {

			
			$response = [
				'response_type' => 'in_channel',
				'text' => 'Testing!'
				'username' => 'Cardinal Solutions',
				'icon_emoji' => ':cardinal:'

			];


	        return response()->json($response);
		}

		return 'Slack Fail.';
	}

    
}
