<?php

use Illuminate\Http\Request;

Route::apiResources(['admin-types' => 'API\AdminTypesController']);
Route::apiResources(['admins' => 'API\AdminsController']);
Route::apiResources(['appointment-queries' => 'API\AppointmentQueriesController']);
Route::apiResources(['categories' => 'API\CategoriesController']);
Route::apiResources(['customers' => 'API\UsersController']);
Route::apiResources(['courses' => 'API\CoursesController']);
Route::apiResources(['diagnosis' => 'API\DiseasesController']);
Route::apiResources(['diseases' => 'API\DiseasesController']);
Route::apiResources(['doctors' => 'API\DoctorsController']);
Route::apiResources(['discounts' => 'API\DiscountsController']);
Route::apiResources(['financial-years' => 'API\FinancialYearsController']);
Route::apiResources(['insurances' => 'API\InsurancesController']);
Route::apiResources(['lab-categories' => 'API\LabCategoriesController']);
Route::apiResources(['laboratories' => 'API\LaboratoriesController']);
Route::apiResources(['locations' => 'API\LocationsController']);
Route::apiResources(['manufacturers' => 'API\ManufacturersController']);
Route::apiResources(['medicines' => 'API\MedicinesController']);
Route::apiResources(['oe-categories' => 'API\OeCategoriesController']);
Route::apiResources(['options' => 'API\OptionsController']);
Route::apiResources(['packages' => 'API\PackagesController']);
Route::apiResources(['purchases' => 'API\PurchasesController']);
Route::apiResources(['payments' => 'API\PaymentsController']);
Route::apiResources(['rooms' => 'API\RoomsController']);
Route::apiResources(['sale-medicines' => 'API\SaleMedicinesController']);
Route::apiResources(['stocks' => 'API\StocksController']);
Route::apiResources(['suppliers' => 'API\SuppliersController']);
Route::apiResources(['symptoms' => 'API\SymptomsController']);
Route::apiResources(['treatments' => 'API\TreatmentsController']);
Route::apiResources(['xrays' => 'API\XraysController']);
Route::apiResources(['comments' => 'API\CommentsController']);
Route::apiResources(['settlements' => 'API\SettlementsController']);

// Select options list
Route::get('getLabCategorySelectList', 'API\LabCategoriesController@getSelectList');
Route::get('getSymptomsSelectList', 'API\SymptomsController@getSelectList');
Route::get('getDiseasesSelectList', 'API\DiseasesController@getSelectList');
Route::get('getDiagnosisSelectList', 'API\DiagnosisController@getSelectList');
Route::get('getOeCategoriesSelectList', 'API\OeCategoriesController@getSelectList');
Route::get('getSlotsSelectList', 'API\TimeSlotsController@getSelectList');
Route::get('getXTestsSelectList', 'API\CommonController@getXTestSelectList');
Route::get('getDTestsSelectList', 'API\CommonController@getDTestSelectList');
Route::get('getCustomerSelectList', 'API\CommonController@getCustomerSelectList');
Route::get('getMedicinesSelectList', 'API\MedicinesController@getFullSelectList');
Route::get('getInsuredMedicinesSelectList/{id}', 'API\MedicinesController@getFullISelectList');
Route::get('getStocksSelectList', 'API\StocksController@getFullSelectList');
Route::get('getTreatmentsSelectList', 'API\TreatmentsController@getSelectList');
Route::get('getPresTreatmentsSelectList', 'API\TreatmentsController@getPresSelectList');
Route::get('getConsultationsSelectList', 'API\TreatmentsController@getCSelectList');
Route::get('getSuppliersSelectList', 'API\SuppliersController@getFullSelectList');
Route::get('getDosesSelectList', 'API\DosesController@getSelectList');
Route::get('getInstructionsSelectList', 'API\InstructionsController@getSelectList');
Route::get('getCommentsList', 'API\CommentsController@getList');
Route::get('getReports', 'API\CommonController@getReports');

