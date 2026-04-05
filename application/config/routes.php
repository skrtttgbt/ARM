<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'MainController';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['login'] = 'MainController/login';
$route['logout'] = 'MainController/logout';
$route['forgot_password'] = 'MainController/forgot_password';
$route['reset_password'] = 'MainController/reset_password';

$route['dashboard'] = 'DashboardController/dashboard';
$route['dashboard/send_reminders'] = 'DashboardController/send_reminders';
$route['profile'] = 'ProfileController/index';
$route['settings'] = 'ProfileController/index';

$route['admin'] = 'AdminController/index';
$route['admin/list'] = 'AdminController/index';
$route['admin/create'] = 'AdminController/create';
$route['admin/archive'] = 'AdminController/archive';
$route['admin/action/reset/(:any)'] = 'AdminController/account_reset/$1';
$route['admin/action/suspend/(:any)'] = 'AdminController/account_suspend/$1';
$route['admin/action/activate/(:any)'] = 'AdminController/account_activate/$1';


$route['patient'] = 'PatientController/index';
$route['patient/list'] = 'PatientController/index';
$route['patient/create'] = 'PatientController/create';
$route['patient/archive'] = 'PatientController/archive';
$route['patient/history/(:any)'] = 'PatientController/history/$1';
$route['patient/profile/(:any)'] = 'PatientController/view/$1';
$route['patient/action/suspend/(:any)'] = 'PatientController/account_suspend/$1';
$route['patient/action/activate/(:any)'] = 'PatientController/account_activate/$1';


$route['vaccine'] = 'VaccineController/index';
$route['vaccine/list'] = 'VaccineController/index';
$route['vaccine/create'] = 'VaccineController/index';
$route['vaccine/archive'] = 'VaccineController/archive';
$route['vaccine/view/(:any)'] = 'VaccineController/view/$1';
$route['vaccine/analyze/(:any)'] = 'VaccineController/analyze/$1';
$route['vaccine/action/remove/(:any)'] = 'VaccineController/remove/$1';
$route['vaccine/action/archive/(:any)'] = 'VaccineController/archiveVaccine/$1';
$route['vaccine/action/add_quantity/(:any)'] = 'VaccineController/addQuantity/$1';
$route['vaccine/action/retreive/(:any)'] = 'VaccineController/retreive/$1';
$route['vaccine/action/retrieve/(:any)'] = 'VaccineController/retrieve/$1';


$route['vial'] = 'VialController/index';
$route['vial/create'] = 'VialController/create';
$route['vial/verify'] = 'VialController/verify';
$route['vial/barcode/(:any)'] = 'VialController/barcode/$1';
$route['vial/barcode/(:any)/download'] = 'VialController/barcodeDownload/$1';


$route['incident'] = 'IncidentController/index';
$route['incident/list'] = 'IncidentController/index';
$route['incident/create'] = 'IncidentController/create';
$route['incident/create/(:any)'] = 'IncidentController/create/$1';
$route['incident/create_schedule/(:any)'] = 'IncidentController/create_schedule/$1';
$route['incident/action/complete/(:any)'] = 'IncidentController/complete/$1';


$route['schedule'] = 'ScheduleController/index';
$route['schedule/list'] = 'ScheduleController/index';
$route['schedule/future'] = 'ScheduleController/future';
$route['schedule/proceed/(:any)'] = 'ScheduleController/proceed/$1';
$route['schedule/complete/(:any)'] = 'ScheduleController/complete/$1';
$route['schedule/barcode_details/(:any)'] = 'ScheduleController/barcodeDetails/$1';

$route['log/transaction'] = 'TransactionsController/index';

$route['test'] = 'TestController';
$route['test/db'] = 'TestController/test_db';

$route['dbtest'] = 'DatabaseTest';
