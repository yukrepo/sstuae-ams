<?php

use App\Events\AppointmentCreated;
use App\Models\Doctors;

Route::get('/', function () { return view('welcome');})->middleware('auth');
Route::get('/updateslots', 'HomeController@makeSlots')->middleware('auth');
Route::get('/update-payment', 'HomeController@updatePayment')->middleware('auth');
Route::get('/crone/morning-sms', 'CroneController@morningSms');
Route::get('/crone/evening-sms', 'CroneController@eveningSms');
/*
Route::get('/fired', function () {
    event(new AppointmentCreated);
    return 'fired';
});
  */
Auth::routes();
Route::post('/switch-admin', 'HomeController@switchAdmin');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () { return view('welcome');});
    Route::get('/home', function () { return view('welcome');});

    Route::get('print/consent-form/{id}/{lng}',  function () { return view('print.cform'); })->defaults('menu', 'customers');


    Route::get('/emailview', function () { return view('email.prescription_mail')->with('apcode', 'S1200212')->with('name', 'Ankit kumar')->with('link', '#');});

    // Search URLs
    Route::get('search',  function () { return view('search.index'); })->defaults('menu', 'search');
    Route::get('dashboard',  function () { return view('profile.dashboard'); })->defaults('menu', 'dashboard')->defaults('slug', 'Summary');
    Route::get('dashboard/analytics',  function () { return view('profile.dashboard'); })->defaults('menu', 'dashboard')->defaults('slug', 'analytics');

    // Customers URLs
    Route::get('accounts/all',  function () { return view('accounts.index'); })->defaults('menu', 'accounts')->defaults('slug', 'accounts');
    Route::get('accounts/categories',  function () { return view('accounts.categories'); })->defaults('menu', 'accounts')->defaults('slug', 'addaccount');

    // Customers URLs
    Route::get('customers/all',  function () { return view('customers.index'); })->defaults('menu', 'customers')->defaults('slug', 'customer-list');
    Route::get('customers/merging',  function () { return view('customers.merging'); })->defaults('menu', 'customers')->defaults('slug', 'customer-merging');
    Route::get('customers/add',  function () { return view('customers.add'); })->defaults('menu', 'customers')->defaults('slug', 'add-customer');
    Route::get('customers/edit/{id}',  function () { return view('customers.edit'); })->defaults('menu', 'customers')->defaults('slug', 'customer-list');
    Route::get('customers/view/{id}',  function () { return view('customers.view'); })->defaults('menu', 'customers')->defaults('slug', 'customer-list');

    // Asset URLs
    Route::get('assets/diagnosis',  function () { return view('assets.diagnosis'); })->defaults('menu', 'assets')->defaults('slug', 'diagnosis');
    Route::get('assets/lab-categories',  function () { return view('assets.labcategories'); })->defaults('menu', 'assets')->defaults('slug', 'labcategories');
    Route::get('assets/laboratories',  function () { return view('assets.laboratories'); })->defaults('menu', 'assets')->defaults('slug', 'laboratories');
    Route::get('assets/oe-categories',  function () { return view('assets.oecategories'); })->defaults('menu', 'assets')->defaults('slug', 'oecategories');
    Route::get('assets/symptoms',  function () { return view('assets.symptoms'); })->defaults('menu', 'assets')->defaults('slug', 'symptoms');
    Route::get('assets/xrays',  function () { return view('assets.xrays'); })->defaults('menu', 'assets')->defaults('slug', 'xrays');
    Route::get('assets/comments',  function () { return view('assets.comments'); })->defaults('menu', 'assets')->defaults('slug', 'comments');

    // Elements URLs
    Route::get('elements/insurances',  function () { return view('modules.insurances'); })->defaults('menu', 'elements')->defaults('slug', 'insurances');
    Route::get('elements/doctors',  function () { return view('modules.doctors'); })->defaults('menu', 'elements')->defaults('slug', 'doctors');
    Route::get('elements/doctors-availability/{id}',  function ($id) {
            $doctor = Doctors::where('id', $id)->get()->first();
            return view('modules.doctors_availability')->with(compact('doctor'));
        })->defaults('menu', 'elements')->defaults('slug', 'doctors');

    Route::get('elements/rooms',  function () { return view('modules.rooms'); })->defaults('menu', 'elements')->defaults('slug', 'rooms');
    Route::get('elements/treatments',  function () { return view('modules.treatments'); })->defaults('menu', 'elements')->defaults('slug', 'treatments');
    Route::get('elements/packages',  function () { return view('modules.packages'); })->defaults('menu', 'elements')->defaults('slug', 'packages');

    // Pharmacy URLs
    Route::get('pharmacy/categories',  function () { return view('pharmacy.categories'); })->defaults('menu', 'pharmacy')->defaults('slug', 'categories');
    Route::get('pharmacy/ledger',  function () { return view('pharmacy.ledger'); })->defaults('menu', 'pharmacy')->defaults('slug', 'ledger');
    Route::get('pharmacy/medicines',  function () { return view('pharmacy.medicines'); })->defaults('menu', 'pharmacy')->defaults('slug', 'medicines');
    Route::get('pharmacy/stocks',  function () { return view('pharmacy.stocks'); })->defaults('menu', 'pharmacy')->defaults('slug', 'stocks');
    Route::get('pharmacy/stock-status',  function () { return view('pharmacy.stockstatus'); })->defaults('menu', 'pharmacy')->defaults('slug', 'stockstatus');
    Route::get('pharmacy/purchases',  function () { return view('pharmacy.purchases'); })->defaults('menu', 'pharmacy')->defaults('slug', 'purchases');
    Route::get('pharmacy/add-purchase',  function () { return view('pharmacy.add_purchase'); })->defaults('menu', 'pharmacy')->defaults('slug', 'purchases');
    Route::get('pharmacy/view-purchase/{id}',  function () { return view('pharmacy.view_purchase'); })->defaults('menu', 'pharmacy')->defaults('slug', 'purchases');
    Route::get('pharmacy/suppliers',  function () { return view('pharmacy.suppliers'); })->defaults('menu', 'pharmacy')->defaults('slug', 'suppliers');
    Route::get('pharmacy/manufacturers',  function () { return view('pharmacy.manufacturers'); })->defaults('menu', 'pharmacy')->defaults('slug', 'manufacturers');

    // Settings URLs
    Route::get('settings/configurations',  function () { return view('settings.configs'); })->defaults('menu', 'settings')->defaults('slug', 'configurations');
    Route::get('settings/locations',  function () { return view('settings.locations'); })->defaults('menu', 'settings')->defaults('slug', 'locations');
    Route::get('settings/discounts',  function () { return view('settings.discounts'); })->defaults('menu', 'settings')->defaults('slug', 'discounts');
    Route::get('settings/timeslots',  function () { return view('settings.timeslots'); })->defaults('menu', 'settings')->defaults('slug', 'timeslots');
    Route::get('settings/financial-years',  function () { return view('settings.years'); })->defaults('menu', 'settings')->defaults('slug', 'financial');
    Route::get('settings/auth-tokens',  function () { return view('settings.tokens'); })->defaults('menu', 'settings')->defaults('slug', 'auth');
    Route::get('settings/profile',  function () { return view('settings.settings'); })->defaults('menu', 'dashboard');

    // Appointments URLs
    Route::get('appointments/requests',  function () { return view('appointments.requests'); })->defaults('menu', 'appointments')->defaults('slug', 'arequest');
    Route::get('appointments/calendar',  function () { return view('appointments.calendar'); })->defaults('menu', 'appointments')->defaults('slug', 'calendar');
    Route::get('appointments/day-schedule',  function () { return view('appointments.day_schedule'); })->defaults('menu', 'appointments')->defaults('slug', 'today');
    Route::get('appointments/view/{id}',  function () { return view('appointments.view'); })->defaults('menu', 'appointments');
    Route::get('appointments/history/{id}',  function () { return view('appointments.history'); })->defaults('menu', 'appointments')->defaults('slug', 'history');
    Route::get('appointments/upcoming-list/{id}',  function () { return view('appointments.upcomings'); })->defaults('menu', 'appointments')->defaults('slug', 'upcoming');
    // Courses URLs
    Route::get('appointments/courses/{id}',  function () { return view('appointments.courses'); })->defaults('menu', 'appointments')->defaults('slug', 'courses');
    Route::get('appointments/course-packages/{id}',  function () { return view('appointments.pcourses'); })->defaults('menu', 'appointments')->defaults('slug', 'packages');
    Route::get('appointments/direct-courses/{id}',  function () { return view('appointments.dcourses'); })->defaults('menu', 'appointments')->defaults('slug', 'directs');
    Route::get('appointments/courses/view/{id}',  function () { return view('appointments.courses_view'); })->defaults('menu', 'appointments')->defaults('slug', 'courses');
    Route::get('appointments/course-packages/view/{id}',  function () { return view('appointments.pcourses_view'); })->defaults('menu', 'appointments')->defaults('slug', 'packages');
    Route::get('appointments/courses/direct-view/{id}',  function () { return view('appointments.dcourses_view'); })->defaults('menu', 'appointments')->defaults('slug', 'directs');
    Route::get('appointments/schedule-package/{id}',  function () { return view('appointments.schedule_package'); })->defaults('menu', 'appointments')->defaults('slug', 'packages');
    Route::get('appointments/print-acknowledgement/{id}',  function () { return view('appointments.print_acknowledgement'); })->defaults('menu', 'appointments');
    Route::get('appointments/print-dacknowledgement/{id}',  function () { return view('appointments.print_dacknowledgement'); })->defaults('menu', 'appointments');
    Route::get('appointments/print-package-acknowledgement/{id}',  function () { return view('appointments.print_packnowledgement'); })->defaults('menu', 'appointments');
    Route::get('appointments/print-perscription/{id}',  function () { return view('appointments.print_prescription'); })->defaults('menu', 'appointments');
    Route::get('appointments/complete-cash-invoice/{id}',  function () { return view('appointments.course_invoice'); })->defaults('menu', 'appointments');
    Route::get('appointments/complete-invoice/{id}',  function () { return view('appointments.complete_invoice'); })->defaults('menu', 'appointments');
    Route::get('appointments/set-cash-invoice/{id}',  function () { return view('appointments.new_course_invoice'); })->defaults('menu', 'appointments');
    Route::get('appointments/preapproval-course-invoice/{id}',  function () { return view('appointments.preapproval_courseinvoice'); })->defaults('menu', 'appointments');

    // Doctors Login URLs

    Route::get('doctors/availability',  function () { return view('doctors.availability'); })->defaults('menu', 'availability');
    Route::get('doctors/appointments-calendar',  function () { return view('doctors.calendar'); })->defaults('menu', 'pappointments');
    Route::get('doctors/appointments-day-schedule',  function () { return view('doctors.day_schedule'); })->defaults('menu', 'pappointments');
    Route::get('doctors/appointments-upcoming-list/{id}',  function () { return view('doctors.upcomings'); })->defaults('menu', 'pappointments');
    Route::get('doctors/appointments-history/{id}',  function () { return view('doctors.history'); })->defaults('menu', 'pappointments');
    Route::get('doctors/view-appointment/{id}',  function () { return view('doctors.view'); })->defaults('menu', 'pappointments');
    Route::get('doctors/prescribe/{id}',  function () { return view('doctors.prescribe'); })->defaults('menu', 'pappointments');
    Route::get('doctors/reports',  function () { return view('doctors.report'); })->defaults('menu', 'reports');

    // Invoice URLs

    Route::get('invoices/appointments/{id}', function() { return view('invoices.appointments'); })->defaults('menu', 'invoices')->defaults('slug', 'appointments');
    Route::get('invoices/edit-invoice/{id}', function() { return view('invoices.edit_invoice'); })->defaults('menu', 'invoices')->defaults('slug', 'appointments');
    Route::get('invoices/print/{id}', function() { return view('invoices.print'); })->defaults('menu', 'invoices')->defaults('slug', 'appointments');
    
    Route::get('invoices/courses/{id}', function() { return view('invoices.courses'); })->defaults('menu', 'invoices')->defaults('slug', 'courses');
    Route::get('invoices/edit-course-invoice/{id}', function() { return view('invoices.edit_course_invoice'); })->defaults('menu', 'invoices')->defaults('slug', 'appointments');
    Route::get('invoices/edit-course-cinvoice/{id}', function() { return view('invoices.edit_course_cinvoice'); })->defaults('menu', 'invoices')->defaults('slug', 'appointments');

    Route::get('invoices/pharmacy/{id}', function() { return view('invoices.pharmacy'); })->defaults('menu', 'invoices')->defaults('slug', 'pharmacy');
    Route::get('invoices/edit-pharmacy-rinvoice/{id}', function() { return view('invoices.edit_pinvoice'); })->defaults('menu', 'invoices')->defaults('slug', 'pharmacy');
    Route::get('invoices/edit-pharmacy-ainvoice/{id}', function() { return view('invoices.add_pinvoice'); })->defaults('menu', 'invoices')->defaults('slug', 'pharmacy');
  


    Route::get('invoices/direct-pharmacy-invoices/{id}', function() { return view('invoices.direct'); })->defaults('menu', 'invoices')->defaults('slug', 'direct');
    Route::get('invoices/direct-invoices/{id}', function() { return view('invoices.direct'); })->defaults('menu', 'invoices')->defaults('slug', 'directinv');

    Route::get('invoices/edit-pharmacy-invoice/{id}', function() { return view('invoices.dpedit'); })->defaults('menu', 'invoices')->defaults('slug', 'direct');


    Route::get('reports', function() { return view('reports.index'); })->defaults('menu', 'reports')->defaults('slug', 'reports');
});


//Route::get('{path}', 'HomeController@index')->where('path', '([A-z\d-\/_.]+)?')->middleware('auth');
//Route::get('{path}','HomeController@index')->where( 'path', '([A-z\d\-\/_.]+)?' );

Route::get('/import', function() { return view('import');});
Route::get('/runscript', 'ImportController@addusers');

Route::post('/import_parse', 'ImportController@parseImport')->name('import_parse');
Route::post('/import_process', 'ImportController@processImport')->name('import_process');