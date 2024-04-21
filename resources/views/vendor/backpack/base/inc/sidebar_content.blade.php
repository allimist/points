{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
@includeWhen(class_exists(\Backpack\DevTools\DevToolsServiceProvider::class), 'backpack.devtools::buttons.sidebar_item')

<li class="nav-item"><a class="nav-link" href="/play"><i class="la la-home nav-icon"></i> Play</a></li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('setting') }}'><i class='nav-icon la la-cog'></i> <span>Settings</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('avatar') }}"><i class="nav-icon la la-question"></i> Avatars</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('currency') }}"><i class="nav-icon la la-question"></i> Currencies</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('balance') }}"><i class="nav-icon la la-question"></i> Balances</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('skill') }}"><i class="nav-icon la la-question"></i> Skills</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('skill-level') }}"><i class="nav-icon la la-question"></i> Skill levels</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('skill-user') }}"><i class="nav-icon la la-question"></i> Skill users</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('resource') }}"><i class="nav-icon la la-question"></i> Resources</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('service') }}"><i class="nav-icon la la-question"></i> Services</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('land-type') }}"><i class="nav-icon la la-question"></i> Land types</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('land') }}"><i class="nav-icon la la-question"></i> Lands</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('farm') }}"><i class="nav-icon la la-question"></i> Farms</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('service-use') }}"><i class="nav-icon la la-question"></i> Service uses</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('order') }}"><i class="nav-icon la la-question"></i> Orders</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('userdata') }}"><i class="nav-icon la la-question"></i> Userdatas</a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> Authentication</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}">&nbsp;&nbsp;<i class="nav-icon la la-user"></i> <span>Users</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}">&nbsp;&nbsp;<i class="nav-icon la la-id-badge"></i> <span>Roles</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}">&nbsp;&nbsp;<i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
    </ul>
</li>


{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-question"></i> Users</a></li>--}}



