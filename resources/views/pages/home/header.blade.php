<header role="banner">
  <div class="top-bar">
    <div class="container">
      <div class="row">
        <div class="col-lg-9 col-sm-6 social">
          @if($twitter != '#')
          <a href="{{ $twitter }}" target="_blank"><i class="fab fa-twitter"></i></a>
          @endif
          @if($facebook != '#')
          <a href="{{ $facebook }}" target="_blank"><i class="fab fa-facebook"></i></a>
          @endif
          @if($instagram != '#')
          <a href="{{ $instagram }}" target="_blank"><i class="fab fa-instagram"></i></a>
          @endif
          @if($youtube != '#')
          <a href="{{ $youtube }}" target="_blank"><i class="fab fa-youtube"></i></a>
          @endif
          <a href="#"><i class="fas fa-envelope mr-2"></i>{{ $email }}</a>
          <a href="#"><i class="fas fa-phone-alt"></i>{{ $phone }}</a>
        </div>
        <div class="col-lg-3 col-sm-6">
          <!-- <a href="#"><span class="fa fa-search"></span></a> -->
          <form class="form-inline ml-3" method="GET" action="{{ url('/') }}">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" name="search" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar btn-info" type="submit">
                  <i class="fas fa-search white"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="container logo-wrap">
    <div class="row pt-5">
      <div class="col-12 text-center">
        <div class="site-branding">
            <h1 class="site-title"><a href="{{ url('/') }}">{{ ucfirst($url) }}</a></h1>
            <!-- <p class="site-description">Personal Blog</p> -->
        </div><!-- .site-branding -->
        <!-- <h1 class="site-logo"><a href="{{ url('/') }}">{{ $url }}</a></h1> -->
      </div>
    </div>
  </div>
  
  <div class="container">
    <div class="d-flex justify-content-center">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
          <div class="navbar-nav px-2">
            <a class="nav-item nav-link {{ Request::path() == '/'? 'active': ''}}" href="{{ url('/') }}">BLOG <span class="sr-only">(current)</span></a>
            <a class="nav-item nav-link {{ Request::path() == 'toko'? 'active' : ''}}" href="{{ url('/toko') }}">TOKO </a>
          </div>
        </div>
      </nav>
    </div>
  </div>
</header>