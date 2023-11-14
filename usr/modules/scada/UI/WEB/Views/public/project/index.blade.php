<html class="k-webkit k-webkit92">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="Simple-Scada Web">
        <meta name="application-name" content="Simple-Scada Web">
        <meta name="msapplication-TileColor" content="#00aba9">
        <meta name="theme-color" content="#312E2B">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="robots" content="noindex,nofollow">
        <style>@font-face{font-family: 'RobotoLight'; font-display: swap; src: url('styles/fonts/RobotoLight.eot'); src: url('styles/fonts/RobotoLight.eot') format('embedded-opentype'), url('styles/fonts/RobotoLight.woff2') format('woff2'), url('styles/fonts/RobotoLight.woff') format('woff'), url('styles/fonts/RobotoLight.ttf') format('truetype'), url('styles/fonts/RobotoLight.svg#RobotoLight') format('svg');}@font-face{font-family: 'RobotoMedium'; font-display: swap; src: url('styles/fonts/RobotoMedium.eot'); src: url('styles/fonts/RobotoMedium.eot') format('embedded-opentype'), url('styles/fonts/RobotoMedium.woff2') format('woff2'), url('styles/fonts/RobotoMedium.woff') format('woff'), url('styles/fonts/RobotoMedium.ttf') format('truetype'), url('styles/fonts/RobotoMedium.svg#RobotoMedium') format('svg');}@font-face{font-family: 'RobotoRegular'; font-display: swap; src: url('styles/fonts/RobotoRegular.eot'); src: url('styles/fonts/RobotoRegular.eot') format('embedded-opentype'), url('styles/fonts/RobotoRegular.woff2') format('woff2'), url('styles/fonts/RobotoRegular.woff') format('woff'), url('styles/fonts/RobotoRegular.ttf') format('truetype'), url('styles/fonts/RobotoRegular.svg#RobotoRegular') format('svg');}html{font-size:14px; font-family:"RobotoRegular", sans-serif;}</style>
        <title>
            Holm
        </title>
        <style>
            .bd-placeholder-img {
              font-size: 1.125rem;
              text-anchor: middle;
              -webkit-user-select: none;
              -moz-user-select: none;
              user-select: none;
            }
      
            @media (min-width: 768px) {
              .bd-placeholder-img-lg {
                font-size: 3.5rem;
              }
            }
          </style>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link href="https://getbootstrap.com/docs/5.0/examples/dashboard/dashboard.css" rel="stylesheet" >
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        <script>
            function getScadaAll(){
                $.post("ajax.php?do=getScadaAll", {
                    id: null
                    // id: // Было просто так ...
                }, function(data){
                    $("#result").html(data);
                });	
            }
            getScadaAll();
            let intervalScada = setInterval(() => {getScadaAll();}, 15000);
            function setScada()
            {

            let tag = $('#tagId').val();
            let value = $('#val').val();
            
            $.post("ajax.php?do=setCoilByTag", {tag, value}, function(data){
                    getScadaAll();
            });	
            }
        </script>
        
   
    </head>
    <body bgcolor="#E0E0E0" style="">
        
    
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
<a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Company name</a>
<button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
  <span class="navbar-toggler-icon"></span>
</button>
<input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
<div class="navbar-nav">
  <div class="nav-item text-nowrap">
    <a class="nav-link px-3" href="#">Sign out</a>
  </div>
</div>
</header>

<div class="container-fluid">
<div class="row">
  <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">
            <span data-feather="home"></span>
            Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span data-feather="file"></span>
            Orders
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span data-feather="shopping-cart"></span>
            Products
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span data-feather="users"></span>
            Customers
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span data-feather="bar-chart-2"></span>
            Reports
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span data-feather="layers"></span>
            Integrations
          </a>
        </li>
      </ul>

      <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
        <span>Saved reports</span>
        <a class="link-secondary" href="#" aria-label="Add a new report">
          <span data-feather="plus-circle"></span>
        </a>
      </h6>
      <ul class="nav flex-column mb-2">
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span data-feather="file-text"></span>
            Current month
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span data-feather="file-text"></span>
            Last quarter
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span data-feather="file-text"></span>
            Social engagement
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span data-feather="file-text"></span>
            Year-end sale
          </a>
        </li>
      </ul>
    </div>
  </nav>

  <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Dashboard</h1>
      <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
          <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
          <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
          <span data-feather="calendar"></span>
          This week
        </button>
      </div>
    </div>

    <canvas class="my-4 w-100" id="myChart" width="900" height="380"></canvas>

    <h2>Section title</h2>
    <div class="table-responsive">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Header</th>
            <th scope="col">Header</th>
            <th scope="col">Header</th>
            <th scope="col">Header</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1,001</td>
            <td>random</td>
            <td>data</td>
            <td>placeholder</td>
            <td>text</td>
          </tr>
          <tr>
            <td>1,002</td>
            <td>placeholder</td>
            <td>irrelevant</td>
            <td>visual</td>
            <td>layout</td>
          </tr>
          <tr>
            <td>1,003</td>
            <td>data</td>
            <td>rich</td>
            <td>dashboard</td>
            <td>tabular</td>
          </tr>
          <tr>
            <td>1,003</td>
            <td>information</td>
            <td>placeholder</td>
            <td>illustrative</td>
            <td>data</td>
          </tr>
          <tr>
            <td>1,004</td>
            <td>text</td>
            <td>random</td>
            <td>layout</td>
            <td>dashboard</td>
          </tr>
          <tr>
            <td>1,005</td>
            <td>dashboard</td>
            <td>irrelevant</td>
            <td>text</td>
            <td>placeholder</td>
          </tr>
          <tr>
            <td>1,006</td>
            <td>dashboard</td>
            <td>illustrative</td>
            <td>rich</td>
            <td>data</td>
          </tr>
          <tr>
            <td>1,007</td>
            <td>placeholder</td>
            <td>tabular</td>
            <td>information</td>
            <td>irrelevant</td>
          </tr>
          <tr>
            <td>1,008</td>
            <td>random</td>
            <td>data</td>
            <td>placeholder</td>
            <td>text</td>
          </tr>
          <tr>
            <td>1,009</td>
            <td>placeholder</td>
            <td>irrelevant</td>
            <td>visual</td>
            <td>layout</td>
          </tr>
          <tr>
            <td>1,010</td>
            <td>data</td>
            <td>rich</td>
            <td>dashboard</td>
            <td>tabular</td>
          </tr>
          <tr>
            <td>1,011</td>
            <td>information</td>
            <td>placeholder</td>
            <td>illustrative</td>
            <td>data</td>
          </tr>
          <tr>
            <td>1,012</td>
            <td>text</td>
            <td>placeholder</td>
            <td>layout</td>
            <td>dashboard</td>
          </tr>
          <tr>
            <td>1,013</td>
            <td>dashboard</td>
            <td>irrelevant</td>
            <td>text</td>
            <td>visual</td>
          </tr>
          <tr>
            <td>1,014</td>
            <td>dashboard</td>
            <td>illustrative</td>
            <td>rich</td>
            <td>data</td>
          </tr>
          <tr>
            <td>1,015</td>
            <td>random</td>
            <td>tabular</td>
            <td>information</td>
            <td>text</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>