Route::get('getLabCategoryList', 'API\LabCategoriesController@getList');
Route::get('getCategoryList', 'API\CategoriesController@getList');
Route::get('getInsurancersList', 'API\InsurancesController@getList');
Route::get('getIdentitiesList', 'API\IdentityTypesController@getList');
Route::get('getLocationsList', 'API\LocationsController@getList');
Route::get('getNonactiveLocationsList', 'API\LocationsController@getNonList');
Route::get('getTreatmentsList', 'API\TreatmentsController@getTList');
Route::get('getAllTreatmentsList', 'API\TreatmentsController@getList');
Route::get('getSymptomsList', 'API\SymptomsController@getList');
Route::get('getConsultationsList', 'API\TreatmentsController@getCList');
Route::get('getDoctorsList', 'API\DoctorsController@getList');
Route::get('getYearsList', 'API\FinancialYearsController@getList');
Route::get('getAllYearsList', 'API\FinancialYearsController@getAllList');
Route::get('getNxtYearsList', 'API\FinancialYearsController@getNxtList');
Route::get('getPrevYearsList', 'API\FinancialYearsController@getPrevList');
Route::get('getOnlyDoctorsList', 'API\DoctorsController@getDoctorsList');
Route::get('getOnlyTherapistsList', 'API\DoctorsController@getTherapistsList');
Route::get('getAdminTypesList', 'API\AdminTypesController@getList');
Route::get('getAppointmentTypesList', 'API\AppointmentTypesController@getList');
Route::get('getOptionsList', 'API\OptionsController@getList');
Route::get('getDiseasesList', 'API\DiseasesController@getList');
Route::get('getDiagnosisList', 'API\DiagnosisController@getList');
Route::get('getOeCategoriesList', 'API\OeCategoriesController@getList');
Route::get('getSlotsList', 'API\TimeSlotsController@getList');
Route::get('getNationalityList', 'API\CommonController@NationalityList');
Route::get('getXTestsList', 'API\CommonController@getXTestList');
Route::get('getDTestsList', 'API\CommonController@getDTestList');
Route::get('getMedicinesList', 'API\MedicinesController@getFullList');
Route::get('getMedicineDetail/{id}', 'API\MedicinesController@getDetail');
Route::get('getTreatmentCost/{id}', 'API\TreatmentsController@getCost');
Route::post('getPackageCost', 'API\PackagesController@getCost');
Route::get('getPackagesList', 'API\PackagesController@getList');
Route::get('getTherapistsArrayList', 'API\DoctorsController@getTherapistsArrayList');
Route::get('getMedStockList', 'API\MedicinesController@getMedStockList');

Route::get('getPackageDetail/{id}', 'API\PackagesController@getDetails');
Route::get('get-profile', 'API\ProfileController@getData');
Route::get('get-last-financial-year', 'API\FinancialYearsController@getLastYear');
Route::get('get-customer/{id}', 'API\UsersController@getProfile');
Route::get('get-active-user', 'API\ProfileController@getActiveUser');
Route::post('get-filtered-discounts', 'API\DiscountsController@filteredDiscount');

Route::get('getMappedConsultations/{id}', 'API\InsurancesController@getMappedConsultations');
Route::post('update-consultation-insurance-mapping', 'API\InsurancesController@updateMappedConsultations');
Route::get('getMappedTreatments/{id}', 'API\InsurancesController@getMappedTreatments');
Route::post('update-treatment-insurance-mapping', 'API\InsurancesController@updateMappedTreatments');
Route::get('getMappedMedicines/{id}', 'API\InsurancesController@getMappedMedicines');
Route::post('update-medicine-insurance-mapping', 'API\InsurancesController@updateMappedMedicines');
Route::post('update-insurance-mapping', 'API\InsurancesController@updateMapping');
Route::get('insurances/addCashDiscount/{id}', 'API\InsurancesController@addCashDiscount');
Route::get('insurances/removeCashDiscount/{id}', 'API\InsurancesController@removeCashDiscount');


