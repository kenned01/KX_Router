# KX_Router
KX_Router is a php component that allows you to implement servers-request methods suchs as `GET`,`POST`, `PUT`, `DELETE`.

View repository 

## Create an Instance

```php
	require_once "path-to-KX_Router/KX_Router.php";
	$App = new KX_Router;
```
## Get method

The get method accepts a function as a parameter, this function will be executed only is the server request is GET.

```php
	$App->get( function ($res) {
		//Your code goes here
	} );
```

## Post method

The Post method accepts a function as a parameter, this function will be executed only is the server request is POST.
> this method accepts form-data or json-data for its body

```php
	$App->post( function ($res) {
		//Your code goes here
	} );
```
## Put method

The put method accepts a function as a parameter, this function will be executed only is the server request is PUT.

> this method only accepts json-data for its body

```php
	$App->put( function ($res) {
		//Your code goes here
	} );
```

## Delete method

The delete method accepts a function as a parameter, this function will be executed only is the server request is DELETE.
> this method only accepts json-data for its body

```php
	$App->delete( function ($res) {
		//Your code goes here
	} );
```

## Run APP

to run this application you must call a funtion to do so, and it must be at the end of the file

```php
$App->run();
```

### Reading sent data
you can read the data you sent to the whichever of those methods and to do so the data would be in the parameter of you function

```php 
	$app->post( function($res){
		// Your data is in the $res parameter
		// let's say you sent a form-data with name 
		//as the key and "your name" as value to read it
		
		echo $res["name"]; 
	});
```