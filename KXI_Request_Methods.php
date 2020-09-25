<?php

interface Request_Methods 	{

    public function get($callback, $middleware = null);
	public function post($callback, $middleware = null);
	public function delete($callback, $middleware = null);
	public function put($callback, $middleware = null);

};