Route::get('searchCustomer', 'API\UsersController@find');
Route::get('searchCustomerByPhone', 'API\UsersController@findByMobile');
Route::get('searchMedicineByName', 'API\MedicinesController@findByName');

//********  Appointment APIs   ***********///
Route::get('appointments/get-upcomings-yearwise/{id}', 'API\AppointmentsController@getUpcomingsYearwise');
Route::get('appointments/get-personal-upcomings-yearwise/{id}', 'API\AppointmentsController@getPersonalUpcomingsYearwise');
Route::get('appointments/get-history-yearwise/{id}', 'API\AppointmentsController@getHistoryYearwise');
Route::get('appointments/get-patient-history-yearwise/{id}/{year}', 'API\AppointmentsController@getPatientHistoryYearwise');
Route::get('appointments/get-todays-appointments', 'API\AppointmentsController@getTodaysAppointments');
//Route::get('appointments/get-todays-appointments', 'API\AppointmentsController@getTodaysAppointments');
Route::post('appointments/make-appointment', 'API\AppointmentsController@makeAppointment');
Route::post('appointments/add-prescription', 'API\AppointmentsController@addPrescription');
Route::post('appointments/change-patient', 'API\AppointmentsController@changePatient');
Route::post('appointments/update-appointment', 'API\AppointmentsController@updateAppointment');
Route::get('appointments/get-start-timings', 'API\AppointmentsController@getTimings');
Route::get('appointments/get-end-timings', 'API\AppointmentsController@getClosings');
Route::get('appointments/get-second-therapist', 'API\AppointmentsController@getSecondTherapist');
Route::get('appointments/get-doctor-appointment-timings', 'API\AppointmentsController@getDoctorAppointmentTimings');
Route::get('appointments/get-rooms', 'API\AppointmentsController@getRooms');
Route::get('appointments/view/{id}', 'API\AppointmentsController@show');
Route::get('appointments/form-view/{id}', 'API\AppointmentsController@showForm');
Route::get('appointments/user-history/{id}', 'API\AppointmentsController@showHistory');
Route::get('appointments/add-course', 'API\AppointmentsController@addCourse');
Route::post('appointments/get-users-quick-history', 'API\AppointmentsController@UsersQuickHistory');
Route::post('appointments/get-by-course', 'API\AppointmentsController@getByCourse');
Route::post('get-available-doctors', 'API\AppointmentsController@getAvailableDoctors');
Route::post('check-followup', 'API\AppointmentsController@checkFollowup');
Route::get('appointments/get-package-appointments/{id}', 'API\AppointmentsController@getPackageAppointments');
Route::get('appointments/get-call-status/{id}', 'API\AppointmentsController@AppointmentCallData');
Route::post('cancel-appointment', 'API\AppointmentsController@cancelAppointment');
Route::post('called-appointment', 'API\AppointmentsController@calledAppointment');
Route::post('appointments/get-bulk-appointments', 'API\AppointmentsController@getBulkAppointments');
Route::get('get-monthly-appointments/{id}/{type?}', 'API\AppointmentsController@getMonthlyAppointments');
Route::get('get-daily-appointments/{id}', 'API\AppointmentsController@getDailyAppointments');
Route::post('set-insured-treatments', 'API\AppointmentsController@getInsuranceTreatments');
Route::post('get-cash-invoicing-treatments', 'API\AppointmentsController@getCashInvoicingTreatments');
Route::post('transfer-appointment-in-invoice', 'API\AppointmentsController@transferAppointmentInvoice');


