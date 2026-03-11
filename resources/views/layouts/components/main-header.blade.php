
		<header class="app-header sticky" id="header">

			<!-- Start::main-header-container -->
			<div class="main-header-container container-fluid">

				<!-- Start::header-content-left -->
				<div class="header-content-left">

					<!-- Start::header-element -->
					<div class="header-element">
						<div class="horizontal-logo">
							<a href="{{url('index')}}" class="header-logo">
								<img src="{{asset('build/assets/images/brand-logos/desktop-logo.png')}}" alt="logo" class="desktop-logo">
								<img src="{{asset('build/assets/images/brand-logos/toggle-logo.png')}}" alt="logo" class="toggle-logo">
								<img src="{{asset('build/assets/images/brand-logos/desktop-dark.png')}}" alt="logo" class="desktop-dark">
								<img src="{{asset('build/assets/images/brand-logos/toggle-dark.png')}}" alt="logo" class="toggle-dark">
							</a>
						</div>
					</div>
					<!-- End::header-element -->

					<!-- Start::header-element -->
					<div class="header-element mx-lg-0 mx-2">
						<a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a>
					</div>
					<!-- End::header-element -->

				</div>
				<!-- End::header-content-left -->

				<!-- Start::header-content-right -->
				<ul class="header-content-right">

					<!-- Start::header-element -->
					<li class="header-element dropdown">
						<a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
							<div>
								<span class="avatar avatar-sm bg-primary-transparent avatar-rounded d-flex align-items-center justify-content-center">
									<i class="ti ti-user fs-18"></i>
								</span>
							</div>
						</a>
						<div class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end" aria-labelledby="mainHeaderProfile">
							<div class="p-3 bg-primary text-fixed-white">
								<p class="mb-0 fs-15 fw-semibold">{{ auth()->user()->name ?? 'Usuario' }}</p>
								<small class="opacity-75">{{ auth()->user()->email ?? '' }}</small>
							</div>
							<div class="dropdown-divider"></div>
							<ul class="list-unstyled mb-0">
								<li>
									<form id="header-logout-form" action="{{ route('usuarios', ['accion' => 'logout']) }}" method="POST" style="display:none;">
										@csrf
									</form>
									<a class="dropdown-item d-flex align-items-center text-danger fw-semibold py-2"
									   href="#"
									   onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();">
										<i class="ti ti-logout me-2 fs-18"></i>Cerrar Sesión
									</a>
								</li>
							</ul>
						</div>
					</li>
					<!-- End::header-element -->

				</ul>
				<!-- End::header-content-right -->

			</div>
			<!-- End::main-header-container -->

		</header>
