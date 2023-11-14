<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Тренды</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script defer="" src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/2.3.0/alpine.js"></script>
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/vendors.min.css')) }}">
      <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    @livewireStyles
</head>

<body>
    <livewire:scada::chart :scada_id="$id" />


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

    <script src="{{ asset(mix('vendors/js/vendors.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>





    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>






    @livewireScripts
    @stack('scripts')
    <script>
        window.livewire.on('chartUpdate', (chartId, labels, datasets) => {
            let chart = window[chartId];

            console.log(chart);
            console.log(chartId);
            chart.updateSeries(datasets);
        });
    </script>
</body>

</html>
