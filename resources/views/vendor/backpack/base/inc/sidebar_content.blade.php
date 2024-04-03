{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
@includeWhen(class_exists(\Backpack\DevTools\DevToolsServiceProvider::class), 'backpack.devtools::buttons.sidebar_item')

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('currency') }}"><i class="nav-icon la la-question"></i> Currencies</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('balance') }}"><i class="nav-icon la la-question"></i> Balances</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('resource') }}"><i class="nav-icon la la-question"></i> Resources</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('service') }}"><i class="nav-icon la la-question"></i> Services</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('land') }}"><i class="nav-icon la la-question"></i> Lands</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('farm') }}"><i class="nav-icon la la-question"></i> Farms</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('service-use') }}"><i class="nav-icon la la-question"></i> Service uses</a></li>


<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> Authentication</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}">&nbsp;&nbsp;<i class="nav-icon la la-user"></i> <span>Users</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}">&nbsp;&nbsp;<i class="nav-icon la la-id-badge"></i> <span>Roles</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}">&nbsp;&nbsp;<i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
    </ul>
</li>
