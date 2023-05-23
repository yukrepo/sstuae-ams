<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'pharmacy')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Pharmacy</a>
    <div class="dropdown-menu">
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'purchases')?'active':'' }}" href="/pharmacy/purchases">Purchases </a>
        <a class="dropdown-item {{ (\Route::current()->parameter('slug') == 'suppliers')?'active':'' }}" href="/pharmacy/suppliers">Suppliers </a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ (\Route::current()->parameter('menu') == 'reports')?'active':'' }}" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false">Reports</a>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="/reports/invoices">Invoices Reports</a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link {{ (\Route::current()->parameter('menu') == 'search')?'active':'' }}"  v-on:click.native="setActive('search')" href="/search">Search</a>
</li>