</div>
</div>


       
       
        <div id="simplescadadiv d-none">
            <div id="simplescadaintf" style=" left: 0px; top: 0px; width: 2560px; height: 1600px; background-color: rgb(97, 97, 97); overflow: hidden; transform-origin: left top; transform: scale(0.605625);">
                <div id="sswndblock"></div>
                <div id="p0s0o0w_" data-cl="#0080FF" style="position: absolute; left: 1112px; top: 392px; width: 496px; height: 521px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 16px; top: 48px; width: 32px; height: 425px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 473px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 48px; top: 473px; width: 400px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 448px; top: 473px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o1w_rot" style="position: absolute; left: 1565px; top: 376px; width: 65px; height: 65px; opacity: 1; display: block;">
                    <div id="p0s0o1w_" data-w="65" data-h="65" data-clw="65" data-clh="65" data-i="-1" data-cl="#fff" style="left: 1565px; top: 376px; width: 65px; height: 65px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-WpFMACPxAqullyVc.png&quot;) 0px 0px / 65px 65px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-WpFMACPxAqullyVc.png&quot;); -webkit-mask-size: 65px 65px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o2w_" data-cl="#FF0000" style="position: absolute; left: 824px; top: 552px; width: 450px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 322px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 386px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o3w_" data-cl="#0080FF" style="position: absolute; left: 535px; top: 248px; width: 425px; height: 207px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 16px; top: 48px; width: 32px; height: 111px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 159px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 48px; top: 159px; width: 329px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 377px; top: 159px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o4w_" data-cl="#0080FF" style="position: absolute; left: 752px; top: 688px; width: 530px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 402px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 466px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o5w_" data-cl="#0080FF" style="position: absolute; left: 752px; top: 232px; width: 528px; height: 1040px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 16px; top: 64px; width: 48px; height: 912px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 976px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 64px; top: 976px; width: 400px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 464px; top: 976px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o6w_" data-cl="#FF0000" style="position: absolute; left: 828px; top: 96px; width: 448px; height: 1040px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 16px; top: 64px; width: 48px; height: 912px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 976px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 64px; top: 976px; width: 320px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 384px; top: 976px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o7w_" data-cl="#0080FF" style="position: absolute; left: 1762px; top: 1190px; width: 289px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 161px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 225px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o8w_" data-cl="#0080FF" style="position: absolute; left: 1571px; top: 249px; width: 379px; height: 208px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 331px; top: 48px; width: 32px; height: 112px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 331px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 331px; top: 160px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p3.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p3.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 48px; top: 160px; width: 283px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 160px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                </div>
                <div id="p0s0o9w_" data-cl="#0080FF" style="position: absolute; left: 1815px; top: 233px; width: 233px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 105px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 169px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o10w_" data-cl="#0080FF" style="position: absolute; left: 1898px; top: 1319px; width: 144px; height: 64px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 47px; top: 17px; width: 50px; height: 30px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 17px; top: 17px; width: 30px; height: 30px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 97px; top: 17px; width: 30px; height: 30px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o11w_rot" style="position: absolute; left: 1685px; top: 265px; width: 127px; height: 84px; opacity: 1; display: block;">
                    <div id="p0s0o11w_" data-w="127" data-h="84" data-clw="127" data-clh="84" data-i="-1" data-cl="#fff" style="left: 1685px; top: 265px; width: 127px; height: 84px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;) 0px 0px / 127px 84px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;); -webkit-mask-size: 127px 84px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o12w_" data-cl="#0080FF" style="position: absolute; left: 1570px; top: 1199px; width: 328px; height: 184px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 17px; top: 47px; width: 30px; height: 90px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 17px; top: 17px; width: 30px; height: 30px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 17px; top: 137px; width: 30px; height: 30px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 47px; top: 137px; width: 234px; height: 30px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 281px; top: 137px; width: 30px; height: 30px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o13w_rot" style="position: absolute; left: 1474px; top: 1073px; width: 107px; height: 193px; opacity: 1; display: block;">
                    <div id="p0s0o13w_" data-w="107" data-h="193" data-clw="107" data-clh="193" data-i="-1" data-cl="#fff" style="left: 1474px; top: 1073px; width: 107px; height: 193px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pto4-new.png&quot;) 0px 0px / 107px 193px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pto4-new.png&quot;); -webkit-mask-size: 107px 193px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o14w_rot" style="position: absolute; left: 1464px; top: 115px; width: 107px; height: 193px; opacity: 1; display: block;">
                    <div id="p0s0o14w_" data-w="107" data-h="193" data-clw="107" data-clh="193" data-i="-1" data-cl="#fff" style="left: 1464px; top: 115px; width: 107px; height: 193px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pto4-new.png&quot;) 0px 0px / 107px 193px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pto4-new.png&quot;); -webkit-mask-size: 107px 193px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o15w_rot" style="position: absolute; left: 1842px; top: 1029px; width: 62px; height: 64px; opacity: 1; display: block;">
                    <div id="p0s0o15w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1842px; top: 1029px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o16w_rot" style="position: absolute; left: 1289px; top: 205px; width: 62px; height: 64px; opacity: 1; display: block;">
                    <div id="p0s0o16w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1289px; top: 205px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o17w_" data-cl="#FF0000" style="position: absolute; left: 56px; top: 96px; width: 256px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 128px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e23.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e23.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 192px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o18w_" data-cl="#0080FF" style="position: absolute; left: 1754px; top: 192px; width: 136px; height: 168px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 8px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 72px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p14.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p14.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 72px; top: 64px; width: 48px; height: 40px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 72px; top: 104px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p3.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p3.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 64px; top: 104px; width: 8px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 104px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                </div>
                <table id="p0s0o19w_tb" style="position: absolute; border-spacing: 0px; left: 16px; top: 120px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o19w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                T1
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o20w_tb" style="position: absolute; border-spacing: 0px; left: 16px; top: 248px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o20w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                T2
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o21w_tb" style="position: absolute; border-spacing: 0px; left: 2081px; top: 256px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o21w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                T21
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o22w_tb" style="position: absolute; border-spacing: 0px; left: 2090px; top: 1217px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o22w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                T4
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o23w_tb" style="position: absolute; border-spacing: 0px; left: 2090px; top: 1329px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o23w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                B1
                            </td>
                        </tr>
                    </tbody>
                </table>
                <svg version="1.1" id="p0s0o24w_" viewBox="0 0 46 352" class="s-unsel" style="position: absolute; left: 170px; top: 832px; width: 46px; height: 352px; fill: rgb(255, 255, 255); cursor: default;">
                    <rect x="36" y="0" width="10" height="2"></rect>
                    <rect x="40" y="8.75" width="6" height="1"></rect>
                    <text x="28" y="10.5" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        50
                    </text>
                    <rect x="36" y="17.5" width="10" height="2"></rect>
                    <rect x="40" y="26.25" width="6" height="1"></rect>
                    <text x="28" y="24.5" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        45
                    </text>
                    <rect x="36" y="35" width="10" height="2"></rect>
                    <rect x="40" y="43.75" width="6" height="1"></rect>
                    <text x="28" y="42" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        40
                    </text>
                    <rect x="36" y="52.5" width="10" height="2"></rect>
                    <rect x="40" y="61.25" width="6" height="1"></rect>
                    <text x="28" y="59.5" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        35
                    </text>
                    <rect x="36" y="70" width="10" height="2"></rect>
                    <rect x="40" y="78.75" width="6" height="1"></rect>
                    <text x="28" y="77" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        30
                    </text>
                    <rect x="36" y="87.5" width="10" height="2"></rect>
                    <rect x="40" y="96.25" width="6" height="1"></rect>
                    <text x="28" y="94.5" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        25
                    </text>
                    <rect x="36" y="105" width="10" height="2"></rect>
                    <rect x="40" y="113.75" width="6" height="1"></rect>
                    <text x="28" y="112" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        20
                    </text>
                    <rect x="36" y="122.5" width="10" height="2"></rect>
                    <rect x="40" y="131.25" width="6" height="1"></rect>
                    <text x="28" y="129.5" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        15
                    </text>
                    <rect x="36" y="140" width="10" height="2"></rect>
                    <rect x="40" y="148.75" width="6" height="1"></rect>
                    <text x="28" y="147" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        10
                    </text>
                    <rect x="36" y="157.5" width="10" height="2"></rect>
                    <rect x="40" y="166.25" width="6" height="1"></rect>
                    <text x="28" y="164.5" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        5
                    </text>
                    <rect x="36" y="175" width="10" height="2"></rect>
                    <rect x="40" y="183.75" width="6" height="1"></rect>
                    <text x="28" y="182" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        0
                    </text>
                    <rect x="36" y="192.5" width="10" height="2"></rect>
                    <rect x="40" y="201.25" width="6" height="1"></rect>
                    <text x="28" y="199.5" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        -5
                    </text>
                    <rect x="36" y="210" width="10" height="2"></rect>
                    <rect x="40" y="218.75" width="6" height="1"></rect>
                    <text x="28" y="217" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        -10
                    </text>
                    <rect x="36" y="227.5" width="10" height="2"></rect>
                    <rect x="40" y="236.25" width="6" height="1"></rect>
                    <text x="28" y="234.5" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        -15
                    </text>
                    <rect x="36" y="245" width="10" height="2"></rect>
                    <rect x="40" y="253.75" width="6" height="1"></rect>
                    <text x="28" y="252" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        -20
                    </text>
                    <rect x="36" y="262.5" width="10" height="2"></rect>
                    <rect x="40" y="271.25" width="6" height="1"></rect>
                    <text x="28" y="269.5" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        -25
                    </text>
                    <rect x="36" y="280" width="10" height="2"></rect>
                    <rect x="40" y="288.75" width="6" height="1"></rect>
                    <text x="28" y="287" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        -30
                    </text>
                    <rect x="36" y="297.5" width="10" height="2"></rect>
                    <rect x="40" y="306.25" width="6" height="1"></rect>
                    <text x="28" y="304.5" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        -35
                    </text>
                    <rect x="36" y="315" width="10" height="2"></rect>
                    <rect x="40" y="323.75" width="6" height="1"></rect>
                    <text x="28" y="322" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        -40
                    </text>
                    <rect x="36" y="332.5" width="10" height="2"></rect>
                    <rect x="40" y="341.25" width="6" height="1"></rect>
                    <text x="28" y="339.5" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        -45
                    </text>
                    <rect x="36" y="350" width="10" height="2"></rect>
                    <text x="28" y="350" font-size="14" fill="#FFFFFFFF" text-anchor="end">
                        -50
                    </text>
                </svg>
                <div id="p0s0o25w_" data-min="-50" data-max="50" data-fz="0" data-ps="0" style="position: absolute; left: 216px; top: 832px; width: 8px; height: 360px; box-sizing: border-box; background-color: rgb(119, 119, 119); border-color: rgb(0, 0, 0); border-style: solid; border-width: 2px;" data-cl="#FF0000FF">
                    <div id="p0s0o25w_lvl" style="position: absolute; background-color: rgb(255, 0, 0); width: 100%; height: 50%; bottom: 0px;"></div>
                </div>
                <table id="p0s0o26w_tb" style="position: absolute; border-spacing: 0px; left: 184px; top: 688px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o26w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="5.95" style="font-size: 24px; font-family: RobotoRegular; letter-spacing: -0.7px; line-height: 16px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Температура наружного воздуха
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o27w_tb" style="position: absolute; border-spacing: 0px; left: 1770px; top: 167px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o27w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                М1
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o28w_tb" style="position: absolute; border-spacing: 0px; left: 1771px; top: 336px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o28w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                М2
                            </td>
                        </tr>
                    </tbody>
                </table>
                <svg version="1.1" id="p0s0o29w_" data-cl="#FF0000FF" viewBox="208 1176 24 24" style="position: absolute; left: 208px; top: 1176px; width: 24px; height: 24px; fill: rgb(255, 0, 0); stroke: rgb(0, 0, 0); stroke-width: 0px; display: block;">
                    <ellipse cx="220" cy="1188" rx="10" ry="10"></ellipse>
                    <ellipse cx="220" cy="1188" rx="11" ry="11" fill="none" stroke-width="2"></ellipse>
                </svg>
                <div id="p0s0o30w_rot" style="position: absolute; left: 1333px; top: 89px; width: 62px; height: 64px; opacity: 1; display: block;">
                    <div id="p0s0o30w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1333px; top: 89px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-IeIc3zzwPc9AUMYx.png&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-IeIc3zzwPc9AUMYx.png&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o31w_rot" style="position: absolute; left: 268px; top: 96px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o31w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 268px; top: 96px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o32w_rot" style="position: absolute; left: 1231px; top: 98px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o32w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 1231px; top: 98px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o33w_" data-cl="#FF0000" style="position: absolute; left: 1252px; top: 97px; width: 132px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 4px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 68px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o34w_rot" style="position: absolute; left: 1768px; top: 72px; width: 62px; height: 64px; opacity: 1; display: block;">
                    <div id="p0s0o34w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1768px; top: 72px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o35w_" data-cl="#FF0000" style="position: absolute; left: 1349px; top: 97px; width: 188px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 60px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 124px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e14.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e14.png&quot;);"></div>
                </div>
                <div id="p0s0o36w_" data-cl="#FF0000" style="position: absolute; left: 1499px; top: 97px; width: 547px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 419px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e12.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e12.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 483px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p19.png&quot;);"></div>
                </div>
                <div id="p0s0o37w_rot" style="position: absolute; left: 2000px; top: 97px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o37w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 2000px; top: 97px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o38w_rot" style="position: absolute; left: 2002px; top: 233px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o38w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 2002px; top: 233px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o39w_" data-cl="#0080FF" style="position: absolute; left: 1500px; top: 231px; width: 176px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e12.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e12.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 112px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o40w_" data-cl="#0080FF" style="position: absolute; left: 1600px; top: 192px; width: 136px; height: 168px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 8px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p7.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p7.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 72px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 64px; width: 48px; height: 40px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 104px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 64px; top: 104px; width: 8px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 72px; top: 104px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o41w_rot" style="position: absolute; left: 267px; top: 231px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o41w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 267px; top: 231px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o42w_" data-cl="#0080FF" style="position: absolute; left: 56px; top: 230px; width: 258px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 130px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e26.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e26.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 194px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o43w_" data-cl="#0080FF" style="position: absolute; left: 1258px; top: 1189px; width: 288px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 160px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 224px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e14.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e14.png&quot;);"></div>
                </div>
                <div id="p0s0o44w_rot" style="position: absolute; left: 1238px; top: 1058px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o44w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 1238px; top: 1058px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o45w_" data-cl="#FF0000" style="position: absolute; left: 1258px; top: 1057px; width: 132px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 4px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 68px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o46w_rot" style="position: absolute; left: 1338px; top: 1049px; width: 62px; height: 64px; opacity: 1; display: block;">
                    <div id="p0s0o46w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1338px; top: 1049px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-IeIc3zzwPc9AUMYx.png&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-IeIc3zzwPc9AUMYx.png&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o47w_" data-cl="#FF0000" style="position: absolute; left: 1354px; top: 1055px; width: 192px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 64px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 128px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e14.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e14.png&quot;);"></div>
                </div>
                <div id="p0s0o48w_rot" style="position: absolute; left: 1238px; top: 1191px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o48w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 1238px; top: 1191px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o49w_rot" style="position: absolute; left: 2004px; top: 1057px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o49w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 2004px; top: 1057px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o50w_rot" style="position: absolute; left: 2007px; top: 1191px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o50w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 2007px; top: 1191px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o51w_rot" style="position: absolute; left: 2008px; top: 1329px; width: 38px; height: 39px; opacity: 1; display: block;">
                    <div id="p0s0o51w_" data-w="38" data-h="39" data-clw="38" data-clh="39" data-i="-1" data-cl="#fff" style="left: 2008px; top: 1329px; width: 38px; height: 39px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 38px 39px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 38px 39px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o52w_" data-cl="#FF0000" style="position: absolute; left: 1509px; top: 1055px; width: 540px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 412px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e12.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e12.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 476px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <table id="p0s0o53w_tb" style="position: absolute; border-spacing: 0px; left: 2080px; top: 112px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o53w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                T11
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o54w_tb" style="position: absolute; border-spacing: 0px; left: 2090px; top: 1073px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o54w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                T3
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o55w_rot" style="position: absolute; left: 1685px; top: 177px; width: 127px; height: 84px; opacity: 1; display: block;">
                    <div id="p0s0o55w_" data-w="127" data-h="84" data-clw="127" data-clh="84" data-i="-1" data-cl="#fff" style="left: 1685px; top: 177px; width: 127px; height: 84px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;) 0px 0px / 127px 84px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;); -webkit-mask-size: 127px 84px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o56w_rot" style="position: absolute; left: 1721px; top: 296px; width: 48px; height: 48px; opacity: 1; display: block;">
                    <div id="p0s0o56w_" data-w="144" data-h="96" data-clw="48" data-clh="48" data-i="-1" data-cl="#FF0000" style="left: 1721px; top: 296px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;) -48px -48px / 144px 96px rgb(255, 0, 0); background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;); -webkit-mask-size: 144px 96px; -webkit-mask-position: -48px -48px;"></div>
                </div>
                <div id="p0s0o57w_rot" style="position: absolute; left: 1721px; top: 209px; width: 48px; height: 48px; opacity: 1; display: block;">
                    <div id="p0s0o57w_" data-w="144" data-h="96" data-clw="48" data-clh="48" data-i="-1" data-cl="#FF0000" style="left: 1721px; top: 209px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;) -48px -48px / 144px 96px rgb(255, 0, 0); background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;); -webkit-mask-size: 144px 96px; -webkit-mask-position: -48px -48px;"></div>
                </div>
                <table id="p0s0o58w_cpttb" style="position: absolute; border-spacing: 0px; left: 1320px; top: 176px; width: 40px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o58w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0.196); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                °С
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o58w_" class="s-unsel" tabindex="0" data-vl="60" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1280px; top: 176px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    60
                </div>
                <table id="p0s0o59w_cpttb" style="position: absolute; border-spacing: 0px; left: 1872px; top: 984px; width: 40px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o59w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                °С
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o59w_" class="s-unsel" tabindex="0" data-vl="60" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1832px; top: 984px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    60
                </div>
                <table id="p0s0o60w_cpttb" style="position: absolute; border-spacing: 0px; left: 1800px; top: 32px; width: 40px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o60w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                °С
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o60w_" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1760px; top: 32px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    0
                </div>
                <div id="p0s0o61w_rot" style="position: absolute; left: 1370px; top: 1217px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o61w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1370px; top: 1217px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o62w_rot" style="position: absolute; left: 1074px; top: 1130px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o62w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1074px; top: 1130px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o63w_rot" style="position: absolute; left: 962px; top: 937px; width: 49px; height: 21px; transform: rotate(270deg); opacity: 1; display: block;">
                    <div id="p0s0o63w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 962px; top: 937px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o64w_rot" style="position: absolute; left: 996px; top: 976px; width: 49px; height: 21px; transform: rotate(270deg); opacity: 1; display: block;">
                    <div id="p0s0o64w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 996px; top: 976px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o65w_rot" style="position: absolute; left: 1448px; top: 260px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o65w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1448px; top: 260px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o66w_" data-cl="#0080FF" style="position: absolute; left: 1256px; top: 231px; width: 280px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 152px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 216px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e14.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e14.png&quot;);"></div>
                </div>
                <div id="p0s0o67w_rot" style="position: absolute; left: 1416px; top: 259px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o67w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1416px; top: 259px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o68w_rot" style="position: absolute; left: 768px; top: 262px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o68w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 768px; top: 262px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o69w_rot" style="position: absolute; left: 836px; top: 125px; width: 57px; height: 21px; opacity: 1; display: block;">
                    <div id="p0s0o69w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 836px; top: 125px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o70w_rot" style="position: absolute; left: 1424px; top: 126px; width: 57px; height: 21px; opacity: 1; display: block;">
                    <div id="p0s0o70w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1424px; top: 126px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o71w_rot" style="position: absolute; left: 1744px; top: 126px; width: 57px; height: 21px; opacity: 1; display: block;">
                    <div id="p0s0o71w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1744px; top: 126px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o72w_rot" style="position: absolute; left: 1576px; top: 260px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o72w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1576px; top: 260px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o73w_rot" style="position: absolute; left: 1912px; top: 262px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o73w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1912px; top: 262px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o74w_rot" style="position: absolute; left: 1075px; top: 256px; width: 57px; height: 21px; transform: rotate(90deg); opacity: 1; display: block;">
                    <div id="p0s0o74w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1075px; top: 256px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o75w_rot" style="position: absolute; left: 1037px; top: 920px; width: 57px; height: 21px; transform: rotate(90deg); opacity: 1; display: block;">
                    <div id="p0s0o75w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1037px; top: 920px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o76w_rot" style="position: absolute; left: 1162px; top: 1166px; width: 57px; height: 21px; opacity: 1; display: block;">
                    <div id="p0s0o76w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1162px; top: 1166px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o77w_rot" style="position: absolute; left: 1415px; top: 1084px; width: 57px; height: 21px; opacity: 1; display: block;">
                    <div id="p0s0o77w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1415px; top: 1084px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o78w_rot" style="position: absolute; left: 1298px; top: 1086px; width: 57px; height: 24px; opacity: 1; display: block;">
                    <div id="p0s0o78w_" data-w="1140" data-h="24" data-clw="57" data-clh="24" data-i="-1" data-cl="#fff" style="left: 1298px; top: 1086px; width: 57px; height: 24px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px -24px / 1140px 24px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 24px; -webkit-mask-position: 0px -24px;"></div>
                </div>
                <div id="p0s0o79w_rot" style="position: absolute; left: 1882px; top: 1084px; width: 57px; height: 21px; opacity: 1; display: block;">
                    <div id="p0s0o79w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1882px; top: 1084px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o80w_rot" style="position: absolute; left: 1572px; top: 1084px; width: 57px; height: 21px; opacity: 1; display: block;">
                    <div id="p0s0o80w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1572px; top: 1084px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o81w_rot" style="position: absolute; left: 1625px; top: 1218px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o81w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1625px; top: 1218px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o82w_rot" style="position: absolute; left: 1802px; top: 1344px; width: 31px; height: 13px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o82w_" data-w="620" data-h="13" data-clw="31" data-clh="13" data-i="-1" data-cl="#fff" style="left: 1802px; top: 1344px; width: 31px; height: 13px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 620px 13px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 620px 13px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o83w_rot" style="position: absolute; left: 1235px; top: 232px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o83w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 1235px; top: 232px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o84w_" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="%" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 152px; top: 784px; border-radius: 2px; width: 112px; height: 32px; line-height: 32px; text-align: center; color: rgba(0, 0, 0, 0.7); background-color: rgb(255, 255, 255); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#FFFFFFFF">
                    0%
                </div>
                <div id="p0s0o85w_rot" style="position: absolute; left: 1853px; top: 1312px; width: 96px; height: 72px; opacity: 1; display: block;">
                    <div id="p0s0o85w_" data-w="96" data-h="72" data-clw="96" data-clh="72" data-i="-1" data-cl="#fff" style="left: 1853px; top: 1312px; width: 96px; height: 72px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-TPmbdwFt6zpDNJ.png&quot;) 0px 0px / 96px 72px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-TPmbdwFt6zpDNJ.png&quot;); -webkit-mask-size: 96px 72px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <table id="p0s0o86w_tb" style="position: absolute; border-spacing: 0px; left: 1778px; top: 1164px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o86w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                М5
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o87w_tb" style="position: absolute; border-spacing: 0px; left: 1040px; top: 328px; width: 56px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o87w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="5.95" style="font-size: 24px; font-family: RobotoRegular; letter-spacing: -0.7px; line-height: 30px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                М6
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o88w_" data-cl="#0080FF" style="position: absolute; left: 1510px; top: 1189px; width: 234px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 106px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e12.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e12.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 170px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o89w_rot" style="position: absolute; left: 1693px; top: 1174px; width: 127px; height: 84px; opacity: 1; display: block;">
                    <div id="p0s0o89w_" data-w="127" data-h="84" data-clw="127" data-clh="84" data-i="-1" data-cl="#fff" style="left: 1693px; top: 1174px; width: 127px; height: 84px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;) 0px 0px / 127px 84px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;); -webkit-mask-size: 127px 84px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o90w_rot" style="position: absolute; left: 1729px; top: 1206px; width: 48px; height: 48px; opacity: 1; display: block;">
                    <div id="p0s0o90w_" data-w="144" data-h="96" data-clw="48" data-clh="48" data-i="-1" data-cl="#FF0000" style="left: 1729px; top: 1206px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;) -48px -48px / 144px 96px rgb(255, 0, 0); background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;); -webkit-mask-size: 144px 96px; -webkit-mask-position: -48px -48px;"></div>
                </div>
                <div id="p0s0o91w_rot" style="position: absolute; left: 1586px; top: 1217px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o91w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1586px; top: 1217px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <table id="p0s0o92w_cpttb" style="position: absolute; border-spacing: 0px; left: 1408px; top: 176px; width: 40px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o92w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0.196); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                °С
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o92w_" class="s-unsel" tabindex="0" data-vl="60" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1368px; top: 176px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    60
                </div>
                <table id="p0s0o93w_cpttb" style="position: absolute; border-spacing: 0px; left: 1880px; top: 32px; width: 40px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o93w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0.196); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                °С
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o93w_" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1840px; top: 32px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    0
                </div>
                <table id="p0s0o94w_cpttb" style="position: absolute; border-spacing: 0px; left: 1956px; top: 984px; width: 40px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o94w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0.196); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                °С
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o94w_" class="s-unsel" tabindex="0" data-vl="60" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1916px; top: 984px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    60
                </div>
                <table id="p0s0o95w_tb" style="position: absolute; border-spacing: 0px; left: 136px; top: 368px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o95w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 24px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Система отопления
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o96w_tb" style="position: absolute; border-spacing: 0px; left: 144px; top: 456px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o96w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Система Гвс
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o97w_tb" style="position: absolute; border-spacing: 0px; left: 1192px; top: 40px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o97w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 24px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Клапан отопления
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o98w_" class="s-unsel" tabindex="0" data-vl="Авто" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1312px; top: 48px; border-radius: 2px; width: 64px; height: 32px; line-height: 32px; text-align: center; color: rgba(0, 0, 0, 0.7); background-color: rgb(0, 255, 128); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#00FF80FF">
                    Авто
                </div>
                <table id="p0s0o99w_tb" style="position: absolute; border-spacing: 0px; left: 1202px; top: 1001px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o99w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Клапан ГВС
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o100w_" class="s-unsel" tabindex="0" data-vl="Авто" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1328px; top: 1008px; border-radius: 2px; width: 64px; height: 32px; line-height: 32px; text-align: center; color: rgba(0, 0, 0, 0.7); background-color: rgb(0, 255, 128); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#00FF80FF">
                    Авто
                </div>
                <table id="p0s0o101w_tb" style="position: absolute; border-spacing: 0px; left: 1456px; top: 328px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o101w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 24px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Открыт/закрыт
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o102w_tb" style="position: absolute; border-spacing: 0px; left: 1800px; top: 168px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o102w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o103w_tb" style="position: absolute; border-spacing: 0px; left: 1800px; top: 336px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o103w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o104w_tb" style="position: absolute; border-spacing: 0px; left: 1807px; top: 1160px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o104w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o105w_tb" style="position: absolute; border-spacing: 0px; left: 1008px; top: 296px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o105w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o106w_tb" style="position: absolute; border-spacing: 0px; left: 1874px; top: 1017px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o106w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o107w_tb" style="position: absolute; border-spacing: 0px; left: 1808px; top: 48px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o107w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o108w_tb" style="position: absolute; border-spacing: 0px; left: 1328px; top: 200px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o108w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o109w_tb" style="position: absolute; border-spacing: 0px; left: 144px; top: 744px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o109w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button id="p0s0o110w_" class="s-unsel k-button" data-role="button" role="button" aria-disabled="false" tabindex="0" data-cl="#6D6D6D" style="position: absolute; left: 376px; top: 424px; width: 112px; height: 40px; padding: 0px; background: rgb(109, 109, 109); color: rgba(255, 255, 255, 0.7); border-width: 2px; border-color: rgb(109, 109, 109); overflow-x: hidden; white-space: nowrap; font-size: 15px; font-family: RobotoRegular; letter-spacing: 0.21px;">
                    Включить
                </button>
                <table id="p0s0o111w_tb" style="position: absolute; border-spacing: 0px; left: 360px; top: 368px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o111w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 24px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Сброс аварии
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o112w_" data-cl="#0080FF" style="position: absolute; left: 1571px; top: 705px; width: 379px; height: 208px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 331px; top: 48px; width: 32px; height: 112px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 331px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl4.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 331px; top: 160px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p3.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p3.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 48px; top: 160px; width: 283px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 160px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                </div>
                <div id="p0s0o113w_" data-cl="#0080FF" style="position: absolute; left: 1815px; top: 689px; width: 233px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 105px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 169px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o114w_rot" style="position: absolute; left: 1565px; top: 833px; width: 65px; height: 65px; opacity: 1; display: block;">
                    <div id="p0s0o114w_" data-w="65" data-h="65" data-clw="65" data-clh="65" data-i="-1" data-cl="#fff" style="left: 1565px; top: 833px; width: 65px; height: 65px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-WpFMACPxAqullyVc.png&quot;) 0px 0px / 65px 65px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-WpFMACPxAqullyVc.png&quot;); -webkit-mask-size: 65px 65px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o115w_rot" style="position: absolute; left: 1685px; top: 721px; width: 127px; height: 84px; opacity: 1; display: block;">
                    <div id="p0s0o115w_" data-w="127" data-h="84" data-clw="127" data-clh="84" data-i="-1" data-cl="#fff" style="left: 1685px; top: 721px; width: 127px; height: 84px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;) 0px 0px / 127px 84px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;); -webkit-mask-size: 127px 84px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o116w_rot" style="position: absolute; left: 1464px; top: 571px; width: 107px; height: 193px; opacity: 1; display: block;">
                    <div id="p0s0o116w_" data-w="107" data-h="193" data-clw="107" data-clh="193" data-i="-1" data-cl="#fff" style="left: 1464px; top: 571px; width: 107px; height: 193px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pto4-new.png&quot;) 0px 0px / 107px 193px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pto4-new.png&quot;); -webkit-mask-size: 107px 193px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o117w_rot" style="position: absolute; left: 1289px; top: 661px; width: 62px; height: 64px; opacity: 1; display: block;">
                    <div id="p0s0o117w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1289px; top: 661px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o118w_" data-cl="#0080FF" style="position: absolute; left: 1754px; top: 648px; width: 136px; height: 168px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 8px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 72px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p14.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p14.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 72px; top: 64px; width: 48px; height: 40px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 72px; top: 104px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p3.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p3.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 64px; top: 104px; width: 8px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 104px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                </div>
                <table id="p0s0o119w_tb" style="position: absolute; border-spacing: 0px; left: 2081px; top: 712px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o119w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                T21
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o120w_tb" style="position: absolute; border-spacing: 0px; left: 1770px; top: 623px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o120w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                М3
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o121w_tb" style="position: absolute; border-spacing: 0px; left: 1771px; top: 792px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o121w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                М4
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o122w_rot" style="position: absolute; left: 1333px; top: 545px; width: 62px; height: 64px; opacity: 1; display: block;">
                    <div id="p0s0o122w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1333px; top: 545px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-IeIc3zzwPc9AUMYx.png&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-IeIc3zzwPc9AUMYx.png&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o123w_rot" style="position: absolute; left: 1231px; top: 554px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o123w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 1231px; top: 554px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o124w_" data-cl="#FF0000" style="position: absolute; left: 1252px; top: 553px; width: 132px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 4px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 68px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o125w_rot" style="position: absolute; left: 1768px; top: 528px; width: 62px; height: 64px; opacity: 1; display: block;">
                    <div id="p0s0o125w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1768px; top: 528px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o126w_" data-cl="#FF0000" style="position: absolute; left: 1349px; top: 553px; width: 188px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 60px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 124px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e14.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e14.png&quot;);"></div>
                </div>
                <div id="p0s0o127w_" data-cl="#FF0000" style="position: absolute; left: 1499px; top: 553px; width: 547px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 419px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e12.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e12.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 483px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p19.png&quot;);"></div>
                </div>
                <div id="p0s0o128w_rot" style="position: absolute; left: 2000px; top: 553px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o128w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 2000px; top: 553px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o129w_rot" style="position: absolute; left: 2002px; top: 689px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o129w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 2002px; top: 689px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o130w_" data-cl="#0080FF" style="position: absolute; left: 1500px; top: 687px; width: 176px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e12.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e12.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 112px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o131w_" data-cl="#0080FF" style="position: absolute; left: 1600px; top: 648px; width: 136px; height: 168px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 8px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p7.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p7.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 72px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 64px; width: 48px; height: 40px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 104px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 64px; top: 104px; width: 8px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 72px; top: 104px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <table id="p0s0o132w_tb" style="position: absolute; border-spacing: 0px; left: 2080px; top: 568px; width: 56px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o132w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                T11
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o133w_rot" style="position: absolute; left: 1685px; top: 633px; width: 127px; height: 84px; opacity: 1; display: block;">
                    <div id="p0s0o133w_" data-w="127" data-h="84" data-clw="127" data-clh="84" data-i="-1" data-cl="#fff" style="left: 1685px; top: 633px; width: 127px; height: 84px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;) 0px 0px / 127px 84px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;); -webkit-mask-size: 127px 84px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o134w_rot" style="position: absolute; left: 1721px; top: 752px; width: 48px; height: 48px; opacity: 1; display: block;">
                    <div id="p0s0o134w_" data-w="144" data-h="96" data-clw="48" data-clh="48" data-i="-1" data-cl="#FF0000" style="left: 1721px; top: 752px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;) -48px -48px / 144px 96px rgb(255, 0, 0); background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;); -webkit-mask-size: 144px 96px; -webkit-mask-position: -48px -48px;"></div>
                </div>
                <div id="p0s0o135w_rot" style="position: absolute; left: 1721px; top: 665px; width: 48px; height: 48px; opacity: 1; display: block;">
                    <div id="p0s0o135w_" data-w="144" data-h="96" data-clw="48" data-clh="48" data-i="-1" data-cl="#FF0000" style="left: 1721px; top: 665px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;) -48px -48px / 144px 96px rgb(255, 0, 0); background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;); -webkit-mask-size: 144px 96px; -webkit-mask-position: -48px -48px;"></div>
                </div>
                <table id="p0s0o136w_cpttb" style="position: absolute; border-spacing: 0px; left: 1320px; top: 632px; width: 40px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o136w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0.196); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                °С
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o136w_" class="s-unsel" tabindex="0" data-vl="60" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1280px; top: 632px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    60
                </div>
                <table id="p0s0o137w_cpttb" style="position: absolute; border-spacing: 0px; left: 1800px; top: 488px; width: 40px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o137w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                °С
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o137w_" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1760px; top: 488px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    0
                </div>
                <div id="p0s0o138w_rot" style="position: absolute; left: 992px; top: 808px; width: 49px; height: 21px; transform: rotate(270deg); opacity: 1; display: block;">
                    <div id="p0s0o138w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 992px; top: 808px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o139w_rot" style="position: absolute; left: 1448px; top: 716px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o139w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1448px; top: 716px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o140w_" data-cl="#0080FF" style="position: absolute; left: 1256px; top: 687px; width: 280px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 152px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 216px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e14.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/e14.png&quot;);"></div>
                </div>
                <div id="p0s0o141w_rot" style="position: absolute; left: 1416px; top: 715px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o141w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1416px; top: 715px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o142w_rot" style="position: absolute; left: 768px; top: 718px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o142w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 768px; top: 718px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o143w_rot" style="position: absolute; left: 856px; top: 717px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o143w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 856px; top: 717px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px -21px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px -21px;"></div>
                </div>
                <div id="p0s0o144w_rot" style="position: absolute; left: 840px; top: 1085px; width: 57px; height: 21px; opacity: 1; display: block;">
                    <div id="p0s0o144w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 840px; top: 1085px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o145w_rot" style="position: absolute; left: 1424px; top: 582px; width: 57px; height: 21px; opacity: 1; display: block;">
                    <div id="p0s0o145w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1424px; top: 582px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o146w_rot" style="position: absolute; left: 1744px; top: 582px; width: 57px; height: 21px; opacity: 1; display: block;">
                    <div id="p0s0o146w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1744px; top: 582px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o147w_rot" style="position: absolute; left: 1576px; top: 716px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o147w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1576px; top: 716px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o148w_rot" style="position: absolute; left: 1912px; top: 718px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
                    <div id="p0s0o148w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1912px; top: 718px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o149w_rot" style="position: absolute; left: 1075px; top: 712px; width: 57px; height: 21px; transform: rotate(90deg); opacity: 1; display: block;">
                    <div id="p0s0o149w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1075px; top: 712px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o150w_rot" style="position: absolute; left: 1235px; top: 688px; width: 66px; height: 68px; opacity: 1; display: block;">
                    <div id="p0s0o150w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 1235px; top: 688px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o151w_" data-cl="#0080FF" style="position: absolute; left: 1051px; top: 392px; width: 557px; height: 64px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 48px; top: 16px; width: 461px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 509px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <table id="p0s0o152w_cpttb" style="position: absolute; border-spacing: 0px; left: 1408px; top: 632px; width: 40px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o152w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0.196); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                °С
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o152w_" class="s-unsel" tabindex="0" data-vl="60" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1368px; top: 632px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    60
                </div>
                <table id="p0s0o153w_cpttb" style="position: absolute; border-spacing: 0px; left: 1880px; top: 488px; width: 40px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o153w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0.196); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                °С
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o153w_" class="s-unsel" tabindex="0" data-vl="60" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1840px; top: 488px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    60
                </div>
                <table id="p0s0o154w_tb" style="position: absolute; border-spacing: 0px; left: 1192px; top: 496px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o154w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 24px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Клапан вентиляции
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o155w_" class="s-unsel" tabindex="0" data-vl="Авто" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1312px; top: 504px; border-radius: 2px; width: 64px; height: 32px; line-height: 32px; text-align: center; color: rgba(0, 0, 0, 0.7); background-color: rgb(0, 255, 128); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#00FF80FF">
                    Авто
                </div>
                <table id="p0s0o156w_tb" style="position: absolute; border-spacing: 0px; left: 1456px; top: 784px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o156w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 24px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Открыт/закрыт
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o157w_tb" style="position: absolute; border-spacing: 0px; left: 1800px; top: 624px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o157w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o158w_tb" style="position: absolute; border-spacing: 0px; left: 1800px; top: 792px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o158w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o159w_tb" style="position: absolute; border-spacing: 0px; left: 1808px; top: 504px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o159w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o160w_tb" style="position: absolute; border-spacing: 0px; left: 1328px; top: 656px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o160w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o161w_" data-cl="#FF0000" style="position: absolute; left: 288px; top: 96px; width: 992px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 864px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 928px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o162w_" data-cl="#0080FF" style="position: absolute; left: 288px; top: 230px; width: 992px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 864px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 928px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <table id="p0s0o163w_tb" style="position: absolute; border-spacing: 0px; left: 136px; top: 552px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o163w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 24px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Система вентиляции
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o164w_" data-cl="#0080FF" style="position: absolute; left: 902px; top: 346px; width: 104px; height: 152px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 48px; top: 16px; width: 8px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p7.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p7.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 56px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 48px; width: 32px; height: 56px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 104px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p13.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 48px; top: 104px; width: 8px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 56px; top: 104px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl19.png&quot;);"></div>
                </div>
                <div id="p0s0o165w_rot" style="position: absolute; left: 967px; top: 341px; width: 85px; height: 56px; opacity: 1; display: block;">
                    <div id="p0s0o165w_" data-w="85" data-h="56" data-clw="85" data-clh="56" data-i="-1" data-cl="#fff" style="left: 967px; top: 341px; width: 85px; height: 56px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;) 0px 0px / 85px 56px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;); -webkit-mask-size: 85px 56px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o166w_rot" style="position: absolute; left: 991px; top: 363px; width: 32px; height: 32px; opacity: 1; display: block;">
                    <div id="p0s0o166w_" data-w="96" data-h="64" data-clw="32" data-clh="32" data-i="-1" data-cl="#FF0000" style="left: 991px; top: 363px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;) -32px -32px / 96px 64px rgb(255, 0, 0); background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;); -webkit-mask-size: 96px 64px; -webkit-mask-position: -32px -32px;"></div>
                </div>
                <div id="p0s0o167w_rot" style="position: absolute; left: 967px; top: 429px; width: 85px; height: 56px; opacity: 1; display: block;">
                    <div id="p0s0o167w_" data-w="85" data-h="56" data-clw="85" data-clh="56" data-i="-1" data-cl="#fff" style="left: 967px; top: 429px; width: 85px; height: 56px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;) 0px 0px / 85px 56px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;); -webkit-mask-size: 85px 56px; -webkit-mask-position: 0px 0px;"></div>
                </div>
                <div id="p0s0o168w_rot" style="position: absolute; left: 992px; top: 450px; width: 32px; height: 32px; opacity: 1; display: block;">
                    <div id="p0s0o168w_" data-w="96" data-h="64" data-clw="32" data-clh="32" data-i="-1" data-cl="#FF0000" style="left: 992px; top: 450px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;) -32px -32px / 96px 64px rgb(255, 0, 0); background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/Animated_5.sbm&quot;); -webkit-mask-size: 96px 64px; -webkit-mask-position: -32px -32px;"></div>
                </div>
                <div id="p0s0o169w_" data-cl="#0080FF" style="position: absolute; left: 1008px; top: 346px; width: 104px; height: 152px; color: rgb(0, 128, 255); opacity: 1; display: block;">
                    <div data-pch="1" style="position: absolute; left: 48px; top: 16px; width: 8px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 56px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p14.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p14.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 56px; top: 48px; width: 32px; height: 56px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p20.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 56px; top: 104px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p3.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p3.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 48px; top: 104px; width: 8px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/p2.png&quot;);"></div>
                    <div data-pch="1" style="position: absolute; left: 16px; top: 104px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/resources/views/public/project/holm/assets/images/pipes/pfl1.png&quot;);"></div>
                </div>
                <table id="p0s0o170w_tb" style="position: absolute; border-spacing: 0px; left: 1040px; top: 480px; width: 56px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o170w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="5.95" style="font-size: 24px; font-family: RobotoRegular; letter-spacing: -0.7px; line-height: 30px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                М7
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o171w_tb" style="position: absolute; border-spacing: 0px; left: 1000px; top: 504px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o171w_" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Авария
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="p0s0o172w_cpttb" style="position: absolute; border-spacing: 0px; left: 1432px; top: 80px; width: 34px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o172w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 34px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                %
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o172w_" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1392px; top: 80px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    0
                </div>
                <table id="p0s0o173w_cpttb" style="position: absolute; border-spacing: 0px; left: 1424px; top: 544px; width: 34px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o173w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 34px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                %
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o173w_" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1384px; top: 544px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    0
                </div>
                <table id="p0s0o174w_cpttb" style="position: absolute; border-spacing: 0px; left: 1440px; top: 1040px; width: 34px; height: 32px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o174w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 34px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                %
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o174w_" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1400px; top: 1040px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: rgb(0, 255, 64); background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
                    0
                </div>
            </div>
        </div>
        <div id="ssrepdiv" style="position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; background: rgb(50, 54, 57); display: none;">
            <div style="position: relative; width: 100%; height: 32px;">
                <button id="btnRepViewClose" class="k-button k-primary" style="position: static; width: 100%; height: 32px; background-color: rgb(0, 151, 167); border-color: rgb(0, 151, 167);">
                    Нажмите здесь, чтобы закрыть просмотр отчета.
                </button>
            </div>
            <div style="position: absolute; top: 32px; width: 100%; bottom: 0px;">
                <iframe id="ssrepframe" data-viewer="0" src="about:blank" style="width: 100%; height: 100%; border-width: 0px;"></iframe>
            </div>
        </div>
        <div id="sstrendsdiv" style="display: none;"></div>
        <div id="ssmessagesdiv" style="display: none;"></div>
    </body>
</html>
