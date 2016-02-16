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
		if($request->token === env('SLACK_LOOKUP_TOKEN')) {
			$response_url = $request->input('response_url');
			
			$response = [
				'response_type' => 'in_channel',
				'text' => 'Testing!'
			];

			$this->http->post($response_url, [
	            'json' => json_encode($response)
	        ]);

	        return 'Slack Lookup.';
		}
	}

    
}
