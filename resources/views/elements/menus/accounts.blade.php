<li class="nav-item">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'dashboard')?'active':'' }}"  href="/dashboard">Dashboard</a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'pharmacy')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Pharmacy</a>
    <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'purchases')?'active':'' }}" href="/pharmacy/purchases">Purchases </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'suppliers')?'active':'' }}" href="/pharmacy/suppliers">Suppliers </a>
    </div>
</li>
<li class="nav-item">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'reports')?'active':'' }}"  href="/reports">Reports</a>
</li>
