<li class="nav-item">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'dashboard')?'active':'' }}"  href="/dashboard">Dashboard</a>
</li>
<li class="nav-item">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'reports')?'active':'' }}"  href="/reports">Reports</a>
</li>