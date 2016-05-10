<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GraphApi;
use App\ApiTransformer as Transform;

class GraphController extends Controller
{
	protected $graph;
	protected $transform;

	public function __construct(GraphApi $graph, Transform $transform)
    {
        $this->graph = $graph;
        $this->transform = $transform;
    }

    public function login() {
    	return redirect('http://login.microsoftonline.com/' . $this->graph->tenant . '/oauth2/authorize?client_id=' . $this->graph->client . '&response_type=code' . '&redirect_uri=' . route('token'));
    }

    public function token(Request $request) {
    	return $this->graph->authorize( $request->code, $request->session_state );
    }

	public function endpoint($endpoint) {
		return $this->transform->endpoint( $this->graph->getEndpoint($endpoint) );
		// return response()->json( $this->graph->getEndpoint($endpoint) );
	}
    
    public function endpointWithItem($endpoint, $item) {
    	return $this->transform->endpointItem( $this->graph->getEndpointWithItem($endpoint, $item) );
    	// return response()->json( $this->graph->getEndpointWithItem($endpoint, $item) );
	}

	public function endpointWithPagination($endpoint, $skiptoken) {
		return $this->transform->endpoint( $this->graph->getEndpointWithPagination($endpoint, $skiptoken) );
		// return response()->json( $this->graph->getEndpointWithPagination($endpoint, $skiptoken));
	}

}
