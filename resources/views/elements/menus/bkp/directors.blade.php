<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'reports')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Reports</a>
    <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'fullreports')?'active':'' }}" href="/reports/full-reports">Full Reports</a>
        <a class="dropdown-item" href="/reports/invoices">Invoices Reports</a>
        <a class="dropdown-item" href="/reports/pharmacy">Pharmacy Reports</a>
        <a class="dropdown-item" href="/reports/consultation">Consultation Reports</a>
        <a class="dropdown-item" href="/reports/treatments">Treatment Report</a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'search')?'active':'' }}"  v-on:click.native="setActive('search')" href="/search">Search</a>
</li>

