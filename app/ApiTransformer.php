<?php

namespace App;

use Request;

class ApiTransformer
{

	public function endpoint($graph) {
		
		if(property_exists($graph, 'error')) {
			return response()->json($graph);
		}


		$path = Request::url();
		$next = $this->getNextPage($graph);

		$items = array_values( array_filter( array_map( function($item) {
			if( property_exists($item, 'surname') && $item->surname) {
				return $this->userTransform($item);
			}
		}, $graph->value ), function($item) {
			return $item;
		} ) );

		$endpoint = [
			'current' => $path,
			'data' =>  $items,
			'next' => $next
		];

		return response()->json( $endpoint );
	}

	public function endpointWithPagination($graph) {

		$endpoint = [
			'current' => $path,
			'data' =>  $items,
			'next' => $next
		];

		return response()->json( $endpoint );
	}

	public function endpointItem($item) {
		$endpoint = [
			'data' => $this->userTransform($item)
		];

		return response()->json( $endpoint );
	}

	public function userTransform($item) {
		return [
			'first' 	=> $item->givenName,
			'last' 		=> $item->surname,
			'username' 	=> $item->mailNickname,
			'email' 	=> $item->userPrincipalName,
			'department' => $item->department,
			'title' 	=> $item->jobTitle,
			'location' 	=> $item->physicalDeliveryOfficeName . ', ' . $item->state
		];
	}

	public function getNextPage($graph) {
		preg_match('/\'([^\"]*?)\'/', $graph->{'odata.nextLink'}, $skiplink);

		return $this->getCurrentEndpoint() . '/s/' . $skiplink[1];
	}

	public function getCurrentEndpoint() {
		$paths = explode('/', $_SERVER['REQUEST_URI']);

		return url() . '/' . $paths[1] . '/' . $paths[2];
	}
}