Route::post('set-appvInsured-treatments', 'API\AppointmentsController@setInsuranceTreatments');
Route::post('appointments/get-bulk-cash-invoices', 'API\AppointmentsController@getBulkCashAppointments');
Route::post('appointments/get-bulk-insurance-invoices', 'API\AppointmentsController@getBulkInsuranceAppointments');
Route::get('getInsMedListbyApt/{id}', 'API\AppointmentsController@getInsMedListbyApt');
Route::get('getcourseAptListbyApt/{id}', 'API\AppointmentsController@getcourseAptListbyApt');
Route::post('appointments/close-appointment', 'API\AppointmentsController@closeAppointment');
Route::get('get-dashboard-appointments', 'API\AppointmentsController@getDashboardAppointments');
Route::post('appointments/shift-in-course', 'API\AppointmentsController@shiftInCourse');
Route::get('get-todays-active-appointments', 'API\AppointmentsController@getTodaysActiveAppointments');

Route::post('appointments/update-comment', 'API\AppointmentsController@updateComment');
Route::post('ready-appointment', 'API\AppointmentsController@updateReadyStatus');


//// ******** Courses API ********
Route::post('courses/make-course-appointment', 'API\CoursesController@createCourseAppointment');
Route::post('courses/make-schcourse-appointment', 'API\CoursesController@createSchCourseAppointment');
Route::get('courses/create-course/{id}', 'API\CoursesController@create');
Route::get('courses/get-yearwise/{id}', 'API\CoursesController@getYearwise');
Route::get('courses/get-direct-yearwise/{id}', 'API\CoursesController@getDirectYearwise');
Route::get('courses/get-packs-yearwise/{id}', 'API\CoursesController@getPacksYearwise');
Route::post('courses/create-schedule-package/{id}', 'API\CoursesController@createSchedulePackage');
Route::post('courses/create-course', 'API\CoursesController@createCourse');
Route::post('courses/create-direct-course', 'API\CoursesController@createDirectCourse');
Route::get('courses/view-direct-course/{id}', 'API\CoursesController@showDirect');
Route::post('courses/approve-invoice', 'API\CoursesController@approveCourseInvoice');
Route::post('courses/reapprove-invoice', 'API\CoursesController@reapproveCourseInvoice');
Route::post('courses/make-cash-course', 'API\CoursesController@makeCashCourse');
Route::get('courses/get-package-detail/{id}', 'API\CoursesController@getPackDetail');
Route::post('courses/create-preapproval-course', 'API\CoursesController@createPreapprovalCourse');
Route::post('courses/close-course', 'API\CoursesController@closeCourse');
Route::post('courses/shift-course', 'API\CoursesController@shiftCourse');
Route::post('courses/check-course', 'API\CoursesController@checkCourse');
Route::post('delete-course', 'API\CoursesController@destroy');
Route::get('courses/get-patient-history-yearwise/{id}/{year}', 'API\CoursesController@getPatientHistoryYearwise');
Route::post('update-end-date', 'API\CoursesController@updateEndDate');
Route::get('get-course-treatments-only/{aid}/{cid}/{iid}', 'API\CoursesController@getOnlyTreatments');
Route::get('course-package/{id}', 'API\CoursesController@CoursePackage');

//// ******** Availabolity API ********
Route::post('availability/create-absent', 'API\AvailabilitiesController@makeAbsent');
Route::post('availability/create-absent-by-admin', 'API\AvailabilitiesController@makeAbsentByAdmin');
Route::get('get-monthly-availabilities/{id}/{did}', 'API\AvailabilitiesController@getMonthlyAvailabilities');
Route::post('get-day-shifts', 'API\AvailabilitiesController@getDayShifts');
Route::post('availability/modify-slot', 'API\AvailabilitiesController@modifySlots');
Route::post('availability/create-slot', 'API\AvailabilitiesController@createSlots');


