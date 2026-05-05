<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Application Name
	|--------------------------------------------------------------------------
	|
	| This value is the name of your application. This value is used when the
	| framework needs to place the application's name in a notification or
	| any other location as required by the application or its packages.
	|
	*/

	'name' => env('APP_NAME', 'Akademi Laravel'),

	'public' => [
		'global' => [
			'css' => [
					'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
					'css/style.css',
			],
			'js' => [
				'top'=> [
					'vendor/global/global.min.js',
					'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
				],
				'bottom'=> [
					'js/custom.min.js',
					'js/dlabnav-init.js',
				],
			],
		],
		'pagelevel' => [
			'css' => [
				'AkademiAdminController_dashboard' => [
					'vendor/wow-master/css/libs/animate.css',
					'vendor/datatables/css/jquery.dataTables.min.css',
					'vendor/datepicker/css/bootstrap-datepicker.min.css',
					'vendor/bootstrap-select-country/css/bootstrap-select-country.min.css',
					'vendor/jquery-nice-select/css/nice-select.css',
					'vendor/swiper/css/swiper-bundle.min.css',
					
				],
				'AkademiAdminController_dashboard_2' => [
					'vendor/wow-master/css/libs/animate.css',
					'vendor/datatables/css/jquery.dataTables.min.css',
					'vendor/datepicker/css/bootstrap-datepicker.min.css',
					'vendor/bootstrap-select-country/css/bootstrap-select-country.min.css',
					'vendor/jquery-nice-select/css/nice-select.css',
					'vendor/swiper/css/swiper-bundle.min.css',
				],
				'AkademiAdminController_finance' => [
					'vendor/wow-master/css/libs/animate.css',
					'vendor/datatables/css/jquery.dataTables.min.css',
				],
				'AkademiAdminController_app_calender' => [
					'./vendor/fullcalendar-5.11.0/lib/main.css'
				],
				'AkademiAdminController_celandar' => [
					'vendor/fullcalendar-5.11.0/lib/main.css',
					'vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'
				],
				'AkademiAdminController_activity'=>[
					'vendor/lightgallery/css/lightgallery.min.css'
				],
				'AkademiAdminController_student'=>[
					'vendor/wow-master/css/libs/animate.css',
					'vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',
				],
				'AkademiAdminController_student_detail'=>[
					'vendor/datatables/css/jquery.dataTables.min.css',
					'vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',
				],
				'AkademiAdminController_edit_profile' => [
					'vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css',
					
				],
				'AkademiAdminController_app_profile' => [
					'vendor/lightgallery/css/lightgallery.min.css',
					
				],
				
				'AkademiAdminController_post_details' => [
					'vendor/lightgallery/css/lightgallery.min.css',
					
				],
				'AkademiAdminController_add_student' => [
					'vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css',
					
				],
				'AkademiAdminController_add_teacher' => [
					'vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css',
					
				],
				'AkademiAdminController_blog_category' => [
					'vendor/datatables/css/jquery.dataTables.min.css',
					
				],

				'AkademiAdminController_chart_chartist' => [
					'vendor/chartist/css/chartist.min.css'
				],
				'AkademiAdminController_chart_chartjs' => [
				],
				'AkademiAdminController_chart_flot' => [
				],
				'AkademiAdminController_chart_morris' => [
				],
				'AkademiAdminController_chart_peity' => [
				],
				'AkademiAdminController_chart_sparkline' => [
				],
				'AkademiAdminController_ecom_checkout' => [
				],
				'AkademiAdminController_ecom_customers' => [
				],
				'AkademiAdminController_ecom_invoice' => [
					
				],
				'AkademiAdminController_ecom_product_detail' => [
					'vendor/star-rating/star-rating-svg.css',
				],
				'AkademiAdminController_ecom_product_grid' => [
				],
				'AkademiAdminController_ecom_product_list' => [
					'vendor/star-rating/star-rating-svg.css'
				],
				'AkademiAdminController_ecom_product_order' => [
				],
				'AkademiAdminController_email_compose' => [
					'vendor/dropzone/dist/dropzone.css'
				],
				'AkademiAdminController_email_inbox' => [
					
				],
				'AkademiAdminController_email_read' => [
				],
				'AkademiAdminController_editor' => [
					
				],
				'AkademiAdminController_form_element' => [
				],
				'AkademiAdminController_form_pickers' => [
					'vendor/bootstrap-daterangepicker/daterangepicker.css',
					'vendor/clockpicker/css/bootstrap-clockpicker.min.css',
					'vendor/jquery-asColorPicker/css/asColorPicker.min.css',
					'vendor/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css',
					'vendor/pickadate/themes/default.css',
					'vendor/pickadate/themes/default.date.css',
					'https://fonts.googleapis.com/icon?family=Material+Icons',
				],
				'AkademiAdminController_email_template' => [
					'vendor/datatables/css/jquery.dataTables.min.css',
					'vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css',
				],
				'AkademiAdminController_blog' => [
					'vendor/datatables/css/jquery.dataTables.min.css',
					'vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css',
				],
				'AkademiAdminController_content' => [
					'vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css',
					'vendor/select2/css/select2.min.css',
					'vendor/datatables/css/jquery.dataTables.min.css',
					
					
				],
				'AkademiAdminController_form_validation_jquery' => [
				],
				'AkademiAdminController_form_wizard' => [
					'vendor/jquery-smartwizard/dist/css/smart_wizard.min.css',
					'vendor/dropzone/dist/dropzone.css',
				],
				'AkademiAdminController_map_jqvmap' => [
					'vendor/jqvmap/css/jqvmap.min.css'
				],
				'AkademiAdminController_page_error_400' => [
					'vendor/bootstrap-select/dist/css/bootstrap-select.min.css'
				],
				'AkademiAdminController_table_bootstrap_basic' => [
					
				],
				'AkademiAdminController_table_datatable_basic' => [
					'vendor/datatables/css/jquery.dataTables.min.css',
					
				],
				'AkademiAdminController_uc_lightgallery' => [
					'vendor/lightgallery/css/lightgallery.min.css',
					
				],
				'AkademiAdminController_uc_nestable' => [
					'vendor/nestable2/css/jquery.nestable.min.css'
				],
				'AkademiAdminController_uc_noui_slider' => [
					'vendor/nouislider/nouislider.min.css'
				],
				'AkademiAdminController_uc_select2' => [
					'vendor/select2/css/select2.min.css'
				],
				'AkademiAdminController_uc_sweetalert' => [
					'vendor/sweetalert2/dist/sweetalert2.min.css'
				],
				'AkademiAdminController_uc_toastr' => [
					'vendor/toastr/css/toastr.min.css'
				],
				
				'AkademiAdminController_ui_accordion' => [
				],
				'AkademiAdminController_ui_alert' => [
				],
				'AkademiAdminController_ui_badge' => [
				],
				'AkademiAdminController_ui_button' => [
				],
				'AkademiAdminController_ui_button_group' => [
				],
				'AkademiAdminController_ui_card' => [
				],
				'AkademiAdminController_ui_carousel' => [
				],
				'AkademiAdminController_ui_dropdown' => [
				],
				'AkademiAdminController_ui_grid' => [
				],
				'AkademiAdminController_ui_list_group' => [
				],
				'AkademiAdminController_ui_media_object' => [
				],
				'AkademiAdminController_ui_modal' => [
				],
				'AkademiAdminController_ui_pagination' => [
				],
				'AkademiAdminController_ui_popover' => [
				],
				'AkademiAdminController_ui_progressbar' => [
				],
				'AkademiAdminController_ui_tab' => [
				],
				'AkademiAdminController_ui_typography' => [
				],
				'AkademiAdminController_widget_basic' => [
					'vendor/chartist/css/chartist.min.css',
				],
			],
			'js' => [
				'AkademiAdminController_dashboard' => [
					'vendor/chart.js/Chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
					'vendor/peity/jquery.peity.min.js',
					'vendor/datatables/js/jquery.dataTables.min.js',
					'js/plugins-init/datatables.init.js',
					'js/dashboard/dashboard-1.js',
					'vendor/wow-master/dist/wow.min.js',
					'vendor/bootstrap-datetimepicker/js/moment.js',
					'vendor/datepicker/js/bootstrap-datepicker.min.js',
					'vendor/swiper/js/swiper-bundle.min.js',
				],
				'AkademiAdminController_dashboard_2' => [
					'vendor/chart.js/Chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
					'vendor/peity/jquery.peity.min.js',
					'vendor/datatables/js/jquery.dataTables.min.js',
					'js/plugins-init/datatables.init.js',
					'js/dashboard/dashboard-1.js',
					'vendor/wow-master/dist/wow.min.js',
					'vendor/bootstrap-datetimepicker/js/moment.js',
					'vendor/datepicker/js/bootstrap-datepicker.min.js',
					'vendor/swiper/js/swiper-bundle.min.js',
				],
				
				
				'AkademiAdminController_finance' => [
					'vendor/datatables/js/jquery.dataTables.min.js',
					'js/plugins-init/datatables.init.js',
					'js/dashboard/dashboard-2.js',
					'vendor/chart.js/Chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
					'vendor/wow-master/dist/wow.min.js',
				],
				'AkademiAdminController_student'=>[
					'vendor/datatables/js/jquery.dataTables.min.js',
					'js/plugins-init/datatables.init.js',
					'vendor/wow-master/dist/wow.min.js',
				],
				'AkademiAdminController_student_detail'=>[
					'vendor/datatables/js/jquery.dataTables.min.js',
					'js/plugins-init/datatables.init.js',
					'vendor/wow-master/dist/wow.min.js',
				],
				'AkademiAdminController_celandar' => [
					'vendor/fullcalendar-5.11.0/lib/main.js',
					'vendor/wow-master/dist/wow.min.js',
					'js/calendar-2.js',
				],
				'AkademiAdminController_activity' => [
					'vendor/lightgallery/js/lightgallery-all.min.js',
				],

				'AkademiAdminController_app_calender' => [
					'vendor/moment/moment.min.js',
					'vendor/fullcalendar-5.11.0/lib/main.js',
					'js/calendar.js',
				],
				'AkademiAdminController_add_student' => [
					'vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js',
					
				],
				'AkademiAdminController_add_teacher' => [
					'vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js',
					
				],
				
				'AkademiAdminController_app_profile' => [
					'vendor/lightgallery/js/lightgallery-all.min.js',
					
				],
				'AkademiAdminController_post_details' => [
					'vendor/lightgallery/js/lightgallery-all.min.js',
					
				],
				'AkademiAdminController_chart_chartist' => [
					'vendor/apexchart/apexchart.js',
					'vendor/chartist/js/chartist.min.js',
					'vendor/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js',
					'js/plugins-init/chartist-init.js',
				],
				'AkademiAdminController_chart_chartjs' => [
					'vendor/chart.js/Chart.bundle.min.js',
					'js/plugins-init/chartjs-init.js',
				],
				'AkademiAdminController_chart_flot' => [
					'vendor/flot/jquery.flot.js',
					'vendor/flot/jquery.flot.pie.js',
					'vendor/flot/jquery.flot.resize.js',
					'vendor/flot-spline/jquery.flot.spline.min.js',
					'js/plugins-init/flot-init.js',
					
				],
				'AkademiAdminController_chart_morris' => [
					
					'vendor/apexchart/apexchart.js',
					'vendor/raphael/raphael.min.js',
					'vendor/morris/morris.min.js',
					'js/plugins-init/morris-init.js',
				],
				'AkademiAdminController_chart_peity' => [
					'vendor/peity/jquery.peity.min.js',
					'js/plugins-init/piety-init.js',
				],
				'AkademiAdminController_chart_sparkline' => [
					'vendor/apexchart/apexchart.js',
					'vendor/jquery-sparkline/jquery.sparkline.min.js',
					'js/plugins-init/sparkline-init.js',
				],
				'AkademiAdminController_ecom_checkout' => [
				],
				'AkademiAdminController_ecom_customers' => [
				],
				'AkademiAdminController_ecom_invoice' => [

				],
				'AkademiAdminController_ecom_product_detail' => [
					'vendor/star-rating/jquery.star-rating-svg.js',
				],
				'AkademiAdminController_ecom_product_grid' => [
				],
				'AkademiAdminController_ecom_product_list' => [
					'vendor/star-rating/jquery.star-rating-svg.js'
				],
				'AkademiAdminController_ecom_product_order' => [
				],
				'AkademiAdminController_email_compose' => [
					'vendor/dropzone/dist/dropzone.js'
				],
				'AkademiAdminController_email_inbox' => [
					
				],
				'AkademiAdminController_email_read' => [
				],
				'AkademiAdminController_form_ckeditor' => [
					'vendor/ckeditor/ckeditor.js',
				],
				'AkademiAdminController_form_element' => [
				],
				'AkademiAdminController_form_pickers' => [
					'vendor/moment/moment.min.js',
					'vendor/bootstrap-daterangepicker/daterangepicker.js',
					'vendor/clockpicker/js/bootstrap-clockpicker.min.js',
					'vendor/jquery-asColor/jquery-asColor.min.js',
					'vendor/jquery-asGradient/jquery-asGradient.min.js',
					'vendor/jquery-asColorPicker/js/jquery-asColorPicker.min.js',
					'vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js',
					'vendor/pickadate/picker.js',
					'vendor/pickadate/picker.time.js',
					'vendor/pickadate/picker.date.js',
					'js/plugins-init/bs-daterange-picker-init.js',
					'js/plugins-init/clock-picker-init.js',
					'js/plugins-init/jquery-asColorPicker.init.js',
					'js/plugins-init/material-date-picker-init.js',
					'js/plugins-init/pickadate-init.js',
				],
				'AkademiAdminController_form_validation_jquery' => [
				],
				'AkademiAdminController_form_wizard' => [
					'vendor/jquery-steps/build/jquery.steps.min.js',
					'vendor/jquery-validation/jquery.validate.min.js',
					'js/plugins-init/jquery.validate-init.js',
					'vendor/jquery-smartwizard/dist/js/jquery.smartWizard.js',
					'vendor/dropzone/dist/dropzone.js',
					
				],
				'AkademiAdminController_map_jqvmap' => [
					'vendor/jqvmap/js/jquery.vmap.min.js',
					'vendor/jqvmap/js/jquery.vmap.world.js',
					'vendor/jqvmap/js/jquery.vmap.usa.js',
					'js/plugins-init/jqvmap-init.js',
				],
				'AkademiAdminController_page_error_400' => [
				],
				'AkademiAdminController_page_error_403' => [
				],
				'AkademiAdminController_page_error_404' => [
				],
				'AkademiAdminController_page_error_500' => [
				],
				'AkademiAdminController_page_error_503' => [
				],
				'AkademiAdminController_page_forgot_password' => [
				],
				'AkademiAdminController_page_lock_screen' => [
					'vendor/deznav/deznav.min.js'
				],
				'AkademiAdminController_page_login' => [
				],
				'AkademiAdminController_page_register' => [
				],
				'AkademiAdminController_table_bootstrap_basic' => [
					'js/highlight.min.js',

				],
				'AkademiAdminController_table_datatable_basic' => [
					'vendor/datatables/js/jquery.dataTables.min.js',
					'js/plugins-init/datatables.init.js',
					'js/highlight.min.js',
					
				],
				'AkademiAdminController_edit_profile' => [
					'vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js',
					
				],
				'AkademiAdminController_uc_lightgallery' => [
					'vendor/lightgallery/js/lightgallery-all.min.js',
				],
				'AkademiAdminController_uc_nestable' => [
					'vendor/nestable2/js/jquery.nestable.min.js',
					'js/plugins-init/nestable-init.js',
				],
				'AkademiAdminController_uc_noui_slider' => [
					'vendor/nouislider/nouislider.min.js',
					'vendor/wnumb/wNumb.js',
					'js/plugins-init/nouislider-init.js',
				],
				'AkademiAdminController_uc_select2' => [
					'vendor/select2/js/select2.full.min.js',
					'js/plugins-init/select2-init.js',
				],
				'AkademiAdminController_uc_sweetalert' => [
					'vendor/sweetalert2/dist/sweetalert2.min.js',
					'js/plugins-init/sweetalert.init.js',
				],
				'AkademiAdminController_uc_toastr' => [
					'vendor/toastr/js/toastr.min.js',
					'js/plugins-init/toastr-init.js',
				],
				'AkademiAdminController_ui_accordion' => [
					'js/highlight.min.js',
				],
				'AkademiAdminController_ui_alert' => [
					'js/highlight.min.js',
				],
				'AkademiAdminController_ui_badge' => [
					'js/highlight.min.js',
				],
				'AkademiAdminController_ui_button' => [
					'js/highlight.min.js',
				],
				'AkademiAdminController_ui_button_group' => [
					'js/highlight.min.js',
				],
				'AkademiAdminController_ui_card' => [
					'js/highlight.min.js',
				],
				'AkademiAdminController_ui_carousel' => [
					'js/highlight.min.js',
				],
				'AkademiAdminController_ui_dropdown' => [
					'js/highlight.min.js',
				],
				'AkademiAdminController_ui_grid' => [
				],
				'AkademiAdminController_ui_list_group' => [
					'js/highlight.min.js',
				],
				'AkademiAdminController_ui_media_object' => [
				],
				'AkademiAdminController_ui_modal' => [
				],
				'AkademiAdminController_ui_pagination' => [
					'js/highlight.min.js',
				],
				'AkademiAdminController_ui_popover' => [
				],
				'AkademiAdminController_ui_progressbar' => [
					'js/highlight.min.js',
				],
				'AkademiAdminController_ui_tab' => [
					'js/highlight.min.js',
				],
				'AkademiAdminController_ui_typography' => [
				],
				'AkademiAdminController_widget_card' => [
				],
				'AkademiAdminController_widget_chart' => [
					'vendor/chart.js/Chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
					'vendor/chartist/js/chartist.min.js',
					'vendor/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js',
					'vendor/flot/jquery.flot.js',
					'vendor/flot/jquery.flot.pie.js',
					'vendor/flot/jquery.flot.resize.js',
					'vendor/flot-spline/jquery.flot.spline.min.js',
					'vendor/jquery-sparkline/jquery.sparkline.min.js',
					'js/plugins-init/sparkline-init.js',
					'vendor/peity/jquery.peity.min.js',
					'js/plugins-init/piety-init.js',
					'vendor/bootstrap-datetimepicker/js/moment.js',
					'vendor/datepicker/js/bootstrap-datepicker.min.js',
					'vendor/wow-master/dist/wow.min.js',
					'js/plugins-init/widgets-script-init.js',
				],
				'AkademiAdminController_widget_list' => [
					'vendor/chart.js/Chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
					'vendor/chartist/js/chartist.min.js',
					'vendor/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js',
					'vendor/flot/jquery.flot.js',
					'vendor/flot/jquery.flot.pie.js',
					'vendor/flot/jquery.flot.resize.js',
					'vendor/flot-spline/jquery.flot.spline.min.js',
					'vendor/jquery-sparkline/jquery.sparkline.min.js',
					'vendor/jquery-sparkline/jquery.sparkline.min.js',
					'js/plugins-init/sparkline-init.js', 
					'vendor/peity/jquery.peity.min.js', 
					'js/plugins-init/piety-init.js', 
					'js/plugins-init/widgets-script-init.js', 
				],
				
			]
		],
	]
];
