{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('currency') }}"><i class="nav-icon la la-question"></i> Currencies</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('balance') }}"><i class="nav-icon la la-question"></i> Balances</a></li>