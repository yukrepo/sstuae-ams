<template>
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel main-nav">
        <router-link class="navbar-brand" to="/" v-on:click.native="setActive('')">
             <img :src="svgurl+'logo.svg'">
        </router-link>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="'Toggle navigation'">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent" >
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto main-nav" v-if="profile.admin_type_id == 1">
                <li class="nav-item dropdown">
                    <router-link class="nav-link" :class="{ active: isActive('dashboard') }"  v-on:click.native="setActive('dashboard')" to="/dashboard">Dashboard</router-link>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('accounts') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Accounts</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('accounts')" to="/accounts/all">All Logins </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('accounts')" to="/accounts/categories">Categories </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('appointments') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Appointments</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="/appointments/requests">Requests </a>
                        <a class="dropdown-item" href="/appointments/calendar">Calendar </a>
                        <a class="dropdown-item" href="/appointments/day-schedule">Todays Appointment </a>
                        <a class="dropdown-item" :href="'/appointments/upcoming-list/'+currentYear">Upcoming Appointments </a>
                        <a class="dropdown-item" :href="'/appointments/history/'+currentYear">History </a>
                        <a class="dropdown-item" :href="'/appointments/courses/'+currentYear">Prescribed Courses </a>
                        <a class="dropdown-item" :href="'/appointments/course-packages/'+currentYear"> Schedule Packages </a>
                        <a class="dropdown-item" :href="'/appointments/direct-courses/'+currentYear"> Direct Courses</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('customers') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Customers</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('customers')" to="/customers/add">Add New Customer</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('customers')" to="/customers/all">Complete List </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('invoices') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Invoices</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/appointments/'+currentYear">Appointment Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/courses/'+currentYear">Courses Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/pharmacy/'+currentYear">Pharmacy Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/direct-pharmacy-invoices/'+currentYear">Direct Pharmacy Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" to="/invoices/reconciliation">Reconciliation </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" to="/invoices/cumulative">cumulative invoices</router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('pharmacy') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Pharmacy</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/categories">Categories </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/medicines">Medicines </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/stocks">Stocks </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/purchases">Purchases </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/suppliers">Suppliers </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/manufacturers">Manufacturers </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('elements') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Elements</a>
                    <div class="dropdown-menu">
                        <router-link to="/elements/insurances" v-on:click.native="setActive('elements')" class="dropdown-item"> Insurances </router-link>
                        <router-link to="/elements/doctors" v-on:click.native="setActive('elements')" class="dropdown-item"> Doctors </router-link>
                        <router-link to="/elements/rooms" v-on:click.native="setActive('elements')" class="dropdown-item"> Rooms </router-link>
                        <router-link to="/elements/treatments" v-on:click.native="setActive('elements')" class="dropdown-item"> Treatments </router-link>
                        <router-link to="/elements/packages" v-on:click.native="setActive('elements')" class="dropdown-item"> Packages </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('assets') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Assets</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('assets')" to="/assets/diagnosis">Diagnosis</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('assets')" to="/assets/lab-categories">Lab Categories</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('assets')" to="/assets/laboratories">Laboratories</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('assets')" to="/assets/oe-categories">OE Categories</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('assets')" to="/assets/symptoms">Symptoms</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('assets')" to="/assets/xrays">Xrays</router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('reports') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Reports</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="/reports/full-reports">Full Reports</a>
                        <a class="dropdown-item" href="/reports/invoices">Invoices Reports</a>
                        <a class="dropdown-item" href="/reports/pharmacy">Pharmacy Reports</a>
                        <a class="dropdown-item" href="/reports/consultation">Consultation Reports</a>
                        <a class="dropdown-item" href="/reports/treatments">Treatment Report</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('settings') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Settings</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('settings')" to="/settings/locations">Locations</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('settings')" to="/settings/auth-tokens">O-Auth Tokens</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('settings')" to="/settings/financial-years">Financial Years</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('settings')" to="/settings/configurations">configurations</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('settings')" to="/settings/discounts">Discounts</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('settings')" to="/settings/timeslots">Timeslots</router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <router-link class="nav-link" :class="{ active: isActive('search') }"  v-on:click.native="setActive('search')" to="/search">Search</router-link>
                </li>
            </ul>

            <ul class="navbar-nav mr-auto main-nav" v-else-if="profile.admin_type_id == 2">
                <li class="nav-item dropdown">
                    <router-link class="nav-link" :class="{ active: isActive('dashboard') }"  v-on:click.native="setActive('dashboard')" to="/dashboard">Dashboard</router-link>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('dappointments') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Personal Appointments</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('dappointments')" to="/doctors/appointments-calendar">Calendar </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('dappointments')" to="/doctors/appointments-day-schedule">Todays Appointment </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('dappointments')" :to="'/doctors/appointments-upcoming-list/'+currentYear">Upcoming Appointments </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('dappointments')" :to="'/doctors/appointments-history/'+currentYear">History </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('appointments') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Clinic Appointments</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('appointments')" to="/appointments/calendar">Calendar </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('appointments')" to="/appointments/day-schedule">Todays Appointment </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('appointments')" :to="'/appointments/upcoming-list/'+currentYear">Upcoming Appointments </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('appointments')" :to="'/appointments/history/'+currentYear">History </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('appointments')" :to="'/appointments/direct-courses/'+currentYear"> Direct Courses</router-link>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('availability') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">availability</a>
                    <div class="dropdown-menu">
                        <router-link to="/doctors/availability/" v-on:click.native="setActive('availability')" class="dropdown-item"> Calendar </router-link>
                       <!--  <router-link to="/doctors/availability-list/" v-on:click.native="setActive('availability')" class="dropdown-item">availability List </router-link> -->
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <router-link class="nav-link" :class="{ active: isActive('reports') }" v-on:click.native="setActive('reports')" to="/doctors/reports-appointments">Appointments Report</router-link>
                </li>
            </ul>

            <ul class="navbar-nav mr-auto main-nav" v-else-if="profile.admin_type_id == 4">
                <li class="nav-item dropdown">
                    <router-link class="nav-link" :class="{ active: isActive('dashboard') }"  v-on:click.native="setActive('dashboard')" to="/dashboard">Dashboard</router-link>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('appointments') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Appointments</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="/appointments/requests">Requests </a>
                        <a class="dropdown-item" href="/appointments/calendar">Calendar </a>
                        <a class="dropdown-item" href="/appointments/day-schedule">Todays Appointment </a>
                        <a class="dropdown-item" :href="'/appointments/upcoming-list/'+currentYear">Upcoming Appointments </a>
                        <a class="dropdown-item" :href="'/appointments/history/'+currentYear">History </a>
                        <a class="dropdown-item" :href="'/appointments/courses/'+currentYear">Prescribed Courses </a>
                        <a class="dropdown-item" :href="'/appointments/course-packages/'+currentYear"> Schedule Packages </a>
                        <a class="dropdown-item" :href="'/appointments/direct-courses/'+currentYear"> Direct Courses</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('customers') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Customers</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('customers')" to="/customers/add">Add New Customer</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('customers')" to="/customers/all">Complete List </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('invoices') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Invoices</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/appointments/'+currentYear">Appointment Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/courses/'+currentYear">Courses Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/pharmacy/'+currentYear">Pharmacy Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/direct-pharmacy-invoices/'+currentYear">Direct Pharmacy Invoices </router-link>
                        <!-- <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" to="/invoices/transactions">Payment Transactions </router-link> -->
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('pharmacy') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Pharmacy</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/categories">Categories </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/medicines">Medicines </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/stocks">Stocks </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/purchases">Purchases </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/suppliers">Suppliers </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/manufacturers">Manufacturers </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('elements') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Elements</a>
                    <div class="dropdown-menu">
                        <router-link to="/elements/insurances" v-on:click.native="setActive('elements')" class="dropdown-item"> Insurances </router-link>
                        <router-link to="/elements/doctors" v-on:click.native="setActive('elements')" class="dropdown-item"> Doctors </router-link>
                        <router-link to="/elements/rooms" v-on:click.native="setActive('elements')" class="dropdown-item"> Rooms </router-link>
                        <router-link to="/elements/treatments" v-on:click.native="setActive('elements')" class="dropdown-item"> Treatments </router-link>
                        <router-link to="/elements/packages" v-on:click.native="setActive('elements')" class="dropdown-item"> Packages </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('reports') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Reports</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="/reports/invoices">Invoices Reports</a>
                        <a class="dropdown-item" href="/reports/pharmacy">Pharmacy Reports</a>
                        <a class="dropdown-item" href="/reports/consultation">Consultation Reports</a>
                        <a class="dropdown-item" href="/reports/treatments">Treatment Report</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('settings') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Settings</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('settings')" to="/settings/discounts">Discounts</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('settings')" to="/settings/timeslots">Timeslots</router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <router-link class="nav-link" :class="{ active: isActive('search') }"  v-on:click.native="setActive('search')" to="/search">Search</router-link>
                </li>
            </ul>

            <ul class="navbar-nav mr-auto main-nav" v-else-if="profile.admin_type_id == 5">
                <li class="nav-item dropdown">
                    <router-link class="nav-link" :class="{ active: isActive('dashboard') }"  v-on:click.native="setActive('dashboard')" to="/dashboard">Dashboard</router-link>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('appointments') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Appointments</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="/appointments/requests">Requests </a>
                        <a class="dropdown-item" href="/appointments/calendar">Calendar </a>
                        <a class="dropdown-item" href="/appointments/day-schedule">Todays Appointment </a>
                        <a class="dropdown-item" :href="'/appointments/upcoming-list/'+currentYear">Upcoming Appointments </a>
                        <a class="dropdown-item" :href="'/appointments/history/'+currentYear">History </a>
                        <a class="dropdown-item" :href="'/appointments/courses/'+currentYear">Prescribed Courses </a>
                        <a class="dropdown-item" :href="'/appointments/course-packages/'+currentYear"> Schedule Packages </a>
                        <a class="dropdown-item" :href="'/appointments/direct-courses/'+currentYear"> Direct Courses</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('customers') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Customers</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('customers')" to="/customers/add">Add New Customer</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('customers')" to="/customers/all">Complete List </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('invoices') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Invoices</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/appointments/'+currentYear">Appointment Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/courses/'+currentYear">Courses Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/pharmacy/'+currentYear">Pharmacy Invoices </router-link>
                         <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/direct-pharmacy-invoices/'+currentYear">Direct Pharmacy Invoices </router-link>
                        <!-- <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" to="/invoices/transactions">Payment Transactions </router-link> -->
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('pharmacy') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Pharmacy</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/categories">Categories </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/medicines">Medicines </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/stocks">Stocks </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('reports') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Reports</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('reports')" to="/reports/invoices">Invoices Reports</router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <router-link class="nav-link" :class="{ active: isActive('search') }"  v-on:click.native="setActive('search')" to="/search">Search</router-link>
                </li>
            </ul>

            <ul class="navbar-nav mr-auto main-nav" v-else-if="profile.admin_type_id == 6">
                <li class="nav-item dropdown">
                    <router-link class="nav-link" :class="{ active: isActive('dashboard') }"  v-on:click.native="setActive('dashboard')" to="/dashboard">Dashboard</router-link>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('appointments') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Appointments</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="/appointments/requests">Requests </a>
                        <a class="dropdown-item" href="/appointments/calendar">Calendar </a>
                        <a class="dropdown-item" href="/appointments/day-schedule">Todays Appointment </a>
                        <a class="dropdown-item" :href="'/appointments/upcoming-list/'+currentYear">Upcoming Appointments </a>
                        <a class="dropdown-item" :href="'/appointments/history/'+currentYear">History </a>
                        <a class="dropdown-item" :href="'/appointments/courses/'+currentYear">Prescribed Courses </a>
                        <a class="dropdown-item" :href="'/appointments/course-packages/'+currentYear"> Schedule Packages </a>
                        <a class="dropdown-item" :href="'/appointments/direct-courses/'+currentYear"> Direct Courses</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('customers') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Customers</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('customers')" to="/customers/add">Add New Customer</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('customers')" to="/customers/all">Complete List </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('invoices') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Invoices</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/appointments/'+currentYear">Appointment Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/courses/'+currentYear">Courses Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/pharmacy/'+currentYear">Pharmacy Invoices </router-link>
                         <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/direct-pharmacy-invoices/'+currentYear">Direct Pharmacy Invoices </router-link>
                        <!-- <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" to="/invoices/transactions">Payment Transactions </router-link> -->
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <router-link class="nav-link" :class="{ active: isActive('search') }"  v-on:click.native="setActive('search')" to="/search">Search</router-link>
                </li>
            </ul>

            <ul class="navbar-nav mr-auto main-nav" v-else-if="profile.admin_type_id == 3">
                <li class="nav-item dropdown">
                    <router-link class="nav-link" :class="{ active: isActive('dashboard') }"  v-on:click.native="setActive('dashboard')" to="/dashboard">Dashboard</router-link>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('customers') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Customers</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('customers')" to="/customers/add">Add New Customer</router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('customers')" to="/customers/all">Complete List </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('invoices') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Invoices</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" to="/invoices/appointments">Appointment Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" to="/invoices/pharmacy">Pharmacy Invoices </router-link>
                         <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/direct-pharmacy-invoices/'+currentYear">Direct Pharmacy Invoices </router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('pharmacy') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Pharmacy</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/categories">Categories </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/medicines">Medicines </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/stocks">Stocks </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('pharmacy')" to="/pharmacy/suppliers">Suppliers </router-link>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('reports') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Reports</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('reports')" to="/reports/pharmacy">Pharmacy Reports</router-link>
                    </div>
                </li>
            </ul>
            <ul class="navbar-nav mr-auto main-nav" v-else-if="profile.admin_type_id == 7">
                <li class="nav-item dropdown">
                    <router-link class="nav-link" :class="{ active: isActive('dashboard') }"  v-on:click.native="setActive('dashboard')" to="/dashboard">Dashboard</router-link>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('invoices') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Invoices</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/appointments/'+currentYear">Appointment Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/courses/'+currentYear">Courses Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/pharmacy/'+currentYear">Pharmacy Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" :to="'/invoices/direct-pharmacy-invoices/'+currentYear">Direct Pharmacy Invoices </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" to="/invoices/reconciliation">Reconciliation </router-link>
                        <router-link class="dropdown-item" v-on:click.native="setActive('invoices')" to="/invoices/cumulative">cumulative invoices</router-link>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" :class="{ active: isActive('reports') }" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Reports</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="/reports/full-reports">Full Reports</a>
                        <a class="dropdown-item" href="/reports/invoices">Invoices Reports</a>
                        <a class="dropdown-item" href="/reports/pharmacy">Pharmacy Reports</a>
                        <a class="dropdown-item" href="/reports/consultation">Consultation Reports</a>
                        <a class="dropdown-item" href="/reports/treatments">Treatment Report</a>
                    </div>
                </li>
            </ul>

            <ul class="navbar-nav mr-auto main-nav" v-else>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto icon-nav">
                <li class="nav-item">
                    <a class="nav-link side-icon"  href="javascript:;" data-toggle="modal" data-target="#sidebarModal">
                        <i class="text-warning fas fa-question-circle"></i>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-uppercase text-white profile-menu" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Welcome {{ profile.name }}</a>
                    <div class="dropdown-menu">
                        <router-link class="dropdown-item" v-on:click.native="setActive('settings')" to="/settings/profile">Profile</router-link>
                        <div role="separator" class="m-0 dropdown-divider"></div>
                        <a class="dropdown-item logout-btn"  href="javascript:;" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-power-off m-r-5"></i> Logout
                        </a>
                    </div>
                </li>
                <!-- <li class="nav-item">
                    <digital-clock :blink="true" />
                </li> -->
               <!--  <li class="nav-item">
                    <a class="nav-link side-icon"  href="javascript:;" data-toggle="modal" data-target="#sidebarModal">
                            <i class="text-warning fas fa-question-circle"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link side-icon"  href="javascript:;" data-toggle="modal" data-target="#logoutModal"><i class="text-danger fas fa-power-off"></i></a>
                </li> -->
                <!-- <li class="nav-item">
                    <a class="nav-link side-icon" href="javascript:;" @click="toggleFullscreen" v-if="isFullscreen == false">
                        <i class="fas fa-expand-arrows-alt text-white"></i>
                    </a>
                    <a class="nav-link side-icon" href="javascript:;" @click="toggleFullscreen" v-else>
                        <i class="fas fa-compress-arrows-alt text-white"></i>
                    </a>
                </li> -->
            </ul>
        </div>
    </nav>
