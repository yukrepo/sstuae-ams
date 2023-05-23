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
</li> --}}
<li class="nav-item">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'pharmacy')?'active':'' }}" href="/pharmacy/stocks">Stocks </a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'reports')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Reports</a>
    <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'consultation')?'active':'' }}" href="/reports/consultation">Consultation Reports</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'treatments')?'active':'' }}" href="/reports/treatments">Treatment Report</a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'pharmacy')?'active':'' }}" href="/full-reports/stocks">Stock  Report</a>
    </div>
</li>
<li class="nav-item">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'search')?'active':'' }}"  v-on:click.native="setActive('search')" href="/search">Search</a>
</li>
