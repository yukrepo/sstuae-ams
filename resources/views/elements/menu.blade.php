<nav class="navbar navbar-expand-md navbar-light navbar-laravel main-nav">
    <a class="navbar-brand" href="{{ url('/') }}">
         <img src="{{ asset('/svg/logo.svg') }}">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="'Toggle navigation'">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent" >
        <!-- Left Side Of Navbar -->
        <ul class="navbar-nav mr-auto main-nav">
            @if(Auth::user()->admin_type_id == 1) @include('elements.menus.admin')
            @elseif(Auth::user()->admin_type_id == 2) @include('elements.menus.doctors')
            @elseif(Auth::user()->admin_type_id == 3) @include('elements.menus.pharmacy')
            @elseif(Auth::user()->admin_type_id == 4) @include('elements.menus.manager')
            @elseif(Auth::user()->admin_type_id == 5) @include('elements.menus.reception')
            @elseif(Auth::user()->admin_type_id == 6) @include('elements.menus.therapists')
            @elseif(Auth::user()->admin_type_id == 7) @include('elements.menus.accounts')
            @elseif(Auth::user()->admin_type_id == 8) @include('elements.menus.directors')
            @elseif(Auth::user()->admin_type_id == 9) @include('elements.menus.supervisor')
            @elseif(Auth::user()->admin_type_id == 10) @include('elements.menus.kims_accounts')
            @endif
        </ul>


        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ml-auto icon-nav m-r-25">
            {{-- <li class="nav-item bg-pink note-btn">
                <a class="nav-link profile-menu" href="javascript:;" data-toggle="modal" data-target="#sidebarModal">
                    <i class="text-warning fas fa-question-circle"></i>
                </a>
            </li> --}}
            <li class="nav-item dropdown bg-primary">
                <a class="nav-link dropdown-toggle text-uppercase text-white profile-menu" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-user"></i> </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="/settings/profile">
                        <i class="fas fa-user m-r-5"></i>  Profile
                    </a>
                    <div role="separator" class="m-0 dropdown-divider"></div>
                    <a class="dropdown-item logout-btn"  href="javascript:;" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-power-off m-r-5"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
