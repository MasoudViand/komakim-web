<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset("AdminLTE-RTL/dist/img/user2-160x160.jpg") }}" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p>{{Auth::user()->name}}</p>

            </div>
        </div>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">

		<li class="header">{{Auth::user()->role==\App\Admin::ADMIN_ROLE ? ' ادمین':(Auth::user()->role==\App\Admin::OPERATOR_ROLE?'اپراتور':'مالی')}}</li>
            <li >
                <a href="{{ route('admin.dashboard') }}" >
                    <i class="fa fa-dashboard "></i> <span> داشبورد </span>
                </a>
            </li>
            @if(Auth::user()->role==\App\Admin::ADMIN_ROLE  )


            <li >
                <a href="{{ route('admin.map') }}" >
                    <i class="fa fa-map"></i> <span> نقشه </span>
                </a>
            </li>
            @endif
            @if(Auth::user()->role==\App\Admin::ADMIN_ROLE or Auth::user()->role==\App\Admin::OPERATOR_ROLE  )

            <li >
                <a href="{{ route('admin.order.list') }}" >
                    <i class="fa fa-shopping-cart"></i> <span> لیست سفارشات </span>
                </a>
            </li>

            @endif

            @if(Auth::user()->role==\App\Admin::ADMIN_ROLE  )


		<li class="treeview">
          <a href="#">
            <i class="fa fa-bars"></i>
            <span>دسته بندی ها</span>
            <span class="pull-left-container">
              <i class="fa fa-angle-left pull-left"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li>
			<a href="{{ route('admin.category') }}">
                    <i class="fa fa-th"></i> <span>لیست دسته بندی ها</span>
                </a>
				</li>
				<li>
				<a href="{{ route('admin.category.insert') }}">
                    <i class="fa fa-th"></i> <span>افزودن دسته بندی</span>
                </a>
				</li>
          </ul>
        </li>
        <li class="treeview">
                    <a href="#">
                        <i class="fa fa-list"></i>
                        <span>زیر دسته بندی ها</span>
                        <span class="pull-left-container">
              <i class="fa fa-angle-left pull-left"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('admin.subcategory') }}">
                                <i class="fa fa-th"></i> <span>لیست زیر دسته بندی ها</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.subcategory.insert') }}">
                                <i class="fa fa-th"></i> <span>افزودن زیر دسته بندی</span>
                            </a>
                        </li>
                    </ul>
                </li>
        <li class="treeview">
                    <a href="">
                        <i class="fa fa-files-o"></i>
                        <span>سرویس ها</span>
                        <span class="pull-left-container">
              <i class="fa fa-angle-left pull-left"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('admin.service') }}"><i class="fa fa-th"></i> <span> لیست سرویس ها</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.service.insert') }}"><i class="fa fa-th"></i> <span>افزودن سرویس</span></a>
                        </li>
                    </ul>
                </li>




            @endif


            @if(Auth::user()->role==\App\Admin::ADMIN_ROLE or Auth::user()->role==\App\Admin::OPERATOR_ROLE  )

            <li>
                <a href="{{ route('admin.user.list') }}">
                    <i class="fa fa-users"></i> <span> کاربران </span>
                </a>
            </li>

            @endif
            @if(Auth::user()->role==\App\Admin::ADMIN_ROLE  )

                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-user-secret"></i>
                        <span>کاربران ادمین</span>
                        <span class="pull-left-container">
              <i class="fa fa-angle-left pull-left"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('admin.user_admin') }}">
                                <i class="fa fa-th"></i> <span>لیست کاربران ادمین</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.user_admin.insert') }}">
                                <i class="fa fa-th"></i> <span>افزودن کاربر ادمین</span>
                            </a>
                        </li>
                    </ul>
                </li>



            @endif


            @if(Auth::user()->role==\App\Admin::ADMIN_ROLE or Auth::user()->role==\App\Admin::FINANCIAL_ROLE  )

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-credit-card"></i>
                    <span>امور مالی</span>
                    <span class="pull-left-container">
              <i class="fa fa-angle-left pull-left"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="{{ route('admin.financial') }}">
                            <i class="fa fa-bar-chart"></i> <span> گزارشات مالی </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.transactions.list') }}">
                            <i class="fa fa-exchange"></i> <span> تراکنش ها </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.financial.remain_wallet') }}">
                            <i class="fa fa-usd"></i> <span> مقادیر کیف پول استفاده نشده </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settle.dept.list') }}">
                            <i class="fa fa-money"></i> <span>تسویه حساب </span>
                        </a>
                    </li>

                </ul>
            </li>

            @endif

            @if(Auth::user()->role==\App\Admin::ADMIN_ROLE   )


            <li>
                <a href="{{ route('admin.discount_code.list') }}">
                    <i class="fa fa-tags"></i> <span> کد تخفیف </span>
                </a>
            </li>

            @endif


            @if(Auth::user()->role==\App\Admin::ADMIN_ROLE or Auth::user()->role==\App\Admin::OPERATOR_ROLE  )




            <li class="treeview">
                <a href="#">
                    <i class="fa fa-check-square-o"></i>
                    <span>نظرسنجی</span>
                    <span class="pull-left-container">
              <i class="fa fa-angle-left pull-left"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="{{ route('admin.list.survey') }}">
                            <i class="fa fa-th"></i> <span>لیست نظرسنجی ها </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.dissatisfied.reason.list') }}">
                            <i class="fa fa-th"></i> <span> دلایل عدم رضایت </span>
                        </a>
                    </li>
                </ul>
            </li>

            @endif



            @if(Auth::user()->role==\App\Admin::ADMIN_ROLE  )


            <li class="treeview">
                <a href="#">
                    <i class="fa fa-cogs"></i>
                    <span>تنظیمات</span>
                    <span class="pull-left-container">
              <i class="fa fa-angle-left pull-left"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="{{ route('admin.emailtemplate') }}">
                            <i class="fa fa-th"></i> <span> قالبهای ایمیل</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.cancel.reason.list') }}">
                            <i class="fa fa-th"></i> <span> دلایل لغو سفارش </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.work.with.us.condition.insert') }}">
                            <i class="fa fa-th"></i> <span> شرایط همکاری با ما</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.rules.insert') }}">
                            <i class="fa fa-th"></i> <span>قوانین و مقررات</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.repeat.question') }}">
                            <i class="fa fa-th"></i> <span>سوالات متداول</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.setting') }}">
                            <i class="fa fa-th"></i> <span> تنظیمات دیگر</span>
                        </a>
                    </li>



                </ul>
            </li>
            <li >
                <a href="{{ route('admin.notification') }}" >
                    <i class="fa fa-bell"></i> <span> ناتیفیکیشن </span>
                </a>
            </li>
            <li >
                <a href="{{ route('admin.sms') }}" >
                    <i class="fa fa-comments"></i> <span> پیام کوتاه </span>
                </a>
            </li>


            @endif


        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>