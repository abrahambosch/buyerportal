<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Buyer Portal</title>

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="/style.css" rel="stylesheet">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body id="app-layout" class="{{ $body_class or '' }}">
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    Buyer Portal
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    @if (Auth::check() && Auth::user()->user_type == 'supplier')
                        <li @if (Request::is('home') || Request::is('home/*')) class="active" @endif><a href="{{ url('/home') }}">Home</a></li>
                        <li @if (Request::is('supplier_product') || Request::is('supplier_product/*')) class="active" @endif><a href="{{ route('supplier_product.index') }}">Products</a></li>
                        <li @if (Request::is('supplier_product_list') || Request::is('supplier_product_list/*')) class="active" @endif><a href="{{ route('supplier_product_list.index') }}">Product Lists</a></li>
                        <li @if (Request::is('purchase_order') || Request::is('purchase_order/*')) class="active" @endif><a href="{{ route('purchase_order.index') }}">Offers</a></li>
                    @else
                        <li @if (Request::is('home') || Request::is('home/*')) class="active" @endif><a href="{{ url('/home') }}">Home</a></li>
                        <li @if (Request::is('supplier') || Request::is('pupplier/*')) class="active" @endif><a href="{{ route('supplier.index') }}">Suppliers</a></li>
                        <li @if (Request::is('product') || Request::is('product/*')) class="active" @endif><a href="{{ route('product.index') }}">Products</a></li>
                        <li @if (Request::is('product_list') || Request::is('product_list/*')) class="active" @endif><a href="{{ route('product_list.index') }}">Product Lists</a></li>
                        <li @if (Request::is('purchase_order') || Request::is('purchase_order/*')) class="active" @endif><a href="{{ route('purchase_order.index') }}">Offers</a></li>
                    @endif
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (!Auth::check())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ ucfirst(Auth::user()->user_type) }} | {{ Auth::user()->first_name }} {{ Auth::user()->last_name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @if (!empty($errors) && count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
    @endif


    @yield('content')

    <!-- JavaScripts -->

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
</body>
</html>
