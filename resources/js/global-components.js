import Vue from 'vue'

// Dashboard component
Vue.component('analytics-dashboard-component', require('./components/Dashboard/Analytics.vue').default);
Vue.component('admin-dashboard-component', require('./components/Dashboard/Admin.vue').default);
Vue.component('doctor-dashboard-component', require('./components/Dashboard/Doctors.vue').default);
Vue.component('manager-dashboard-component', require('./components/Dashboard/ClinicManager.vue').default);
Vue.component('reception-dashboard-component', require('./components/Dashboard/Reception.vue').default);
Vue.component('therapists-dashboard-component', require('./components/Dashboard/Therapists.vue').default);
Vue.component('accounts-dashboard-component', require('./components/Dashboard/Accounts.vue').default);
Vue.component('director-dashboard-component', require('./components/Dashboard/Director.vue').default);

// Account Component
Vue.component('accounts-component', require('./components/Accounts/Index.vue').default);
Vue.component('accounts-categories-component', require('./components/Accounts/Categories.vue').default);

// Appointment Components
Vue.component('appointment-upcoming-component', require('./components/Appointments/Index.vue').default);
Vue.component('appointment-requests-component', require('./components/Appointments/Requests.vue').default);
Vue.component('appointment-day-schedule-component', require('./components/Appointments/DaySchedule.vue').default);
Vue.component('appointment-history-component', require('./components/Appointments/History.vue').default);
Vue.component('appointment-view-component', require('./components/Appointments/View.vue').default);

// Courses Components
Vue.component('courses-component', require('./components/Appointments/Courses.vue').default);
Vue.component('direct-courses-component', require('./components/Appointments/DirectCourses.vue').default);
Vue.component('course-packages-component', require('./components/Appointments/Packages.vue').default);
Vue.component('course-packages-view-component', require('./components/Appointments/ViewPackages.vue').default);
Vue.component('schedule-package-component', require('./components/Appointments/SchedulePackage.vue').default);
Vue.component('courses-view-component', require('./components/Appointments/ViewCourse.vue').default);
Vue.component('courses-cashinvoice-component', require('./components/Appointments/CourseCashInvoice.vue').default);
Vue.component('courses-direct-view-component', require('./components/Appointments/ViewDirectCourse.vue').default);
Vue.component('course-print-acknowledgement-component', require('./components/Appointments/PrintAcknowledgement.vue').default);
Vue.component('dcourse-print-acknowledgement-component', require('./components/Appointments/PrintDirAcknowledgement.vue').default);
Vue.component('course-print-package-acknowledgement-component', require('./components/Appointments/PrintPackageAcknowledgement.vue').default );

// Invoice Components
Vue.component('invoices-appointment-component', require('./components/Invoices/Appointments.vue').default);
Vue.component('invoices-courses-component', require('./components/Invoices/Courses.vue').default);
Vue.component('invoices-pharmacy-component', require('./components/Invoices/Pharmacy.vue').default);
Vue.component('invoices-edit-component', require('./components/Invoices/EditInvoice.vue').default);
Vue.component('invoices-course-edit-component', require('./components/Invoices/EditCourseInvoice.vue').default);
Vue.component('invoices-ccourse-edit-component', require('./components/Invoices/EditCourseCInvoice.vue').default);
Vue.component('invoices-edit-pharmacy-component', require('./components/Invoices/EditPharmacyInvoice.vue').default);
Vue.component('invoices-edit-dpharmacy-component', require('./components/Invoices/EditDPharmacyInvoice.vue').default);
Vue.component('invoices-add-pharmacy-component', require('./components/Invoices/EditPharmacyAInvoice.vue').default);
Vue.component('invoices-direct-pharmacy-component', require('./components/Invoices/DirectPharmacyInvoices.vue').default);
Vue.component('invoices-direct-component', require('./components/Invoices/DirectInvoices.vue').default);
Vue.component('preapproval-courses-invoice-component', require('./components/Appointments/PreapprovalCourseInvoice.vue').default);
Vue.component('appointment-complete-invoice-component', require('./components/Appointments/CompleteInvoice.vue').default);

// Print Components
Vue.component('print-cform-component', require('./components/Print/Cform.vue').default);
Vue.component('print-prescription-component', require('./components/Appointments/PrintPrescription.vue').default);
Vue.component('invoices-print-component', require('./components/Invoices/PrintInvoice.vue').default);

