<?php

	require_once __DIR__."/../KX_Router.php";
	$App = new KX_Router;

	
	$App->get(function ($res){
		echo json_encode($res);
	});

	$App->post(function ($res){
		echo json_encode($res);
	});

	$App->put(function ($res){
		echo json_encode($res);
	});

	$App->delete(function ($res){
		echo json_encode($res);
	});

	$App->run();