</template>

<script>
export default {
        data() {
            return {
                svgurl: '/svg/',
                activeMenu: '',
                currentYear: new Date().getFullYear(),
                isFullscreen: false,
                currenttime: '',
                profile:''
            }
        },
        methods: {
            setRespectiveMenu() {
                axios.get('/api/get-active-user')
                    .then(response => {
                        this.profile = response.data;
                });
            },
            isActive(menuItem) {
                return this.activeMenu === menuItem;
            },
            setActive(menuItem) {
                this.activeMenu = menuItem; // no need for Vue.set()
            },
           /*  toggleFullscreen(event){
                var elem = document.documentElement;
                if (
                document.fullscreenEnabled ||
                document.webkitFullscreenEnabled ||
                document.mozFullScreenEnabled ||
                document.msFullscreenEnabled
                ) {
                if(!this.isFullscreen){
                    if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                    this.isFullscreen = true;
                    return;
                    } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                    this.isFullscreen = true;
                    return;
                    } else if (elem.mozRequestFullScreen) {
                    elem.mozRequestFullScreen();
                    this.isFullscreen = true;
                    return;
                    } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                    this.isFullscreen = true;
                    return;
                    }
                }else{
                    if (document.exitFullscreen) {
                    document.exitFullscreen();
                    this.isFullscreen = false;
                    return;
                    } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                    this.isFullscreen = false;
                    return;
                    } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                    this.isFullscreen = false;
                    return;
                    } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                    this.isFullscreen = false;
                    return;
                    }
                };
                }
            }, */

        },
        mounted() {
           this.activeMenu = this.$route.meta.menuname;
           this.setRespectiveMenu();
        }
    }
</script>