//// ******** Stocks API ********
Route::get('stocks/by-appointment/{id}', 'API\StocksController@byAppointment');
Route::get('stocks/stock-by-id/{id}', 'API\StocksController@showById');
Route::get('stocks/get-list-by-medicine/{id}', 'API\StocksController@getListByMedicine');
Route::get('stocks/get-expiring-by-medicine/{id}', 'API\StocksController@getExpiringByMedicine');
Route::get('stocks/get-medicine-stocks/{id}', 'API\StocksController@getMedicineStocks');
Route::post('stocks/get-insured-expiring-by-medicine', 'API\StocksController@getInsuredExpiringByMedicine');
Route::get('get-stocks-by-purchase/{id}', 'API\StocksController@getPurchaseStocks');
Route::post('stocks/get-stock-ledger', 'API\StocksController@getStockLedger');
Route::get('stocks/get-sale-stock-ledger/{id}', 'API\StocksController@getSaleStockLedger');
Route::get('stockstatus/{id}', 'API\StocksController@getStockStatus');

//// ******** Sales API ********
Route::post('sales/create', 'API\SalesController@store');
Route::get('sales/view/{id}', 'API\SalesController@show');


//// ******** Timing slots ********
Route::get('get-time-slots', 'API\CommonController@getTimeSlots');
Route::get('get-all-time-slots', 'API\CommonController@getAllTimeSlots');
Route::get('get-on-time-slots', 'API\TimeSlotsController@getOnTimeSlots');
Route::get('get-off-time-slots', 'API\TimeSlotsController@getOffTimeSlots');
Route::post('modify-slots', 'API\TimeSlotsController@modifySlots');
Route::get('getBothSlotsList', 'API\TimeSlotsController@getBothList');


//// ******** Common Call ********
Route::post('get-consult-mapped-insurances', 'API\CommonController@getConsultMappedInsurances');
Route::post('get-treatment-mapped-insurances', 'API\CommonController@getTreatmentMappedInsurances');
Route::post('get-medicine-mapped-insurances', 'API\CommonController@getMedicineMappedInsurances');
Route::get('get-configs', 'API\CommonController@getConfigs');
Route::post('check-consult-mapping', 'API\CommonController@checkConsultMapping');
Route::post('check-treatment-mapping', 'API\CommonController@checkTreatmentMapping');
Route::post('check-medicine-mapping', 'API\CommonController@checkMedicineMapping');
Route::get('get-course-discounts', 'API\CommonController@getCourseDiscounts');
Route::get('get-course-form-discounts', 'API\CommonController@getCourseFormDiscounts');
Route::post('update-course-form-discounts', 'API\OptionsController@updateCourseFormDiscounts');


//// ***********  Search links  ************** /////
Route::get('findInsurances', 'API\InsurancesController@search');
Route::get('findCustomer', 'API\UsersController@search');
Route::post('find-customer', 'API\UsersController@csearch');
Route::post('find-merger-customers', 'API\UsersController@mergerSearch');
Route::get('findMedicine', 'API\MedicinesController@search');
Route::post('findTreatments', 'API\TreatmentsController@search');
Route::post('findStock', 'API\StocksController@search');
Route::post('findTodaysAppointment', 'API\AppointmentsController@search');
Route::post('findUpcomingAppointment', 'API\AppointmentsController@upcomingSearch');
Route::post('findHistoryAppointment', 'API\AppointmentsController@historySearch');
Route::post('findDrHistoryAppointment', 'API\AppointmentsController@drHistorySearch');
Route::post('search/menu-search', 'API\SearchController@menuSearch');
Route::get('findDpInvoice/{id}', 'API\InvoicesController@dpsearch');
Route::post('findAppInvoices', 'API\InvoicesController@searchAppInvoices');
Route::post('findPrescbiredCourses', 'API\CoursesController@prescribedSearch');
Route::post('findDirectCourses', 'API\CoursesController@directSearch');
Route::post('findSchedulesCourses', 'API\CoursesController@packagedSearch');
Route::post('findCourseInvoices', 'API\InvoicesController@searchCourseInvoices');
Route::post('findPurchases', 'API\PurchasesController@search');
Route::post('findPharmacyInvoices', 'API\InvoicesController@searchPharmacyInvoices');
Route::post('findDPharmacyInvoices', 'API\InvoicesController@searchDPharmacyInvoices');
Route::post('findSettlements', 'API\SettlementsController@search');
Route::post('findCumulativeInvoices', 'API\InvoicesController@searchAll');

