@inject('request', 'Illuminate\Http\Request')
<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="sidebar-menu">


            <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
                <a href="{{ url('/admin/home') }}">
                    <i class="fa fa-house"></i>
                    <span class="title">@lang('Home')</span>
                </a>
            </li>
            @if (config('app.is_super_admin_site'))
                <li class="{{ $request->segment(1) == 'clients' ? 'active' : '' }}">
                    <a href="{{ url('/admin/clients') }}">
                        <i class="fa fa-user"></i>
                        <span class="title">Clients</span>
                    </a>
                </li>
            @endif

            @can('has_permission', 'can_user_management')
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span>@lang('quickadmin.user-management.title')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('has_permission', 'can_user_management')
                            <li>
                                <a href="{{ route('admin.roles.index') }}">
                                    <i class="fa fa-id-card"></i>
                                    <span>@lang('quickadmin.roles.title')</span>
                                </a>
                            </li>
                        @endcan

                        @can('has_permission', 'can_user_management')
                            <li>
                                <a href="{{ route('admin.users.index') }}">
                                    <i class="fa fa-user"></i>
                                    <span>@lang('quickadmin.users.title')</span>
                                </a>
                            </li>
                        @endcan

                        @can('has_permission', 'can_user_management')
                            <li>
                                <a href="{{ route('admin.teams.index') }}">
                                    <i class="fa fa-users"></i>
                                    <span>@lang('quickadmin.teams.title')</span>
                                </a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan



            <li class="{{ request()->is('tenant/weekly-plans*') ? 'active' : '' }}">
                <a href="{{ route('tenant.weekly-plans.index') }}">
                    <i class="fa fa-calendar"></i> <span>Weekly Plans</span>
                </a>
            </li>


            @can('has_permission', 'can_access_projects')
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-project-diagram" aria-hidden="true"></i>
                        <span>@lang('Project Management')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('tenant.projects.index') }}">
                                <i class="fa fa-list"></i>
                                <span>Projects</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tenant.projects.dashboard') }}">
                                <i class="fa fa-chart-line"></i>
                                <span>Analytics</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('has_permission', 'can_access_finance')
                <li class="treeview">
                    <a href="{{ route('admin.tenants.create') }}">
                        <i class="fa fa-money-check-dollar"></i>
                        <span>@lang('Finance Management')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('has_permission', 'can_view_quotes')
                            <li>
                                <a href="{{ route('tenant.quotes.index') }}">
                                    <i class="fa fa-file-text"></i>
                                    <span>Quotes</span>
                                </a>

                            </li>
                        @endcan

                        @can('has_permission', 'can_view_invoices')
                            <li>
                                <a href="{{ route('tenant.invoices.index') }}">
                                    <i class="fa fa-file"></i>
                                    <span>Invoices</span>
                                </a>
                            </li>
                        @endcan
                        @can('has_permission', 'can_access_finance')
                            <li>
                                <a href="{{ route('tenant.finance-stats.index') }}">
                                    <i class="fa fa-pie-chart"></i>
                                    <span>Insights</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('has_permission', 'can_access_assets')
                <li>
                    <a href="{{ route('tenant.assets.index') }}">
                        <i class="fa fa-car" aria-hidden="true"></i>
                        <span>@lang('Asset Management')</span>
                    </a>
                </li>
            @endcan

            @unless (config('app.business_type') === 'SMME')
                @can('has_permission', 'can_access_smme')
                    <li class="treeview">
                        <a href="{{ route('admin.tenants.create') }}">
                            <i class="fa fa-briefcase"></i>
                            <span>@lang('Supplier Management')</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @can('has_permission', 'can_access_assets')
                                <li>
                                    <a href="{{ route('tenant.smmes.index') }}">
                                        <i class="fa fa-briefcase" aria-hidden="true"></i>
                                        <span>@lang('Suppliers')</span>
                                    </a>
                                </li>
                            @endcan

                            @can('has_permission', 'can_access_data_config')
                                <li>
                                    <a href="{{ route('tenant.smme-stats.index') }}">
                                        <i class="fa fa-pie-chart"></i>
                                        <span>Insights</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
            @endunless

            @can('has_permission', 'can_access_data_config')
                <li class="treeview">
                    <a href="{{ route('admin.tenants.create') }}">
                        <i class="fa fa-sliders-h"></i>
                        <span>@lang('Data Config')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('has_permission', 'can_access_assets')
                            <li>
                                <a href="{{ route('tenant.asset-types.index') }}">
                                    <i class="fas fa-cubes"></i>
                                    <span>Asset Types</span>
                                </a>

                            </li>
                        @endcan

                        @can('has_permission', 'can_access_data_config')
                            <li>
                                <a href="{{ route('tenant.locations.index') }}">
                                    <i class="fa fa-map"></i><span>Project Locations</span>
                                </a>
                            </li>
                        @endcan
                        @can('has_permission', 'can_access_data_config')
                            <li>
                                <a href="#">
                                    <i class="fas fa-users-cog"></i>
                                    <span>Clients Config</span>
                                </a>
                            </li>
                        @endcan
                        @can('has_permission', 'can_access_logs')
                            <li>
                                <a href="{{ route('tenant.logs.index') }}">
                                    <i class="fa fa-history"></i>
                                    <span>Activity Logs</span>
                                </a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan

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
    </section>
</aside>
