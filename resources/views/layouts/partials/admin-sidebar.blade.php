<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark border-end border-5 ">
    <div class="container-fluid">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <h1 class="navbar-brand navbar-brand-autodark">

        </h1>

        <div class=" d-lg-none d-flex justify-content-evenly">
            <div class="nav-item dropdown ">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0 text-secondary" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <span>
                        @include('icons.user-circle')
                    </span>
                    <div class="d-xl-block ps-2">
                        @isset(getAuthData()->name)
                            <div>{{ getAuthData()->name ?? '' }}</div>
                        @endisset
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow ">
                    <a href="{{ route('user.profile') }}" class="dropdown-item">Account Info</a>
                    {{-- <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="javascript:void(0);" onclick="document.getElementById('logout-form').submit()"
                            class="dropdown-item text-danger">Logout</a>
                    </form> --}}
                </div>
            </div>

            <div class=" d-md-flex ">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a href="javascript:void(0);" onclick="document.getElementById('logout-form').submit()"
                        class="text-danger text-decoration-none px-2">
                        <span class="text-danger">
                            @include('icons.logout')
                        </span>
                        Logout
                    </a>
                </form>
            </div>
        </div>

        <div class="collapse navbar-collapse" id="sidebar-menu">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch mt-2">
                <ul class="navbar-nav">

                    <li class="nav-item {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Dashboard
                            </span>
                        </a>
                    </li>


                    <li class="dropdown active">
                        <a class="nav-link dropdown-toggle " href="#navbar-help" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                @include('icons.settings')
                            </span>
                            <span class="nav-link-title">
                                Masters
                            </span>
                        </a>

                        <div class="dropdown-menu show">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">

                                    <a class="dropdown-item " href="{{ route('employees.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            @include('icons.user')
                                        </span>
                                        <span class="nav-link-title">
                                            Employees
                                        </span>
                                    </a>

                                    <a class="dropdown-item " href={{ route('country') }}>
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            @include('icons.category')
                                        </span>
                                        <span class="nav-link-title">
                                            Country
                                        </span>
                                    </a>

                                    <a class="dropdown-item" href={{ route('state') }}>
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            @include('icons.speakerphone')
                                        </span>
                                        <span class="nav-link-title">
                                            State
                                        </span>
                                    </a>

                                    <a class="dropdown-item " href={{ route('city') }}>
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            @include('icons.basket-filled')
                                        </span>
                                        <span class="nav-link-title">
                                            City
                                        </span>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>
        </div>

    </div>

</aside>
