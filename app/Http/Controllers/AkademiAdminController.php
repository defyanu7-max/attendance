<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AkademiAdminController extends Controller
{
	public $page_description;
	
    // Dashboard
    public function dashboard(){
        $page_title = 'Dashboard';
        $page_description = $this->page_description();
        return view('akademi.dashboard.index', compact('page_title', 'page_description'));
    }
	
	// Dashboard 2
	public function dashboard_2(){
        $page_title = 'Dashboard';
        $page_description = $this->page_description();
        return view('akademi.dashboard.index_2', compact('page_title', 'page_description'));
    }
	
	// order-list 
	
	public function finance(){
        $page_title = 'Finance';
        $page_description = $this->page_description();
        return view('akademi.dashboard.finance', compact('page_title', 'page_description'));
    }
	
	// order-details 
	
	public function file_manager(){
        $page_title = 'File Manager';
        $page_description = $this->page_description();
        return view('akademi.dashboard.file_manager', compact('page_title', 'page_description'));
    }
	// customer list
	
	public function user(){
        $page_title = 'User';
        $page_description = $this->page_description();
        return view('akademi.dashboard.user', compact('page_title', 'page_description'));
    }
	
	// analytics 
	
	public function celandar(){
        $page_title = 'Celandar';
        $page_description = $this->page_description();
        return view('akademi.dashboard.celandar', compact('page_title', 'page_description'));
    }

    public function chat(){
        $page_title = 'Chat';
        $page_description = $this->page_description();
        return view('akademi.dashboard.chat', compact('page_title', 'page_description'));
    }
	
	// Reviews 
	
	public function activity(){
        $page_title = 'Activity';
        $page_description = $this->page_description();
        return view('akademi.dashboard.activity', compact('page_title', 'page_description'));
    }
	
	// add blog 
	
	public function student(){
        $page_title = 'Student';
        $page_description = $this->page_description();
        return view('akademi.student.student', compact('page_title', 'page_description'));
    }
	
	// add email 
	
	public function student_detail(){
        $page_title = 'Student Detail';
        $page_description = $this->page_description();
        return view('akademi.student.student_detail', compact('page_title', 'page_description'));
    }

    public function add_student(){
        $page_title = 'Add New Student';
        $page_description = $this->page_description();
        return view('akademi.student.add_student', compact('page_title', 'page_description'));
    }

    public function teacher(){
        $page_title = 'Teacher';
        $page_description = $this->page_description();
        return view('akademi.teacher.teacher', compact('page_title', 'page_description'));
    }

    public function teacher_detail(){
        $page_title = 'Teacher Detail';
        $page_description = $this->page_description();
        return view('akademi.teacher.teacher_detail', compact('page_title', 'page_description'));
    }

    public function add_teacher(){
        $page_title = 'Add Teacher';
        $page_description = $this->page_description();
        return view('akademi.teacher.add_teacher', compact('page_title', 'page_description'));
    }
    public function food(){
        $page_title = 'Food';
        $page_description = $this->page_description();
        return view('akademi.food.food', compact('page_title', 'page_description'));
    }
    public function food_details(){
        $page_title = 'Food Details';
        $page_description = $this->page_description();
        return view('akademi.food.food_details', compact('page_title', 'page_description'));
    }
	
	// app-calender 
	
	public function app_calender(){
        $page_title = 'Calender';
        $page_description = $this->page_description();
        return view('akademi.app.calender', compact('page_title', 'page_description'));
    }
	
	// app-profile-1
	
	public function app_profile(){
        $page_title = 'App Profile';
        $page_description = $this->page_description();
        return view('akademi.app.profile', compact('page_title', 'page_description'));
    }
	public function edit_profile(){
        $page_title = 'Edit Profile';
        $page_description = $this->page_description();
        return view('akademi.app.edit_profile', compact('page_title', 'page_description'));
    }
	
	// blog
	
	public function blog(){
        $page_title = 'CMS';
        $page_description = $this->page_description();
        return view('akademi.cms.blog', compact('page_title', 'page_description'));
    }
	
	// add catagery
	
	public function blog_category(){
        $page_title = 'CMS';
        $page_description = $this->page_description();
        return view('akademi.cms.blog_category', compact('page_title', 'page_description'));
    }
	
	// chart-chartist
	
	public function chart_chartist(){
        $page_title = 'Chart Chartlist';
        $page_description = $this->page_description();
        return view('akademi.chart.chartist', compact('page_title', 'page_description'));
    }
	
	// chart-chartjs
	
	public function chart_chartjs(){
        $page_title = 'Chart Chartjs';
        $page_description = $this->page_description();
        return view('akademi.chart.chartjs', compact('page_title', 'page_description'));
    }
	
	// chart-flot
	
	public function chart_flot(){
        $page_title = 'Chart Flot';
        $page_description = $this->page_description();
        return view('akademi.chart.flot', compact('page_title', 'page_description'));
    }
	
	// chart-morris
	
	public function chart_morris(){
        $page_title = 'Chart Morris';
        $page_description = $this->page_description();
        return view('akademi.chart.morris', compact('page_title', 'page_description'));
    }
	
	// chart-sparkline
	
	public function chart_sparkline(){
        $page_title = 'Chart Sparkline';
        $page_description = $this->page_description();
        return view('akademi.chart.sparkline', compact('page_title', 'page_description'));
    }
	
	
	// chart-peity
	
	public function chart_peity(){
        $page_title = 'Chart Peity';
        $page_description = $this->page_description();
        return view('akademi.chart.peity', compact('page_title', 'page_description'));
    }
	
	// Contant
	
	public function content(){
        $page_title = 'CMS';
        $page_description = $this->page_description();
        return view('akademi.cms.content', compact('page_title', 'page_description'));
    }
	
	// Add content
	
	public function content_add(){
        $page_title = 'CMS';
        $page_description = $this->page_description();
        return view('akademi.cms.content_add', compact('page_title', 'page_description'));
    }
	
	// ecom-checkout
	
	public function ecom_checkout(){
        $page_title = 'Ecom Checkout';
        $page_description = $this->page_description();
        return view('akademi.ecom.checkout', compact('page_title', 'page_description'));
    }
	
	// ecom-customers
	
	public function ecom_customers(){
        $page_title = 'Customers';
        $page_description = $this->page_description();
        return view('akademi.ecom.customers', compact('page_title', 'page_description'));
    }
	
	// ecom-invoice
	
	public function ecom_invoice(){
        $page_title = 'Invoice';
        $page_description = $this->page_description();
        return view('akademi.ecom.invoice', compact('page_title', 'page_description'));
    }
	
	// ecom-product-detail
	
	public function ecom_product_detail(){
        $page_title = 'Product Detai';
        $page_description = $this->page_description();
        return view('akademi.ecom.product_detail', compact('page_title', 'page_description'));
    }
	
	// ecom-product-grid
	
	public function ecom_product_grid(){
        $page_title = 'Product Grid';
        $page_description = $this->page_description();
        return view('akademi.ecom.product_grid', compact('page_title', 'page_description'));
    }
	
	// ecom-product-list
	
	public function ecom_product_list(){
        $page_title = 'Product List';
        $page_description = $this->page_description();
        return view('akademi.ecom.product_list', compact('page_title', 'page_description'));
    }
	
	// ecom-product-order
	
	public function ecom_product_order(){
        $page_title = 'Product Order';
        $page_description = $this->page_description();
        return view('akademi.ecom.product_order', compact('page_title', 'page_description'));
    }

	
	// email-compose
	
	public function email_compose(){
        $page_title = 'Email Compose';
        $page_description = $this->page_description();
        return view('akademi.message.compose', compact('page_title', 'page_description'));
    }
	
	//email-inbox
	
	public function email_inbox(){
        $page_title = 'Email Inbox';
        $page_description = $this->page_description();
        return view('akademi.message.inbox', compact('page_title', 'page_description'));
    }
	
	//email-read
	
	public function email_read(){
        $page_title = 'Email Read';
        $page_description = $this->page_description();
        return view('akademi.message.read', compact('page_title', 'page_description'));
    }
	
	//email-template
	
	public function email_template(){
        $page_title = 'CMS';
        $page_description = $this->page_description();
        return view('akademi.cms.email_template', compact('page_title', 'page_description'));
    }
	
	//empty-page
	
	public function empty_page(){
        $page_title = 'Empty Page';
        $page_description = $this->page_description();
        return view('akademi.page.empty_page', compact('page_title', 'page_description'));
    }
	
	//Flat icon
	
	public function flat_icons(){
        $page_title = 'Flaticon Icons';
        $page_description = $this->page_description();
        return view('akademi.icon.flat_icons', compact('page_title', 'page_description'));
    }

    //feather icon
    public function feather(){
        $page_title = 'Feather Icons';
        $page_description = $this->page_description();
        return view('akademi.icon.feather', compact('page_title', 'page_description'));
    }
	
	//form-ckeditor
	
	public function form_ckeditor(){
        $page_title = 'Form Ckeditor';
        $page_description = $this->page_description();
        return view('akademi.form.ckeditor', compact('page_title', 'page_description'));
    }
	
	//form-summernote
	
	public function form_editor_summernote(){
        $page_title = 'Ckeditor';
        $page_description = $this->page_description();
        return view('akademi.form.editor_summernote', compact('page_title', 'page_description'));
    }
	
	//form-element
	
	public function form_element(){
        $page_title = 'Form Element';
        $page_description = $this->page_description();
        return view('akademi.form.element', compact('page_title', 'page_description'));
    }
	
	//form-pickers
	
	public function form_pickers(){
        $page_title = 'Form Pickers';
        $page_description = $this->page_description();
        return view('akademi.form.pickers', compact('page_title', 'page_description'));
    }
	
	//form-validation
	
	public function form_validation(){
        $page_title = 'Form validation';
        $page_description = $this->page_description();
        return view('akademi.form.validation', compact('page_title', 'page_description'));
    }
	
	//form-wizard
	
	public function login(){
        $page_title = 'Login';
        $page_description = $this->page_description();
        return view('akademi.page.login', compact('page_title', 'page_description'));
    }
	
	//login
	
	public function form_wizard(){
        $page_title = 'Form wizard';
        $page_description = $this->page_description();
        return view('akademi.form.wizard', compact('page_title', 'page_description'));
    }
	
	//menu
	
	public function menu(){
        $page_title = 'CMS';
        $page_description = $this->page_description();
        return view('akademi.cms.menu', compact('page_title', 'page_description'));
    }
	
	//ap-jqvmap
	
	public function map_jqvmap(){
        $page_title = 'Jqvmap';
        $page_description = $this->page_description();
        return view('akademi.map.jqvmap', compact('page_title', 'page_description'));
    }
	
	
	//page-error-400
	
	public function page_error_400(){
        $page_title = 'Page Error 400';
        $page_description = $this->page_description();
        return view('akademi.page.error_400', compact('page_title', 'page_description'));
    }
	
	//page-error-403
	
	public function page_error_403(){
        $page_title = 'Page Error 403';
        $page_description = $this->page_description();
        return view('akademi.page.error_403', compact('page_title', 'page_description'));
    }
	
	//page-error-404
	
	public function page_error_404(){
        $page_title = 'Page Error 404';
        $page_description = $this->page_description();
        return view('akademi.page.error_404', compact('page_title', 'page_description'));
    }
	
	//page-error-500
	
	public function page_error_500(){
        $page_title = 'Page Error 500';
        $page_description = $this->page_description();
        return view('akademi.page.error_500', compact('page_title', 'page_description'));
    }
	
	//page-error-503
	
	public function page_error_503(){
        $page_title = 'Page Error 503';
        $page_description = $this->page_description();
        return view('akademi.page.error_503', compact('page_title', 'page_description'));
    }
	
	//page-forgot-password
	
	public function page_forgot_password(){
        $page_title = 'Page Forgot Password';
        $page_description = $this->page_description();
        return view('akademi.page.forgot_password', compact('page_title', 'page_description'));
    }
	
	//page-lock-screen
	
	public function page_lock_screen(){
        $page_title = 'Page Lock Screen';
        $page_description = $this->page_description();
        return view('akademi.page.lock_screen', compact('page_title', 'page_description'));
    }
	
	//page-login
	
	public function page_login(){
        $page_title = 'Page Login';
        $page_description = $this->page_description();
        return view('akademi.page.login', compact('page_title', 'page_description'));
    }
	
	//page-register
	
	public function page_register(){
        $page_title = 'Page Register';
        $page_description = $this->page_description();
        return view('akademi.page.register', compact('page_title', 'page_description'));
    }
	
	//svg
	
	public function svg_icons(){
        $page_title = 'Svg Icons';
        $page_description = $this->page_description();
        return view('akademi.icon.svg_icons', compact('page_title', 'page_description'));
    }
	
	//svg icon
	
	public function post_details(){
        $page_title = 'Post Details';
        $page_description = $this->page_description();
        return view('akademi.app.post_details', compact('page_title', 'page_description'));
    }
	
	//table-bootstrap-basic
	public function table_bootstrap_basic(){
        $page_title = 'Bootstrap Basic';
        $page_description = $this->page_description();
        return view('akademi.table.bootstrap_basic', compact('page_title', 'page_description'));
    }
	
	//table-datatable-basic
	
	public function table_datatable_basic(){
        $page_title = 'Datatable Basic';
        $page_description = $this->page_description();
        return view('akademi.table.datatable_basic', compact('page_title', 'page_description'));
    }
	
	//uc-lightgallery
	
	public function uc_lightgallery(){
        $page_title = 'Light Gallery';
        $page_description = $this->page_description();
        return view('akademi.uc.lightgallery', compact('page_title', 'page_description'));
    }
	
	//uc-nestable
	
	public function uc_nestable(){
        $page_title = 'Nestable';
        $page_description = $this->page_description();
        return view('akademi.uc.nestable', compact('page_title', 'page_description'));
    }
	
	//uc-noui-slider
	
	public function uc_noui_slider(){
        $page_title = 'Noui Slider';
        $page_description = $this->page_description();
        return view('akademi.uc.noui_slider', compact('page_title', 'page_description'));
    }
	
	//uc-select2
	
	public function uc_select2(){
        $page_title = 'Select2';
        $page_description = $this->page_description();
        return view('akademi.uc.select2', compact('page_title', 'page_description'));
    }
	
	//uc-sweetalert
	
	public function uc_sweetalert(){
        $page_title = 'Sweetalert';
        $page_description = $this->page_description();
        return view('akademi.uc.sweetalert', compact('page_title', 'page_description'));
    }
	
	//uc-toastr
	
	public function uc_toastr(){
        $page_title = 'Toastr';
        $page_description = $this->page_description();
        return view('akademi.uc.toastr', compact('page_title', 'page_description'));
    }
	
	//ui-accordion
	
	public function ui_accordion(){
        $page_title = 'Accordion';
        $page_description = $this->page_description();
        return view('akademi.ui.accordion', compact('page_title', 'page_description'));
    }
	
	//ui-alert
	
	public function ui_alert(){
        $page_title = 'Alert';
        $page_description = $this->page_description();
        return view('akademi.ui.alert', compact('page_title', 'page_description'));
    }
	
	//ui-badge
	
	public function ui_badge(){
        $page_title = 'Badge';
        $page_description = $this->page_description();
        return view('akademi.ui.badge', compact('page_title', 'page_description'));
    }
	
	//ui-button
	
	public function ui_button(){
        $page_title = 'Button';
        $page_description = $this->page_description();
        return view('akademi.ui.button', compact('page_title', 'page_description'));
    }
	
	//ui-button-group
	
	public function ui_button_group(){
        $page_title = 'Button';
        $page_description = $this->page_description();
        return view('akademi.ui.button_group', compact('page_title', 'page_description'));
    }
	
	//ui-button-group
	
	public function ui_card(){
        $page_title = 'Card';
        $page_description = $this->page_description();
        return view('akademi.ui.card', compact('page_title', 'page_description'));
    }
	
	//ui-carousel
	
	public function ui_carousel(){
        $page_title = 'Carouse';
        $page_description = $this->page_description();
        return view('akademi.ui.carousel', compact('page_title', 'page_description'));
    }
	
	//ui-dropdown
	
	public function ui_dropdown(){
        $page_title = 'Dropdown';
        $page_description = $this->page_description();
        return view('akademi.ui.dropdown', compact('page_title', 'page_description'));
    }
	
	//ui-grid
	
	public function ui_grid(){
        $page_title = 'Grid';
        $page_description = $this->page_description();
        return view('akademi.ui.grid', compact('page_title', 'page_description'));
    }
	
	//media object
	
	public function ui_media_object(){
        $page_title = 'Media Object';
        $page_description = $this->page_description();
        return view('akademi.ui.media_object', compact('page_title', 'page_description'));
    }
	
	//ui-list-group
	
	public function ui_list_group(){
        $page_title = 'List Group';
        $page_description = $this->page_description();
        return view('akademi.ui.list_group', compact('page_title', 'page_description'));
    }
	
	//ui-modal
	
	public function ui_modal(){
        $page_title = 'Modal';
        $page_description = $this->page_description();
        return view('akademi.ui.modal', compact('page_title', 'page_description'));
    }
	
	//ui-pagination
	
	public function ui_pagination(){
        $page_title = 'Pagination';
        $page_description = $this->page_description();
        return view('akademi.ui.pagination', compact('page_title', 'page_description'));
    }
	
	//ui-popover
	
	public function ui_popover(){
        $page_title = 'Popover';
        $page_description = $this->page_description();
        return view('akademi.ui.popover', compact('page_title', 'page_description'));
    }
	
	//ui-progressbar
	
	public function ui_progressbar(){
        $page_title = 'Progressbar';
        $page_description = $this->page_description();
        return view('akademi.ui.progressbar', compact('page_title', 'page_description'));
    }
	
	//ui-tab
	
	public function ui_tab(){
        $page_title = 'Tab';
        $page_description = $this->page_description();
        return view('akademi.ui.tab', compact('page_title', 'page_description'));
    }
	
	//ui-typography
	
	public function ui_typography(){
        $page_title = 'Tab';
        $page_description = $this->page_description();
        return view('akademi.ui.typography', compact('page_title', 'page_description'));
    }
	
	//widget-basic
	public function widget_card(){
        $page_title = 'Widget Card';
        $page_description = $this->page_description();
        return view('akademi.widget.card', compact('page_title', 'page_description'));
    }

    //widget-basic
	public function widget_chart(){
        $page_title = 'Widget Chart';
        $page_description = $this->page_description();
        return view('akademi.widget.chart', compact('page_title', 'page_description'));
    }

    //widget-basic
	public function widget_list(){
        $page_title = 'Widget List';
        $page_description = $this->page_description();
        return view('akademi.widget.list', compact('page_title', 'page_description'));
    }
	
    //seller_menus
	public function ajax_recentactivity(){
        return view('akademi.ajax.recentactivity');
    }

    //seller_menus
	public function ajax_message_content(){
        return view('akademi.ajax.message_content');
    }

    //seller_menus
	public function ajax_message(){
        return view('akademi.ajax.message');
    }

    //seller_menus
	public function ajax_food_menu_list_3(){
        return view('akademi.ajax.food_menu_list_3');
    }

    //seller_menus
	public function ajax_trending_ingridients(){
        return view('akademi.ajax.trending_ingridients');
    }

    //seller_menus
	public function ajax_featured_menu_list(){
        return view('akademi.ajax.featured_menu_list');
    }
    
	//seller_menus
	public function ajax_recent_activities(){
        return view('akademi.ajax.recent_activities');
    }
	
	//$page_description = $this->page_description();
	private function page_description() {
		return 'Akademi - the ultimate admin dashboard and Bootstrap 5 template. Specially designed for professionals, and for business. Akademi provides advanced features and an easy-to-use interface for creating a top-quality website with School and Education Dashboard';
	}
	
	
}
