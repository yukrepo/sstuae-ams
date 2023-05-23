<li class="nav-item">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'dashboard')?'active':'' }}"  href="/dashboard">Dashboard</a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'pappointments')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Personal Appointments</a>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="/doctors/appointments-calendar">Calendar </a>
        <a class="dropdown-item" href="/doctors/appointments-day-schedule">Todays Appointment </a>
        <a class="dropdown-item" href="/doctors/appointments-upcoming-list/{{ date('Y') }}">Upcoming Appointments </a>
        <a class="dropdown-item" href="/doctors/appointments-history/{{ date('Y') }}">History </a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'appointments')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Clinic Appointments</a>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="/appointments/calendar">Calendar </a>
        <a class="dropdown-item" href="/appointments/day-schedule">Todays Appointment </a>
        <a class="dropdown-item" href="/appointments/upcoming-list/{{ date('Y') }}">Upcoming Appointments </a>
        <a class="dropdown-item" href="/appointments/history/{{ date('Y') }}">History </a>
    </div>
</li>
{{-- <li class="nav-item">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'availability')?'active':'' }}"  v-on:click.native="setActive('search')" href="/doctors/availability">Availability</a>
</li>  --}}
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'elements')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Elements</a>
    <div class="dropdown-menu">
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
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'pharmacy')?'active':'' }}" href="/pharmacy/stocks">Stocks </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'reports')?'active':'' }}"  href="/reports">Reports</a>
</li>