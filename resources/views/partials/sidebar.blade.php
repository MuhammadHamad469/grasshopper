@inject('request', 'Illuminate\Http\Request')
<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
       <ul class="sidebar-menu">

    <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
        <a href="{{ url('/') }}">
            <i class="fa fa-wrench"></i>
            <span class="title">@lang('quickadmin.qa_dashboard')</span>
        </a>
    </li>

    @can('user_management_access')
    <li class="treeview">
        <a href="#">
            <i class="fa fa-users"></i>
            <span>@lang('quickadmin.user-management.title')</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @can('role_access')
            <li>
                <a href="{{ route('admin.roles.index') }}">
                    <i class="fa fa-briefcase"></i>
                    <span>@lang('quickadmin.roles.title')</span>
                </a>
            </li>@endcan
            
            @can('user_access')
            <li>
                <a href="{{ route('admin.users.index') }}">
                    <i class="fa fa-user"></i>
                    <span>@lang('quickadmin.users.title')</span>
                </a>
            </li>@endcan
            
            @can('team_access')
            <li>
                <a href="{{ route('admin.teams.index') }}">
                    <i class="fa fa-users"></i>
                    <span>@lang('quickadmin.teams.title')</span>
                </a>
            </li>@endcan
        </ul>
    </li>@endcan

    @can('tenant_management_access')
    <li class="treeview">
        <a href="{{ route('admin.tenants.create') }}">
            <i class="fa fa-building"></i>
            <span>@lang('quickadmin.tenant-management.title')</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @can('role_access')
            <li>
                <a href="{{ route('admin.tenants.create') }}">
                    <i class="fa fa-plus"></i>
                    <span>New tenant</span>
                </a>
            </li>@endcan

            @can('user_access')
            <li>
                <a href="{{ route('admin.users.index') }}">
                    <i class="fa fa-users"></i>
                    <span>@lang('Tenants')</span>
                </a>
            </li>@endcan
        </ul>
    </li>@endcan

    @can('product_access')
    <li>
        <a href="{{ route('admin.products.index') }}">
            <i class="fa fa-file-text"></i>
            <span>@lang('quickadmin.products.title')</span>
        </a>
    </li>@endcan

    <li class="{{ $request->segment(1) == 'change_password' ? 'active' : '' }}">
        <a href="{{ route('auth.change_password') }}">
            <i class="fa fa-key"></i>
            <span class="title">@lang('quickadmin.qa_change_password')</span>
        </a>
    </li>

    <li>
        <a href="#logout" onclick="$('#logout').submit();">
            <i class="fa fa-arrow-left"></i>
            <span class="title">@lang('quickadmin.qa_logout')</span>
        </a>
    </li>
</ul>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.sidebar-menu .treeview > a').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                let parent = this.parentElement;
                let menu = parent.querySelector('.treeview-menu');
                document.querySelectorAll('.sidebar-menu .treeview').forEach(function (item) {
                    if (item !== parent) {
                        item.classList.remove('active');
                        let sub = item.querySelector('.treeview-menu');
                        if (sub) sub.style.display = 'none';
                    }
                });
                parent.classList.toggle('active');
                if (menu) {
                    if (menu.style.display === 'block') {
                        menu.style.display = 'none';
                    } else {
                        menu.style.display = 'block';
                    }
                }
            });
        });
    });
</script>
    </section>
</aside>