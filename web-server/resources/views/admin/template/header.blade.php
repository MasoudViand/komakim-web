<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="#" class="logo">کمکیم - پنل مدیریت</a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Messages: style can be found in dropdown.less-->



                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{ asset("AdminLTE-RTL/dist/img/user2-160x160.jpg") }}" class="user-image" alt="User Image"/>
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{Auth::user()->name}}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ asset("AdminLTE-RTL/dist/img/user2-160x160.jpg") }}" class="img-circle" alt="User Image" />
                            <p>
                                {{Auth::user()->name}}
                                <small>{{Auth::user()->email}}</small>
                            </p>
                        </li>
                        <!-- Menu Body -->

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{route('admin.change.pass')}}" class="btn btn-default btn-flat">تغییر رمز</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{route('admin.logout')}}" class="btn btn-default btn-flat">خروج</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>