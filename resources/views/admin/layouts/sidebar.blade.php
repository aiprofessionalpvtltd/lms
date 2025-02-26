<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="index.html">
					<span class="sidebar-brand-text align-middle">
						Admin Panel
						<sup><small class="badge bg-primary text-uppercase">Pro</small></sup>
					</span>
            <svg class="sidebar-brand-icon align-middle" width="32px" height="32px" viewBox="0 0 24 24" fill="none"
                 stroke="#FFFFFF" stroke-width="1.5"
                 stroke-linecap="square" stroke-linejoin="miter" color="#FFFFFF" style="margin-left: -3px">
                <path d="M12 4L20 8.00004L12 12L4 8.00004L12 4Z"></path>
                <path d="M20 12L12 16L4 12"></path>
                <path d="M20 16L12 20L4 16"></path>
            </svg>
        </a>

        <div class="sidebar-user">
            <div class="d-flex justify-content-center">
                <div class="flex-shrink-0">
                    <img src="{{asset('backend/img/avatars/new_logo.jpg')}}" style="width: 60px !important;"
                         class="avatar img-fluid rounded me-1"
                         alt="{{ Auth::user()->name }}"/>
                </div>
                <div class="flex-grow-1 ps-2">
                    <a class="sidebar-user-title dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-start">

                        <a href="{{ route('logout') }}" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"
                           class="dropdown-item"><i class="icon-switch2"></i> Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>

                    <div class="sidebar-user-subtitle">
                        {{ Auth::user()->roles->pluck('name')->first() }}
                    </div>
                </div>
            </div>
        </div>

        <ul class="sidebar-nav">
            <li class="sidebar-header">
                Pages
            </li>

            <li class="sidebar-item {{ request()->routeIs('our-dashboard') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('dashboard') }}">
                    <i class="align-middle" data-feather="user"></i>
                    <span class="align-middle">Dashboard</span>
                </a>
            </li>
            @can('view-roles')
                <li class="sidebar-item {{ request()->routeIs('failed-attempt-logs') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('failed-attempt-logs') }}">
                        <i class="align-middle" data-feather="user"></i>
                        <span class="align-middle">Failed Attempt Logs</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('activity-logs') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('activity-logs') }}">
                        <i class="align-middle" data-feather="user"></i>
                        <span class="align-middle">Activity Logs</span>
                    </a>
                </li>




                <li class="sidebar-item {{ request()->routeIs('show-role') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('show-role') }}">
                        <i class="align-middle" data-feather="user"></i>
                        <span class="align-middle">Role</span>
                    </a>
                </li>

            @endcan

            @can('view-users')

                <li class="sidebar-item {{ request()->routeIs('show-user') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('show-user') }}">
                        <i class="align-middle" data-feather="user"></i>
                        <span class="align-middle">User</span>
                    </a>
                </li>
            @endcan
            @can('view-products')

                <li class="sidebar-item {{ request()->routeIs('show-product') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('show-product') }}">
                        <i class="align-middle" data-feather="user"></i>
                        <span class="align-middle">Product</span>
                    </a>
                </li>

            @endcan
            @can('view-customer')
                <li class="sidebar-item {{ request()->routeIs('show-nacta-list') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('show-nacta') }}">
                        <i class="align-middle" data-feather="user"></i>
                        <span class="align-middle">NACTA  List</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('show-customer') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('show-customer') }}">
                        <i class="align-middle" data-feather="user"></i>
                        <span class="align-middle">Customers</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('show-customer-zindagi') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('show-customer-zindagi') }}">
                        <i class="align-middle" data-feather="user"></i>
                        <span class="align-middle">JS Zindagi Verfied Customers</span>
                    </a>
                </li>

            @endcan

            @can('view-customer-noc')

                <li class="sidebar-item  ">
                    <a class="sidebar-link" href="{{ route('get-complete-loan-applications') }}">
                        <i class="fas fa-clipboard-check"></i>
                        <span class="align-middle">Customer NOC</span>
                    </a>
                </li>
            @endcan

            @can('view-loan-management')

                <li class="sidebar-item  ">
                    <a class="sidebar-link" href="{{ route('get-all-loan-applications') }}">
                        <i class="fas fa-bank"></i>
                        <span class="align-middle">Loan Applications</span>
                    </a>
                </li>
            @endcan


            @can('view-installments')

                <li class="sidebar-item {{ request()->routeIs('show-installment') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('show-installment') }}">
                        <i class="align-middle" data-feather="user"></i>
                        <span class="align-middle">Installments</span>
                    </a>
                </li>

            @endcan

            @can('view-accounts')

                <li class="sidebar-item">
                    <a data-bs-target="#account" data-bs-toggle="collapse" class="sidebar-link collapsed"
                       aria-expanded="false">
                        <span class="align-middle">Accounting</span>
                    </a>
                    <ul id="account" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar" style="">
                        {{--                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-expense-categories')}}">Expense Category--}}
                        {{--                            </a></li>--}}
                        {{--                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-expense')}}">Expenses</a></li>--}}

                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-vendor-account')}}">Vendors Accounts

                            </a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-account-type')}}">Account
                                Type
                            </a></li>

                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-account-name')}}">Account
                                Name
                            </a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-account')}}">Chart Of
                                Account
                            </a></li>

                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-account-transaction')}}">
                                Journal Entry
                            </a></li>

                    </ul>
                </li>

            @endcan

            @can('view-reports')

                <li class="sidebar-item">
                    <a data-bs-target="#reports" data-bs-toggle="collapse" class="sidebar-link collapsed"
                       aria-expanded="false">
                        <span class="align-middle">Reports</span>
                    </a>
                    <ul id="reports" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar" style="">
                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-invoice-report')}}">Customer
                                Invoice
                            </a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-disbursement-report')}}">Disbursement
                                Report</a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-overdue-report')}}">Overdue
                                Report</a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-collection-report')}}">Collection
                                Report</a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-profit-report')}}">Service
                                Charge/Profit Report</a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-outstanding-report')}}">Outstanding
                                Report</a></li>

                        <li class="sidebar-item"><a class="sidebar-link"
                                                    href="{{route('show-aging-receivable-report')}}">Aging Receivable
                                Report</a></li>


                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-provision-report')}}">Provisioning
                                Report</a></li>


                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-financing-report')}}">Product
                                Financing
                                Report</a></li>

                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-penalty-report')}}">Penalty
                                Report</a></li>

                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-principal-report')}}">Principal
                                Payment
                                Report</a></li>

                        <li class="sidebar-item"><a class="sidebar-link"
                                                    href="{{route('show-interest-income-report')}}">Interest Income
                                Report</a></li>

                        <li class="sidebar-item"><a class="sidebar-link"
                                                    href="{{route('show-early-settlement-report')}}">Early Settlement
                                Report</a></li>

                        <li class="sidebar-item"><a class="sidebar-link" href="{{route('show-complete-report')}}">
                                Completed Application
                                Report</a></li>

                    </ul>
                </li>

            @endcan


        </ul>
    </div>
</nav>
