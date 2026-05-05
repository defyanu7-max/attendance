<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AkademiAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::controller(AkademiAdminController::class)->group(function () {
    Route::get('/', 'dashboard');
    Route::get('/index', 'dashboard')->name('dashboard');
    Route::get('/index-2','dashboard_2')->name('dashboard_2');
    Route::get('/finance','finance')->name('finance');
	Route::get('/student', 'student')->name('student');
    Route::get('/student-detail', 'student_detail')->name('student_detail');
	Route::get('/add-student', 'add_student')->name('add_student');
	Route::get('/teacher', 'teacher')->name('teacher');
	Route::get('/teacher-detail', 'teacher_detail')->name('teacher_detail');
	Route::get('/add-teacher', 'add_teacher')->name('add_teacher');
    Route::get('/food', 'food')->name('food');
    Route::get('/food-details', 'food_details')->name('food_details');
    Route::get('/file-manager', 'file_manager')->name('file_manager');
    Route::get('/user', 'user')->name('user');
    Route::get('/celandar', 'celandar')->name('celandar');
    Route::get('/chat', 'chat')->name('chat');
    Route::get('/app-calender', 'app_calender')->name('app_calender');
    Route::get('/app-profile', 'app_profile')->name('app_profile');
    Route::get('/activity', 'activity')->name('chaactivityt');
    Route::get('/chart-chartist', 'chart_chartist')->name('chart_chartist');
    Route::get('/chart-chartjs', 'chart_chartjs')->name('chart_chartjs');
    Route::get('/chart-flot', 'chart_flot')->name('chart_flot');
    Route::get('/chart-morris', 'chart_morris')->name('chart_morris');
    Route::get('/chart-peity', 'chart_peity')->name('chart_peity');
    Route::get('/chart-sparkline', 'chart_sparkline')->name('chart_sparkline');
    Route::get('/ecom-checkout', 'ecom_checkout')->name('ecom_checkout');
    Route::get('/ecom-customers', 'ecom_customers')->name('ecom_customers');
    Route::get('/ecom-invoice', 'ecom_invoice')->name('ecom_invoice');
    Route::get('/ecom-product-detail', 'ecom_product_detail')->name('ecom_product_detail');
    Route::get('/ecom-product-grid', 'ecom_product_grid')->name('ecom_product_grid');
    Route::get('/ecom-product-list', 'ecom_product_list')->name('ecom_product_list');
    Route::get('/ecom-product-order', 'ecom_product_order')->name('ecom_product_order');
    Route::get('/edit-profile', 'edit_profile')->name('edit_profile');
    Route::match(['get','post'],'/email-compose','email_compose')->name('email_compose');
    Route::get('/email-inbox', 'email_inbox')->name('email_inbox');
    Route::get('/email-read', 'email_read')->name('email_read');
    Route::get('/empty-page', 'empty_page')->name('empty_page');
    Route::get('/form-ckeditor', 'form_ckeditor')->name('form_ckeditor');
    Route::get('/form-element', 'form_element')->name('form_element');
    Route::get('/form-pickers', 'form_pickers')->name('form_pickers');
    Route::get('/form-validation', 'form_validation')->name('form_validation');
    Route::get('/form-wizard', 'form_wizard')->name('form_wizard');
    Route::get('/map-jqvmap', 'map_jqvmap')->name('map_jqvmap');
    Route::get('/page-error-400', 'page_error_400')->name('page_error_400');
    Route::get('/page-error-403', 'page_error_403')->name('page_error_403');
    Route::get('/page-error-404', 'page_error_404')->name('page_error_404');
    Route::get('/page-error-500', 'page_error_500')->name('page_error_500');
    Route::get('/page-error-503', 'page_error_503')->name('page_error_503');
    Route::get('/page-forgot-password', 'page_forgot_password')->name('page_forgot_password');
    Route::get('/page-lock-screen', 'page_lock_screen')->name('page_lock_screen');
    Route::get('/page-login', 'page_login')->name('page_login');
    Route::get('/page-register', 'page_register')->name('page_register');
    Route::match(['get','post'],'/post-details','post_details')->name('post_details');
    Route::get('/table-bootstrap-basic', 'table_bootstrap_basic')->name('table_bootstrap_basic');
    Route::get('/table-datatable-basic', 'table_datatable_basic')->name('table_datatable_basic');
    Route::get('/uc-lightgallery', 'uc_lightgallery')->name('uc_lightgallery');
    Route::get('/uc-nestable', 'uc_nestable')->name('uc_nestable');
    Route::get('/uc-noui-slider', 'uc_noui_slider')->name('uc_noui_slider');
    Route::get('/uc-select2', 'uc_select2')->name('uc_select2');
    Route::get('/uc-sweetalert', 'uc_sweetalert')->name('uc_sweetalert');
    Route::get('/uc-toastr', 'uc_toastr')->name('uc_toastr');
    Route::get('/ui-accordion', 'ui_accordion')->name('ui_accordion');
    Route::get('/ui-alert', 'ui_alert')->name('ui_alert');
    Route::get('/ui-badge', 'ui_badge')->name('ui_badge');
    Route::get('/ui-button', 'ui_button')->name('ui_button');
    Route::get('/ui-button-group', 'ui_button_group')->name('ui_button_group');
    Route::get('/ui-card', 'ui_card')->name('ui_card');
    Route::get('/ui-carousel', 'ui_carousel')->name('ui_carousel');
    Route::get('/ui-dropdown', 'ui_dropdown')->name('ui_dropdown');
    Route::get('/ui-grid', 'ui_grid')->name('ui_grid');
    Route::get('/ui-list-group', 'ui_list_group')->name('ui_list_group');
    Route::get('/ui-modal', 'ui_modal')->name('ui_modal');
    Route::get('/ui-pagination', 'ui_pagination')->name('ui_pagination');
    Route::get('/ui-popover', 'ui_popover')->name('ui_popover');
    Route::get('/ui-progressbar', 'ui_progressbar')->name('ui_progressbar');
    Route::get('/ui-tab', 'ui_tab')->name('ui_tab');
    Route::get('/ui-typography', 'ui_typography')->name('ui_typography');
    Route::get('/widget-card', 'widget_card')->name('widget_card');
    Route::get('/widget-chart', 'widget_chart')->name('widget_chart');
    Route::get('/widget-list', 'widget_list')->name('widget_list');


    Route::post('/ajax/recentactivity', 'ajax_recentactivity')->name('ajax_recentactivity');
    Route::post('/ajax/message-content', 'ajax_message_content')->name('ajax_message_content');
    Route::post('/ajax/message', 'ajax_message')->name('ajax_message');
    
});
