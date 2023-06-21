<header class="header-section bg-red-700 drop-shadow-lg h-20 w-full">
    <div class="container-fluid py-4 relative flex justify-between h-full">
        <div class="mobile-header h-full">
            <button type="button" class="bi bi-list text-white cursor-pointer text-3xl" id="btn-sidebar-mobile"></button>
        </div>
        @if ((Auth::check() && Auth::user()->user_role == 'CDRRMO') || (Auth::check() && Auth::user()->user_role == 'CSWD'))
        <div class="flex justify-center items-center ">
            <div class="dropdown px-2">
                <button class="text-white text-sm bi bi-caret-down-fill" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                </button>
    
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item text-sm" href=""><i class="bi bi-person pr-2"></i>My Profile</a></li>
                    <li><a class="dropdown-item text-sm" href="{{ route('logout.user') }}"><i class="bi bi-box-arrow-in-left pr-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
        @endif
    </div>
</header>