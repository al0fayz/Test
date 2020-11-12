@extends('pages.home.app')
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 d-flex justify-content-center">
                    <div class="login-box">
                        <div class="login-logo text-light">
                            <b>Login</b>
                        </div>
                        @include('pages.home.notif')
                        <div class="card">
                            <div class="card-body login-card-body">
                            <form action="{{ url('/login') }}" method="post">
                                @csrf
                                <div class="input-group mb-3">
                                    <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" value="" name="username" placeholder="Username or Email" required autofocus>
                                    <div class="input-group-append input-group-text">
                                        <span class="fas fa-envelope blue"></span>
                                    </div>
                                </div>
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="input-group mb-3">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" value="" name="password" placeholder="password" required>
                                    <div class="input-group-append input-group-text">
                                        <span class="fas fa-lock blue"></span>
                                    </div>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="social-auth-links text-center mb-3">
                                    <br />
                                    <button type="submit" class="btn btn-block btn-primary">
                                        Login
                                    </button>
                                </div>
                                <!-- <a href="/reset/password"><i>Forgot Password </i></a> -->

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection