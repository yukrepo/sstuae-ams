<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'dashboard')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Dashboard</a>
    <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'Summary')?'active':'' }}" href="/dashboard">List and Summary</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'analytics')?'active':'' }}" href="/dashboard/analytics">Analytics </a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'accounts')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Accounts</a>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="/accounts/all">All Logins </a>
        <a class="dropdown-item" href="/accounts/categories">Categories </a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'appointments')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Appointments</a>
    <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'arequest')?'active':'' }}" href="/appointments/requests">Requests <span class="m-l-20 px-1 badge-warning">{{$qcount}}</span> </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'calendar')?'active':'' }}" href="/appointments/calendar">Calendar </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'today')?'active':'' }}" href="/appointments/day-schedule">Todays Appointment </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'upcoming')?'active':'' }}" href="/appointments/upcoming-list/{{ date('Y') }}">Upcoming Appointments </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'history')?'active':'' }}" href="/appointments/history/{{ date('Y') }}">History </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'courses')?'active':'' }}" href="/appointments/courses/{{ date('Y') }}">Prescribed Courses </a>
        <a href="/appointments/course-packages/{{ date('Y') }}" class="dropdown-item {{ (\Route::current()->parameter('slug') == 'packages')?'active':'' }}"> Schedule Packages </a>
        <a href="/appointments/direct-courses/{{ date('Y') }}" class="dropdown-item {{ (\Route::current()->parameter('slug') == 'directs')?'active':'' }}"> Direct Courses</a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'invoices')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Invoices</a>
    <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'appointments')?'active':'' }}" href="/invoices/appointments/{{ date('Y') }}">Appointment Invoices </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'courses')?'active':'' }}" href="/invoices/courses/{{ date('Y') }}">Courses Invoices </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'directinv')?'active':'' }}" href="/invoices/direct-invoices/{{ date('Y') }}">Direct Invoices </a>
        <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'pharmacy')?'active':'' }}" href="/invoices/pharmacy/{{ date('Y') }}">Pharmacy Invoices </a>
    </div>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'customers')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Customers</a>
    <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'add-customer')?'active':'' }}" href="/customers/add">Add New Customer</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'customer-list')?'active':'' }}" href="/customers/all">Complete List </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'customer-merging')?'active':'' }}" href="/customers/merging">Merge Records </a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'pharmacy')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Pharmacy</a>
    <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'ledger')?'active':'' }}" href="/pharmacy/ledger">Ledger </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'categories')?'active':'' }}" href="/pharmacy/categories">Categories </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'medicines')?'active':'' }}" href="/pharmacy/medicines">Medicines </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'stocks')?'active':'' }}" href="/pharmacy/stocks">Stocks </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'stockstatus')?'active':'' }}" href="/pharmacy/stock-status">Stock Status</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'purchases')?'active':'' }}" href="/pharmacy/purchases">Purchases </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'suppliers')?'active':'' }}" href="/pharmacy/suppliers">Suppliers </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'manufacturers')?'active':'' }}" href="/pharmacy/manufacturers">Manufacturers </a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'elements')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Elements</a>
    <div class="dropdown-menu">
        <a href="/elements/doctors" class="dropdown-item {{ (\Route::current()->parameter('slug') == 'doctors')?'active':'' }}"> Doctors </a>
        <a href="/elements/rooms" class="dropdown-item {{ (\Route::current()->parameter('slug') == 'rooms')?'active':'' }}"> Rooms </a>
        <a href="/elements/treatments" class="dropdown-item {{ (\Route::current()->parameter('slug') == 'treatments')?'active':'' }}"> Treatments </a>
        <a href="/elements/packages" class="dropdown-item {{ (\Route::current()->parameter('slug') == 'packages')?'active':'' }}"> Packages </a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'assets')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Assets</a>
    <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'diagnosis')?'active':'' }}" href="/assets/diagnosis">Diagnosis</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'labcategories')?'active':'' }}" href="/assets/lab-categories">Lab Categories</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'laboratories')?'active':'' }}" href="/assets/laboratories">Laboratories</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'oecategories')?'active':'' }}" href="/assets/oe-categories">OE Categories</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'symptoms')?'active':'' }}" href="/assets/symptoms">Symptoms</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'xrays')?'active':'' }}" href="/assets/xrays">Xrays</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'comments')?'active':'' }}" href="/assets/comments">comments</a>
    </div>
</li>
<li class="nav-item">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'reports')?'active':'' }}"  href="/reports">Reports</a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'settings')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Settings</a>
    <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'financial')?'active':'' }}" href="/settings/financial-years">Financial Years</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'configurations')?'active':'' }}" href="/settings/configurations">configurations</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'discounts')?'active':'' }}" href="/settings/discounts">Discounts</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'timeslots')?'active':'' }}" href="/settings/timeslots">Timeslots</a>
    </div>
</li>