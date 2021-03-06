<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>Point Of Sales</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper" id="app">

        <!-- Navbar -->
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
            </ul>


            <!-- SEARCH FORM -->
            <!-- <div class="form-inline ml-3">
                <div class="input-group input-group-sm">
                    <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-navbar">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div> -->

        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <router-link to="/home" class="brand-link">
                <img src="/img/logo.png" alt="AppInterview Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">POS</span>
            </router-link>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img v-if="$store.state.user.user && userPhoto" :src="[userPhoto == 'profile.png' ? '/img/' + userPhoto : '/storage/images/' + userPhoto]" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <router-link to="/profile" class="d-block">
                            {{Auth::user()->name}}
                        </router-link>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                        <li class="nav-item">
                            <router-link to="/dashboard" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/management" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>

                                <p>
                                    Orders Management
                                </p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/order" class="nav-link">
                                <i class="nav-icon fas fa-shopping-bag"></i>

                                <p>
                                    Orders
                                </p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/category" class="nav-link">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>
                                    Categories
                                </p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/product" class="nav-link">
                                <i class="nav-icon fas fa-box"></i>
                                <p>
                                    Products
                                </p>
                            </router-link>
                        </li>
                        <!-- <li class="nav-item">
                            <router-link to="/profile" class="nav-link">
                                <i class="nav-icon fas fa-user color-orange"></i>
                                <p>
                                    Profile
                                </p>
                            </router-link>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                <i class="nav-icon fas fa-power-off color-red"></i>
                                <p>
                                    {{ __('Logout') }}
                                </p>
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">


            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <router-view></router-view>
                        <!-- <vue-progress-bar></vue-progress-bar> -->
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline">
                Template AdminLTE
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; 2020 <a href="https://github.com/yoghiyb/eDocs" target="_blank">yoghiyb</a>.</strong>.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    @auth
    <script>
        window.Laravel = <?php echo json_encode(['api_token' => (Auth::user())->api_token]); ?>
    </script>
    @endauth
    <script src="{{asset('js/app.js')}}"></script>

</body>

</html>
