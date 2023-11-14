<!DOCTYPE html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ config('app.name', 'AE') }}</title>
    <meta name="description" content="{{ __('profile::login.about') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/usr/modules/profile/UI/WEB/Resources/Assets/css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="/usr/modules/profile/UI/WEB/Resources/Assets/fonts/css/all.min.css">
    <!-- Flaticon CSS -->
    <link rel="stylesheet" href="/usr/modules/profile/UI/WEB/Resources/Assets/fonts/flaticon.css">
    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/usr/modules/profile/UI/WEB/Resources/Assets/css/style.css">
</head>

<body>

@yield('content')

<!-- jquery-->
<script src="/usr/modules/profile/UI/WEB/Resources/Assets/js/jquery-3.5.0.min.js"></script>
<!-- Bootstrap js -->
<script src="/usr/modules/profile/UI/WEB/Resources/Assets/js/bootstrap.min.js"></script>
<!-- Imagesloaded js -->
<script src="/usr/modules/profile/UI/WEB/Resources/Assets/js/imagesloaded.pkgd.min.js"></script>
<!-- Custom Js -->
<script src="/usr/modules/profile/UI/WEB/Resources/Assets/js/main.js"></script>

</body>
</html>