// Pharmacy Components
Vue.component('stock-status-component', require('./components/Pharmacy/StockStatus.vue').default);
Vue.component('pharmacy-suppliers-component', require('./components/Pharmacy/Suppliers.vue').default);
Vue.component('pharmacy-categories-component', require('./components/Pharmacy/Categories.vue').default);
Vue.component('pharmacy-medicines-component', require('./components/Pharmacy/Medicines.vue').default);
Vue.component('pharmacy-stocks-component', require('./components/Pharmacy/Stocks.vue').default);
Vue.component('pharmacy-purchases-component', require('./components/Pharmacy/Purchases.vue').default);
Vue.component('pharmacy-add-purchase-component', require('./components/Pharmacy/AddPurchase.vue').default);
Vue.component('pharmacy-view-purchase-component', require('./components/Pharmacy/ViewPurchase.vue').default);
Vue.component('pharmacy-manufacturers-component', require('./components/Pharmacy/Manufacturers.vue').default);
Vue.component('pharmacy-ledger-component', require('./components/Pharmacy/Ledger.vue').default);

// Element Components
Vue.component('elements-doctors-component', require('./components/Elements/Doctors.vue').default);
Vue.component('elements-doctors-availability-component', require('./components/Elements/DoctorsAvailability.vue').default);
Vue.component('elements-rooms-component', require('./components/Elements/Rooms.vue').default);
Vue.component('elements-treatments-component', require('./components/Elements/Treatments.vue').default);
Vue.component('elements-packages-component',require('./components/Elements/Packages.vue').default);
Vue.component('doctors-appointments-day-schedule', require('./components/Doctors/DaySchedule.vue').default);
Vue.component('doctors-prescribe', require('./components/Doctors/Prescribe.vue').default);
Vue.component('doctors-view-appointment', require('./components/Doctors/ViewAppointment.vue').default);
Vue.component('doctors-appointments-upcoming-list', require('./components/Doctors/Upcomings.vue').default);
Vue.component('doctors-appointments-history', require('./components/Doctors/History.vue').default);
Vue.component('doctors-availability', require('./components/Doctors/Availability.vue').default);
Vue.component('doctors-availability-list', require('./components/Doctors/AvailabilityList.vue').default);
Vue.component('doctors-reports-appointments', require('./components/Doctors/ReportAppointments.vue').default);

// Asset Components
Vue.component('assets-diagnosis-component', require('./components/Assets/Diagnosis.vue').default);
Vue.component('assets-diseases-component', require('./components/Assets/Diseases.vue').default);
Vue.component('assets-labcategories-component', require('./components/Assets/LabCategories.vue').default);
Vue.component('assets-oecategories-component', require('./components/Assets/OeCategories.vue').default);
Vue.component('assets-laboratories-component', require('./components/Assets/Laboratories.vue').default);
Vue.component('assets-symptoms-component', require('./components/Assets/Symptoms.vue').default);
Vue.component('assets-xrays-component', require('./components/Assets/Xrays.vue').default);
Vue.component('assets-comments-component', require('./components/Assets/Comments.vue').default);

// Customer Components
Vue.component('appointment-history', require('./components/Custom/Users/AppointmentHistory.vue').default);
Vue.component('course-history', require('./components/Custom/Users/CoursesHistory.vue').default);
Vue.component('invoice-history', require('./components/Custom/users/InvoicesHistory.vue').default);
Vue.component('customers-add-component', require('./components/Customers/Add.vue').default);
Vue.component('customers-edit-component', require('./components/Customers/Edit.vue').default);
Vue.component('customers-component', require('./components/Customers/Index.vue').default);
Vue.component('customers-view-component', require('./components/Customers/View.vue').default);
Vue.component('customers-mnerging-component', require('./components/Customers/Merger.vue').default)

// Report Components
Vue.component('reports-component', require('./components/Reports.vue').default);

// Settings Components
//Vue.component('settings-software-details-component', require('./components/Settings/Info.vue').default);
Vue.component('settings-financial-years-component', require('./components/Settings/FinancialYears.vue').default);
//Vue.component('settings-locations-component', require('./components/Settings/Locations.vue').default);
Vue.component('settings-timeslots-component', require('./components/Settings/Timeslots.vue').default);
Vue.component('settings-configurations-component', require('./components/Settings/Configurations.vue').default);
Vue.component('settings-discounts-component', require('./components/Settings/Discounts.vue').default);
//Vue.component('settings-auth-tokens-component', require('./components/Settings/AuthTokens.vue').default);
Vue.component('settings-profile-component', require('./components/Settings/Profile.vue').default);


// Search Components
Vue.component('vue-search', require('./components/Custom/Search.vue').default);
//Vue.component('search-component', require('./components/Search.vue').default);

// Custom Components
Vue.component('appointment-creater-component', require('./components/Custom/Appointment.vue').default);
Vue.component('availability-calendar', require('./components/Custom/AvailabilityCalendar.vue').default);
Vue.component('availability-admin-calendar', require('./components/Custom/AvailabilityAdminCalendar.vue').default);
Vue.component('appointment-calendar', require('./components/Custom/AppointmentCalendar.vue').default);
Vue.component('dr-appointment-calendar', require('./components/Custom/DrAppointmentCalendar.vue').default);