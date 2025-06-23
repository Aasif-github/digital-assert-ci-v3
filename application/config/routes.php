<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| For complete details, see: https://codeigniter.com/userguide3/general/routing.html
|
*/


// $route['client'] = 'client/index';
// $route['client/project/(:num)'] = 'client/project/$1';

// // Default route (Client Home)
// $route['default_controller'] = 'home/index';


$route['default_controller'] = 'client';
$route['client'] = 'client/index';
$route['client/project/(:num)'] = 'client/project/$1';
$route['admin'] = 'admin/index';
$route['admin/show'] = 'admin/show';
$route['admin/store'] = 'admin/store';
$route['admin/edit/(:num)'] = 'admin/edit/$1';
$route['admin/update/(:num)'] = 'admin/update/$1';
$route['admin/destroy/(:num)'] = 'admin/destroy/$1';
$route['admin/project/(:num)'] = 'admin/project/$1';



// // Upload limits route
// $route['upload-limits'] = 'home/upload_limits';

// // Client routes
// $route['projects/(:num)'] = 'home/show/$1';

// // Admin routes
// $route['admin/dashboard'] = 'admin/dashboard/index';
// $route['admin/add-project'] = 'admin/dashboard/show';
// $route['admin/projects'] = 'admin/dashboard/store'; // POST
// $route['admin/projects/(:num)/edit'] = 'admin/dashboard/edit/$1';
// $route['admin/projects/(:num)']['PUT'] = 'admin/dashboard/update/$1';
// $route['admin/projects/(:num)']['DELETE'] = 'admin/dashboard/destroy/$1';
// $route['admin/projects/(:num)/show'] = 'admin/dashboard/project/$1';

// $route['404_override'] = '';
// $route['translate_uri_dashes'] = FALSE;