//// ***********  Invoices API  ************** /////
Route::post('invoices/create', 'API\InvoicesController@store');
Route::post('invoices/modify', 'API\InvoicesController@modify');
Route::post('invoices/icmodify', 'API\InvoicesController@icmodify');
Route::post('invoices/create-course-invoice', 'API\InvoicesController@storeCourseInvoice');
Route::post('invoices/create-schedule-course-invoice', 'API\InvoicesController@storeScheduleCourseInvoice');
Route::post('invoices/create-ischedule-course-invoice', 'API\InvoicesController@storeIScheduleCourseInvoice');
Route::get('invoices/view/{id}', 'API\InvoicesController@show');
Route::get('invoices/get-invoices/{id}', 'API\InvoicesController@allInvoices');
Route::get('invoices/get-pharmacy-invoices/{id}', 'API\InvoicesController@pharmacyInvoices');
Route::get('invoices/get-dpharmacy-invoices/{id}', 'API\InvoicesController@dPharmacyInvoices');
Route::get('invoices/get-course-invoices/{id}', 'API\InvoicesController@courseInvoices');
Route::get('invoices/get-ui-invoices/{id}', 'API\InvoicesController@uiInvoices');
Route::post('invoices/update-pharmacy-invoice', 'API\InvoicesController@updatePharmacyInvoice');
Route::post('invoices/update-inpharmacy-invoice', 'API\InvoicesController@updateInPharmacyInvoice');
Route::post('invoices/create-direct-invoice', 'API\InvoicesController@createDirectInvoice');
Route::post('invoices/create-new-direct-invoice', 'API\InvoicesController@createNewDirectInvoice');
Route::post('invoices/update-cash-course-invoice', 'API\InvoicesController@updateCashCourseInvoice');
Route::post('invoices/update-insurance-course-invoice', 'API\InvoicesController@updateInsuranceCourseInvoice');
Route::get('invoices/get-patient-history-yearwise/{id}/{year}', 'API\InvoicesController@getPatientHistoryYearwise');

Route::any('invoice-settlement/', 'API\SettlementsController@invoiceSettlement');
Route::any('all-invoice-settlement', 'API\SettlementsController@allInvoiceSettlement');
Route::any('invoice-settlement-rollback', 'API\SettlementsController@invoiceSettlementRollBack');

Route::post('submit-invoice-payment', 'API\InvoicesController@addPayment');
Route::post('cancel-invoice', 'API\InvoicesController@cancelInvoice');
Route::post('cancel-pharmacy-invoice', 'API\InvoicesController@cancelPharmacyInvoice');
Route::post('get-invoices-bycourses', 'API\InvoicesController@getInvoicesBycourses');
Route::post('get-invoices-by-insurance', 'API\InvoicesController@getInvoicesForSettlement');


Route::post('update-password', 'API\ProfileController@updatePassword');
Route::post('users/merge-records', 'API\UsersController@mergeRecords');
Route::post('send-feedback-sms', 'API\CommonController@sendFeedbackSMS');

/// Admin Dashboard API ////////
Route::get('get-dashboard-score', 'API\DashboardController@getDashboardScore');
Route::get('get-admin-score', 'API\DashboardController@getAdminDashboardScore');
Route::get('get-dr-score', 'API\DashboardController@getDrDashboardScore');
Route::get('get-admin-synops', 'API\DashboardController@getAdminDashboardSynops');
Route::post('admin-reports', 'API\AdminsController@updatePermission');
Route::post('get-reports-data', 'API\ReportsController@index');

/// New URLs
Route::post('create-dummy-appointment', 'API\NewAppointmentController@createDummyAppointments');


