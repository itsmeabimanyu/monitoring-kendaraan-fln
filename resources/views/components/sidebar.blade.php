  <!-- Sidebar -->
  @auth
  @if(in_array(auth()->user()->jabatan, ['Admin GA', 'Staff GA']))
  <div class="bg-primary text-white p-3" style="width: 250px; min-height: 100vh;">
      <h4 class="text-center mb-4">FLN GA</h4>

      <a href="/admin" class="text-white d-block py-2 px-3 text-decoration-none">Dashboard</a>
      <a href="{{ route('list.users') }}" class="text-white d-block py-2 px-3 text-decoration-none">Users</a>

      <!-- Kendaraan Menu -->
      <a class="text-white d-block py-2 px-3 text-decoration-none" data-bs-toggle="collapse" href="#kendaraanMenu" role="button" aria-expanded="false" aria-controls="kendaraanMenu">
          Kendaraan
      </a>
      <div class="collapse" id="kendaraanMenu">
          {{-- <a href="{{ route('list.kendaraan') }}" class="text-white d-block py-1 ps-5 text-decoration-none">• List Kendaraan</a> --}}
          <a href="{{ route('kendaraan') }}" class="text-white d-block py-1 ps-5 text-decoration-none">• List Kendaraan</a>
          {{-- <a href="{{ route('monitoring.kendaraan') }}" class="text-white d-block py-1 ps-5 text-decoration-none">• Monitoring</a> --}}
          <a href="{{ route('history.kendaraan') }}" class="text-white d-block py-1 ps-5 text-decoration-none">• History</a>
      </div>

      <form action="{{ route('logout') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="text-white d-block py-2 px-3 text-decoration-none bg-transparent border-0">
              Logout
          </button>
      </form>
  </div>
  @endif
  @endauth
