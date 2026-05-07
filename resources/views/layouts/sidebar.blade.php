<aside id="sidebar" class="sidebar">
    <div class="d-flex justify-content-between align-items-center px-4 pt-3 pb-2 d-lg-none">
        <span class="fw-bold text-primary">Menu</span>
        <button class="btn btn-light btn-sm border-0" id="closeSidebar">
            <i class="ti ti-x fs-4"></i>
        </button>
    </div>
    <div class="px-4 py-3 border-bottom border-dashed mb-2">
        <div class="d-flex align-items-center gap-3">
            <div class="icon-shape bg-primary text-white rounded-circle" style="width: 35px; height: 35px;">
                <i class="ti ti-user fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h6 class="mb-0 text-truncate" style="font-size: 0.85rem;">{{ Auth::user()->name }}</h6>
                <span class="badge bg-light-primary text-primary text-uppercase p-1" style="font-size: 0.6rem;">{{ Auth::user()->role }}</span>
            </div>
        </div>
    </div>
    <ul class="nav flex-column">
      <li class="px-4 py-2"><small class="nav-text">Main</small></li>
      <li>
        <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
          <i class="ti ti-home"></i><span class="nav-text">Dashboard</span>
        </a>
      </li>
      
      @if(auth()->user()->role == 'admin')
      <li>
        <a class="nav-link {{ Request::routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}">
          <i class="ti ti-database"></i><span class="nav-text">Data Transaksi</span>
        </a>
      </li>
      <li>
        <a class="nav-link {{ Request::routeIs('apriori.*') ? 'active' : '' }}" href="{{ route('apriori.index') }}">
          <i class="ti ti-settings"></i><span class="nav-text">Proses Apriori</span>
        </a>
      </li>
      @endif

      <li>
        <a class="nav-link {{ Request::routeIs('history.*') ? 'active' : '' }}" href="{{ route('history.index') }}">
          <i class="ti ti-history"></i><span class="nav-text">Riwayat Analisis</span>
        </a>
      </li>
      
      <li class="px-4 pt-4 pb-2"><small class="nav-text">Settings</small></li>
      @if(auth()->user()->role == 'owner')
      <li>
        <a class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
          <i class="ti ti-users"></i><span class="nav-text">Kelola Akun</span>
        </a>
      </li>
      @endif
      <li>
        <form action="{{ route('logout') }}" method="POST" id="sidebar-logout-form" class="d-none">
            @csrf
        </form>
        <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
          <i class="ti ti-logout"></i><span class="nav-text">Logout</span>
        </a>
      </li>
    </ul>
</aside>
