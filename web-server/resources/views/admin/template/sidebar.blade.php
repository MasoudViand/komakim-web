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
                <p>Alexander Pierce</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form (Optional) -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
                <span class="input-group-btn">
  <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
</span>
            </div>
        </form>
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">HEADER</li>
            <!-- Optionally, you can add icons to the links -->
            <li class="active">
                <a href="{{ route('admin.service') }}">
                    <i class="fa fa-th"></i> <span> سرویس</span>
                </a>
                <a href="{{ route('admin.category') }}">
                    <i class="fa fa-star"></i> <span>دسته بندی</span>
                </a>
                <a href="{{ route('admin.subcategory') }}">
                    <i class="fa fa-th"></i> <span>زیر دسته بندی</span>
                </a>
                <a href="{{ route('admin.emailtemplate') }}">
                    <i class="fa fa-th"></i> <span> قالبهای ایمیل</span>
                </a>
                <a href="{{ route('admin.list.survey') }}">
                    <i class="fa fa-th"></i> <span> نظرات کاربران </span>
                </a>
                <a href="{{ route('admin.dissatisfied.reason.list') }}">
                    <i class="fa fa-th"></i> <span> دلایل عدم رضایت </span>
                </a>
                <a href="{{ route('admin.user.list') }}">
                    <i class="fa fa-th"></i> <span> کاربران </span>
                </a>
                <a href="{{ route('admin.order.list') }}">
                    <i class="fa fa-th"></i> <span> سفارسات </span>
                </a>
                <a href="{{ route('admin.discount_code.list') }}">
                    <i class="fa fa-th"></i> <span> کد تخفیف </span>
                </a>


                <a href="{{ route('admin.cancel.reason.list') }}">
                    <i class="fa fa-th"></i> <span> دلایل لغو سفارش </span>
                </a>
                <a href="{{ route('admin.settle.dept.list') }}">
                    <i class="fa fa-th"></i> <span>تسویه حساب </span>
                </a>
                <a href="{{ route('admin.financial') }}">
                    <i class="fa fa-th"></i> <span> گزارشات مالی </span>
                </a>
                <a href="{{ route('admin.financial.remain_wallet') }}">
                    <i class="fa fa-th"></i> <span> مقادیر کیف پول استفاده نشده </span>
                </a>

                <a href="{{ route('admin.transactions.list') }}">
                    <i class="fa fa-th"></i> <span> تراکنش ها </span>
                </a>
                <a href="{{ route('admin.setting') }}">
                    <i class="fa fa-th"></i> <span> تنظیمات </span>
                </a>


            </li>

        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>