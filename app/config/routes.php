<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Root Route
$router->get('/', 'Welcome@index');

$router->match('/auth/login', 'AuthController@login', ['GET','POST']);
$router->match('/auth/register', 'AuthController@register', ['GET','POST']);
$router->get('/auth/logout', 'AuthController@logout');
$router->get('/auth/dashboard', 'DashboardController@index');

$router->get('/student/dashboard', 'DashboardController@student');
$router->get('/counselor/dashboard', 'DashboardController@counselor');

$router->get('/appointments', 'AppointmentController@index');
$router->match('/appointments/create', 'AppointmentController@create', ['GET','POST']);
$router->match('/appointments/edit/{id}', 'AppointmentController@edit', ['GET','POST']);
$router->get('/appointments/delete/{id}', 'AppointmentController@delete');
$router->get('/appointments/upcoming', 'AppointmentController@upcoming');

$router->get('/profile', 'ProfileController@index');
$router->match('/profile/edit', 'ProfileController@edit', ['GET','POST']);
$router->match('/profile/change_password', 'ProfileController@change_password', ['GET','POST']);
?>
