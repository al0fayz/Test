<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="/img/logo.png" alt="Pandi Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light"><P>Ahmad</P></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ $images }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ $user }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <router-link to="/user/dashboard" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/user/blog" class="nav-link">
            <i class="nav-icon fas fa-at"></i>
              <p>
              Upload Blog
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/user/blogList" class="nav-link">
            <i class="nav-icon fas fa-clipboard-list"></i>
              <p>
              Blog List
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/user/blogCategory" class="nav-link">
            <i class="nav-icon fas fa-quote-right"></i>
              <p>
              Blog Category
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/user/member" class="nav-link">
            <i class="nav-icon fas fa-users-cog"></i>
              <p>
              User
              </p>
            </router-link>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>