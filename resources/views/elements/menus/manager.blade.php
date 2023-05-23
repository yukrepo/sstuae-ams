<li class="nav-item">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'dashboard')?'active':'' }}"  href="/dashboard">Dashboard</a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'appointments')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Appointments</a>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="/appointments/requests">Requests <span class="m-l-20 px-1 badge-warning">{{$qcount}}</span></a>
        <a class="dropdown-item" href="/appointments/calendar">Calendar </a>
        <a class="dropdown-item" href="/appointments/day-schedule">Todays Appointment </a>
        <a class="dropdown-item" href="/appointments/upcoming-list/{{ date('Y') }}">Upcoming Appointments </a>
        <a class="dropdown-item" href="/appointments/history/{{ date('Y') }}">History </a>
        <a class="dropdown-item" href="/appointments/courses/{{ date('Y') }}">Prescribed Courses </a>
        <a href="/appointments/course-packages/{{ date('Y') }}" class="dropdown-item"> Schedule Packages </a>
        <a href="/appointments/direct-courses/{{ date('Y') }}" class="dropdown-item"> Direct Courses</a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'invoices')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Invoices</a>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="/invoices/appointments/{{ date('Y') }}">Appointment Invoices </a>
        <a class="dropdown-item" href="/invoices/courses/{{ date('Y') }}">Courses Invoices </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'directinv')?'active':'' }}" href="/invoices/direct-invoices/{{ date('Y') }}">Direct Invoices </a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'customers')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Customers</a>
    <div class="dropdown-menu">
        <<a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'add-customer')?'active':'' }}" href="/customers/add">Add New Customer</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'customer-list')?'active':'' }}" href="/customers/all">Complete List </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'customer-merging')?'active':'' }}" href="/customers/merging">Merge Records </a>
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
<li class="nav-item">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'reports')?'active':'' }}"  href="/reports">Reports</a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'settings')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Settings</a>
    <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'discounts')?'active':'' }}" href="/settings/discounts">Discounts</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'timeslots')?'active':'' }}" href="/settings/timeslots">Timeslots</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'comments')?'active':'' }}" href="/assets/comments">comments</a>
    </div>
</li>