// Analytis
Route::get('analytics-top-medicines', 'API\AnalyticsController@getTopModicines');
Route::get('analytics-top-treatments', 'API\AnalyticsController@getTopTreatments');
Route::get('analytics-revenue-data', 'API\AnalyticsController@revenueData');
Route::get('pharmacy-revenue-data', 'API\AnalyticsController@pharmacyRevenueData');
Route::get('consult-revenue-data', 'API\AnalyticsController@consultRevenueData');
Route::get('treatment-revenue-data', 'API\AnalyticsController@treatmentRevenueData');
Route::get('low-business-data', 'API\AnalyticsController@lowBusinessData');
Route::get('high-business-data', 'API\AnalyticsController@highBusinessData');

/// User App API /////
Route::post('user-login', 'API\UserLoginController@index')->middleware('userauthlogin');
Route::post('user-mobile-login', 'API\UserLoginController@mobileLogin')->middleware('userauthlogin');
Route::post('user-forget-password-otp', 'API\UserLoginController@forgotPasswordOtp')->middleware('userauthlogin');
Route::post('user-reset-password', 'API\UserLoginController@updatePassword')->middleware('userauthlogin');
Route::post('user-registration', 'API\UserLoginController@register')->middleware('userauthlogin');
Route::post('user-web-registration', 'API\UserLoginController@webRegister')->middleware('userauthlogin');
Route::post('user-otp-check', 'API\UserLoginController@OtpCheckForRegister')->middleware('userauthlogin');
Route::post('get-nationalities', 'API\UserLoginController@getNationalities')->middleware('userauthlogin');
Route::post('get-reg-insurances', 'API\FrontController@getInsurances')->middleware('userauthlogin');
Route::post('search-customer', 'API\UserLoginController@findCustomer')->middleware('userauthlogin');
Route::post('livetrack', 'API\TrackerController@index')->middleware('userauthlogin');
Route::post('prescription-data/{id}', 'API\PrintController@prescription')->middleware('userauthlogin');
Route::post('dpinvoice-data/{id}', 'API\PrintController@dpinvoice')->middleware('userauthlogin');
Route::post('uctinvoice-data/{id}', 'API\PrintController@uctinvoice')->middleware('userauthlogin');

Route::post('switch-admin', 'Auth\LoginController@switchAdmin');

// Invoice Payments
Route::post('invoice-payments', 'API\InvoicePaymentsController@index');
Route::post('cancel-invoice-payment', 'API\InvoicePaymentsController@calcel');
Route::post('add-invoice-payment', 'API\InvoicePaymentsController@store');


Route::middleware(['userauth'])->group(function () {
	Route::post('user-account-switch', 'API\FrontController@switchLogin');
    Route::post('user-dashboard', 'API\FrontController@dashboard');
    Route::post('user-data', 'API\FrontController@data');
    Route::post('user-appointments', 'API\FrontController@appointments');
    Route::post('user-view-appointment', 'API\FrontController@viewAppointment');
    Route::post('user-update-profile-pic', 'API\FrontController@updateProfilePic');
    Route::post('user-complete-profile-update', 'API\FrontController@updateCompleteProfile');
    Route::post('user-update-profile', 'API\FrontController@updateProfile');
    Route::post('user-update-insurance', 'API\FrontController@updateInsurance');
    Route::post('user-update-document', 'API\FrontController@updateDocument');
    Route::post('user-post-appointment', 'API\FrontController@postAppointment');
    Route::post('user-post-query', 'API\FrontController@postQuery');
    Route::post('get-insurances', 'API\FrontController@getInsurances');
	Route::post('get-locations', 'API\FrontController@getLocations');

	Route::post('send-email-invoice', 'API\FrontController@emailInvoice');
	Route::post('send-prescription-email', 'API\FrontController@emailPrescription');

    Route::post('change-password', 'API\FrontController@changePassword');
    Route::post('toggle-notification', 'API\FrontController@toggleNotification');
    Route::post('update-token', 'API\FrontController@updateToken');
});

