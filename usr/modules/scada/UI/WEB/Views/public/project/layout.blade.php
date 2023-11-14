<!doctype html>
<html lang="ru_RU">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Диспетчеризация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <meta name="theme-color" content="#7952b3">
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
    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/5.0/examples/dashboard/dashboard.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <script>
        function getScadaAll() {
            $.get("{{ route('scada.ajax', ['id' => $id]) }}", function(data) {
                try {
                    let json = JSON.parse(data);
                    json.forEach(function(i) {
                        let value = i.last_value;
                        if (i.field_id !== null) {
                            if (i.last_value === '0') {
                                value = 'false';
                            }
                            switch (i.field_type) {
                                case "crash":
                                    if (i.last_value == '1') {
                                        $("#" + i.field_id).show();
                                        $("#" + i.field_id).addClass('puls');
                                    } else {
                                        $("#" + i.field_id).hide();
                                        $("#" + i.field_id).removeClass('puls');
                                    }
                                    break;
                                case "crash_rvd":
                                    console.log("#" + i.field_id + '_30');
                                    if (i.last_value == '0.0') {
                                        $("#" + i.field_id + '_10').hide();
                                        $("#" + i.field_id + '_20').hide();
                                        $("#" + i.field_id + '_30').hide();
                                        $("#" + i.field_id + '_40').hide();
                                        $("#" + i.field_id + '_42').hide();
                                        $("#" + i.field_id + '_50').hide();
                                        $("#" + i.field_id + '_52').hide();
                                        $("#" + i.field_id + '_61').hide();
                                        $("#" + i.field_id + '_62').hide();
                                        $("#" + i.field_id + '_73').hide();
                                        $("#" + i.field_id + '_78').hide();
                                        $("#" + i.field_id + '_86').hide();
                                        $("#" + i.field_id + '_170').hide();
                                        $("#" + i.field_id + '_195').hide();
                                        $("#" + i.field_id + '_196').hide();
                                        $("#" + i.field_id + '_10').removeClass();
                                        $("#" + i.field_id + '_20').removeClass();
                                        $("#" + i.field_id + '_30').removeClass();
                                        $("#" + i.field_id + '_40').removeClass();
                                        $("#" + i.field_id + '_42').removeClass();
                                        $("#" + i.field_id + '_50').removeClass();
                                        $("#" + i.field_id + '_52').removeClass();
                                        $("#" + i.field_id + '_61').removeClass();
                                        $("#" + i.field_id + '_62').removeClass();
                                        $("#" + i.field_id + '_73').removeClass();
                                        $("#" + i.field_id + '_78').removeClass();
                                        $("#" + i.field_id + '_86').removeClass();
                                        $("#" + i.field_id + '_170').removeClass();
                                        $("#" + i.field_id + '_195').removeClass();
                                        $("#" + i.field_id + '_196').removeClass();

                                    } else {
                                        $("#" + i.field_id + i.last_value).show();
                                        $("#" + i.field_id + i.last_value).addClass('puls');
                                    }
                                    break;
                                case "thermometer":
                                    setTemperature(i.last_value);
                                    break;
                                case "auto":
                                    if (i.last_value == '1') {
                                        $("#" + i.field_id).html('Авто');
                                        $("#" + i.field_id).css("background-color", "rgb(0, 255, 128)");
                                    } else {
                                        $("#" + i.field_id).html('Ручн.');
                                        $("#" + i.field_id).css("background-color", "#ccc");
                                    }
                                    break;
                                case "auto_invert":
                                    if (i.last_value == '0') {
                                        $("#" + i.field_id).html('Авто');
                                        $("#" + i.field_id).css("background-color", "rgb(0, 255, 128)");
                                    } else {
                                        $("#" + i.field_id).html('Ручн.');
                                        $("#" + i.field_id).css("background-color", "#ccc");
                                    }
                                    break;
                                case "open":
                                    if (i.last_value == '1') {
                                        $("#" + i.field_id).html('Открыт');
                                    } else {
                                        $("#" + i.field_id).html('Закрыт');
                                    }
                                    break;
                                case "pump":
                                    if (i.last_value == '1') {
                                        $("#" + i.field_id).addClass('pumpWork');
                                    } else {
                                        $("#" + i.field_id).removeClass('pumpWork');
                                    }
                                    break;
                                case "water_level":
                                    $("#" + i.field_id).css("height", (value / 150) * 100 + "%");
                                    $("#" + i.field_id + '_txt').html(value);
                                    break;
                                case "valve_rvd":
                                    if (i.last_value > 0) {
                                        $("#" + i.field_id).show();
                                    } else {
                                        $("#" + i.field_id).hide();
                                    }
                                    break;
                                case "puls":
                                    if (i.last_value == '1') {
                                        $("#" + i.field_id).addClass('puls_adv');
                                    } else {
                                        $("#" + i.field_id).removeClass('puls_adv');
                                    }
                                    break;
                                default:
                                    $("#" + i.field_id).html(value);
                            }
                        }

                    });
                } catch (e) {
                    console.log(e);
                    return true;
                }
            });
        }
        getScadaAll();
        let intervalScada = setInterval(() => {
            getScadaAll();
        }, 5000);
        // JavaScript Document
        function thermometer(goalAmount, progressAmount, animate) {
            "use strict";
            var $thermo = $("#thermometer"),
                $progress = $(".progress", $thermo),
                $goal = $(".goal", $thermo),
                $innerBulb = $(".inner-bulb", $thermo),
                percentageAmount;

            goalAmount = goalAmount || parseFloat($goal.text()),
                progressAmount = progressAmount || parseFloat($progress.text()),
                percentageAmount = Math.min(Math.round((progressAmount / goalAmount * 1000) / 10) + 35, 100); //make sure we have 1 decimal point

            $goal.find(".amount").text();
            $progress.find(".amount").text();

            //$progress.find(".amount").hide();
            if (animate !== false) {
                $progress.animate({
                    "height": percentageAmount + "%"
                }, 1200, function() {
                    //$(this).find(".amount").fadeIn(200);
                });
                if (goalAmount > 0) {
                    $progress.css({

                        "backgroundColor": "#8bc34a"
                    });
                    $innerBulb.css({
                        "backgroundColor": "#8bc34a"
                    });
                } else {
                    $progress.css({

                        "backgroundColor": "blue"
                    });

                    $innerBulb.css({
                        "backgroundColor": "blue"
                    });
                }

            } else {
                $progress.css({
                    "height": percentageAmount + "%",
                    "backgroundColor": "#000"
                });

                // $progress.find(".amount").fadeIn(200);
            }
        }






        function setTemperature(value) {
            const temperature = document.getElementById("temperature");
            const units = {
                Celcius: "°C",
                Fahrenheit: "°F"
            };


            const config = {
                minTemp: -20,
                maxTemp: 50,
                unit: "Celcius"
            };
            temperature.style.height = (value - config.minTemp) / (config.maxTemp - config.minTemp) * 100 + "%";
            temperature.dataset.value = value + units[config.unit];
        }
    </script>
    <style>
        #wrapper {
            margin: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #termometer {
            width: 35px;
            background: #38383f;
            height: 300px;
            position: relative;
            border: 9px solid #2a2a2e;
            border-radius: 20px;
            z-index: 1;
            margin-bottom: 100px;
        }

        #termometer:before,
        #termometer:after {
            position: absolute;
            content: "";
            border-radius: 50%;
        }

        #termometer:before {
            width: 100%;
            height: 59px;
            bottom: 9px;
            background: #38383f;
            z-index: -1;
        }

        #termometer:after {
            transform: translateX(-50%);
            width: 70px;
            height: 70px;
            background-color: #3dcadf;
            bottom: -55px;
            border: 9px solid #2a2a2e;
            z-index: -3;
            left: 50%;
        }

        #termometer #graduations {
            height: 59%;
            top: 20%;
            width: 50%;
        }

        #termometer #graduations,
        #termometer #graduations:before {
            position: absolute;
            border-top: 2px solid rgba(0, 0, 0, 0.5);
            border-bottom: 2px solid rgba(0, 0, 0, 0.5);
        }

        #termometer #graduations:before {
            content: "";
            height: 34%;
            width: 100%;
            top: 32%;
        }

        #termometer #temperature {
            bottom: 0;
            background: linear-gradient(#f17a65, #3dcadf) no-repeat bottom;
            width: 100%;
            border-radius: 20px;
            background-size: 100% 300px;
            transition: all 0.2s ease-in-out;
        }

        #termometer #temperature,
        #termometer #temperature:before,
        #termometer #temperature:after {
            position: absolute;
        }

        #termometer #temperature:before {
            content: attr(data-value);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            z-index: 2;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 1.7em;
            line-height: 1;
            transform: translateY(50%);
            left: calc(100% + 1em / 1.5);
            top: calc(-1em + 5px - 5px * 2);
        }

        #termometer #temperature:after {
            content: "";
            border-top: 0.4545454545em solid transparent;
            border-bottom: 0.4545454545em solid transparent;
            border-right: 0.6666666667em solid rgba(0, 0, 0, 0.7);
            left: 100%;
            top: calc(-1em / 2.2 + 5px);
        }

        #playground {
            font-size: 1.1em;
        }

        #playground #range {
            display: flex;
        }

        #playground #range input[type=text] {
            width: 2em;
            background: transparent;
            border: none;
            color: inherit;
            font: inherit;
            margin: 0 5px;
            padding: 0px 5px;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease-in-out;
        }

        #playground #range input[type=text]:focus {
            border-color: #3dcadf;
            outline: none;
        }

        #playground #range input[type=text]:first-child {
            text-align: right;
        }

        #playground #unit {
            width: 100%;
            margin: 0;
            text-align: center;
        }


        .pumpWork {
            animation: move 2s linear infinite;
            background: url(/usr/modules/scada/resources/views/public/project/assets/images/Animated_5.sbm) -48px -48px / 144px 96px rgb(142 255 146) !important;
        }

        @keyframes move {
            100% {
                transform: rotate(360deg);
            }
        }

        .puls {

            animation: pulsate 0.5s infinite;
            color: #f44336 !important;
        }

        .puls_adv {

            animation: pulsate_adv 1s infinite;
            color: #f44336 !important;
        }

        @keyframes pulsate {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.5);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes pulsate_adv {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body style="overflow-y: hidden;">
    @yield('main')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script>
</body>

</html>
