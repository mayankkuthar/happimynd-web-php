<?php $curentPageUrl = $_SERVER['REQUEST_URI']; $reportLogoV = @filemtime(public_path('assets/Frontend/images/report_logo_img.png')); ?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Google Tag Manager -->
  <script>
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-P3VGFBK');
  </script>
  <!-- End Google Tag Manager -->
  
  <meta name="description" content="HappiMynd offers unique and digitally empowered tools to support your emotional wellbeing. Our employee wellness programme is uniquely designed to ensure complete work-life balance." />
  <link rel="canonical" href="{{ url('/') }}{{ $curentPageUrl }}" />
  <base href="{{ url('/') }}" />

  <title>Happimynd | Report</title>

  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="{{ asset('assets/Backend/css/plugins/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/Frontend/css/report.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/Frontend/css/main.css') }}" rel="stylesheet">
</head>
<body class="font-sans antialiased">
  <div id="container1">
    <div class="report">
      <div class="report__firstpage">
        <div class="report__printbtn">
          <button onclick="window.print()">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M5 7.50033V1.66699H15V7.50033" stroke="#3C92C6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M5.00002 15H3.33335C2.89133 15 2.4674 14.8244 2.15484 14.5118C1.84228 14.1993 1.66669 13.7754 1.66669 13.3333V9.16667C1.66669 8.72464 1.84228 8.30072 2.15484 7.98816C2.4674 7.6756 2.89133 7.5 3.33335 7.5H16.6667C17.1087 7.5 17.5326 7.6756 17.8452 7.98816C18.1578 8.30072 18.3334 8.72464 18.3334 9.16667V13.3333C18.3334 13.7754 18.1578 14.1993 17.8452 14.5118C17.5326 14.8244 17.1087 15 16.6667 15H15" stroke="#3C92C6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M15 11.667H5V18.3337H15V11.667Z" stroke="#3C92C6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Print
          </button>
        </div>
        <h1>{{$data[4]->title}} Profile Summary</h1><div class="">
          <div class="report__content report__content__first">
            <div class="report__content__userinfo">
              <div class="row no-gutters align-items-end">
                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                  <h2>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="#3C92C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="#3C92C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ $report['nickname'] }}
                  </h2>
                  <h2>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" stroke="#3C92C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M16 2V6" stroke="#3C92C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M8 2V6" stroke="#3C92C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M3 10H21" stroke="#3C92C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ $report['assessment_date'] }}
                  </h2>
                  <h2>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#3C92C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M12 6V12L16 14" stroke="#3C92C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ $report['assessment_time'] }} | IST
                  </h2>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                  <div class="text-right">
                    <img src="{{ asset('assets/Frontend/images/report_logo_img.png') }}?v={{ $reportLogoV }}" />
                  </div>
                </div>
              </div>
            </div>
            <hr>
            <div class="report__content__summary">
              <div>
                <h2>About this Summary</h2>
                <p>
                  Holistic wellness is an amalgamation of physical and mental wellness. You need to take care of both for maintaining the equilibrium of your body.
                  <br><br>Mental Wellness constitutes of:
                  <br><br>Behavioural Health: How we behave
                  <br>Cognitive Health: How we think
                  <br>Emotional Health: How we feel
                  <br>Quality of Life: Facets of physical health, social relations, spirituality, etc.
                  <br><br>The summary will give you a snapshot of your mental well wellbeing, virtues and vices.
                  <br>The first step towards emotional wellness is to be aware of your capacities and is the beginning point from where you start your journey. This summary compiles your strengths, opportunities  and various facets of your personality. There are {{ count($score) }} parameters on which your emotional wellness is scaled. The information is based on your responses in the screening. The synopsis is like a mirror that describes and helps you to start working towards holistic wellness. You can also opt for assisted summary reading with a qualified mental wellness expert.<br>
                  Note that the summary is indicative and is not equivalent to medical advice.
                </p>
              </div>
              <div class="report__content__summary__wheel">
                <div class="row no-gutters align-items-center report__content__summary__wheel__margin-left">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="">
                      <div id="wheeloflife">
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6" style="padding-left: 70px;">
                    <div class="report__content__summary__wheel__desc">
                      <h2>HappiMynd Wheel</h2>
                      <p>
                        Here is  your HappiMynd wheel! This wheel, is a visual tool used to assess and understand how balanced your emotional & behavioral life currently is. Using this tool, you map out the areas of your emotional wellbeing on a circle that resembles the spoke of a wheel, which is the reasoning for its name. It will help you in understanding the areas where you are thriving and opportunities to improve. The coloured area indicates your current level of emotional wellbeing. The white space indicates the area of opportunity in each of the aspect of emotional wellbeing.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="report__content__tabs">
              <div class="">
                @php $loopVariable = 0; $tempScore = $score @endphp
                @foreach($tempScore as $name => $s)
                @if($loopVariable == 1 ) @php $loopVariable=0 @endphp  @break @endif
                @php $loopVariable++ @endphp
                <div class="report__content__tabs__text">
                  <div class="report__content__tabs__text__img">
                    <h2>{{ $s['category_in_report'] }}</h2>
                    @isset($s['picture'])<img hight="65px" width="65px" src="{{ $s['picture'] }}" />@endif
                  </div>
                  <span>{!! $s['summary'] ?? ''!!} @if(env('ASSESSMENT_DEBUG')) <b>Score: {{ json_encode($s['score']) }} Scale: {{ $s['meterScaleLevelName'] }} </b>@endif</span>
                </div>
                @php array_shift($tempScore) @endphp
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
      @while(count($tempScore) > 2 && array_keys($tempScore)[0] != "Anxiety" && array_keys($tempScore)[0] != "Personality")
      <div class="report__content">
        <div class="report__content__tabs report__content__tabs--secondpage">
          <div class="">
            <div class="report__content__tabs__text__withlogo">
              <div class="text-right">
                <img src="{{ asset('assets/Frontend/images/report_logo_img.png') }}?v={{ $reportLogoV }}" />
              </div>
              @foreach($tempScore as $name => $s)

            


             

                @if($loopVariable == 4 || $name == "Anxiety")@php $loopVariable=0 @endphp  @break @endif
                @php $loopVariable++ @endphp
                @if(array_key_exists('category_in_report',$s))

                  <div class="report__content__tabs__text">
                    <div class="report__content__tabs__text__img">
                      <h2>{{ $s['category_in_report'] ?? ''}}</h2>
                      @isset($s['picture'])<img hight="65px" width="65px" src="{{ $s['picture'] }}" />@endif
                    </div>
                    <span>{!! $s['summary'] ?? '' !!}@if(env('ASSESSMENT_DEBUG')) <b>Score: {{ json_encode($s['score']) }} Scale: {{ $s['meterScaleLevelName'] ?? ''}} </b>@endif</span>
                  </div>
                @endif  

                @php array_shift($tempScore) @endphp

              @endforeach
            </div>
          </div>
        </div>
      </div>
      @endwhile
      <div class="report__content">
        <div class="report__content__tabs__last">
          <div class="report__content__tabs">
            <div class="">
              <div class="report__content__tabs__text__withlogo report__content__tabs__text__withlogo--last">
                <div class="text-right">
                  <img src="{{ asset('assets/Frontend/images/report_logo_img.png') }}?v={{ $reportLogoV }}" />
                </div>
                @foreach($tempScore as $name => $s)
                  @if(array_key_exists('category_in_report',$s))
                    <div class="report__content__tabs__text">
                      <div class="report__content__tabs__text__img">
                        <h2>{{ $s['category_in_report'] }}</h2>
                        @isset($s['picture'])<img hight="65px" width="65px" src="{{ $s['picture'] }}" />@endif
                      </div>
                      <span>{!! $s['summary'] ?? ''!!}@if(env('ASSESSMENT_DEBUG')) <b> @isset($s['score']) Score: {{ json_encode($s['score']) }} @endisset @isset($s['meterScaleLevelName'])Scale: {{ $s['meterScaleLevelName'] }} @endisset </b>@endif</span>
                    </div>
                  @endif
                @php array_shift($tempScore) @endphp
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="report__content">
        <div class="report__content__tabs__last report__content__tabs__text__withlogo">
          <div class="text-right">
            <img src="{{ asset('assets/Frontend/images/report_logo_img.png') }}?v={{ $reportLogoV }}" />
          </div>
          <div class="report__content__donext-parent">
            <div class="report__content__donext">
              <h2>Next Step</h2>
              <p>
                Congratulations on completing your HappiLIFE Awareness tool and going through the entire summary. Now that you know how the HappiMynd wheel works, we are sure you have gained a comprehensive understanding of your emotional, behavioral and cognitive wellbeing. You are well equipped to identify the areas of life that you are thriving in and ones that need to be worked upon.
                <br>If you are keen on making the most out of your summary, an assisted Summary Reading session by our emotional wellbeing expert will guide you in minutely scrutinizing and interpreting your performance under each parameter, what implications the scores carry, and guiding you on the necessary next steps that can set you sailing on a holistic wellness journey.
                <br>
                  Once aware of your needs, you can choose from our unique range of  accessible, affordable & reliable services available over a fully digital human assisted platform while ensuring utmost confidentiality.
                <br>
                <br>

                  <b>Recommendations-</b>
                  <br>
                  <br>
                  <table class="table table-bordered" style="    width: 550px;">
                    <tr>
                      <th style="text-align: center;">Score(in any parameter)</th>
                      <th style="text-align: center;">Recommended Tool</th>
                    </tr>
                    <tr>
                      <td style="text-align: center;">>5</td>
                      <td style="text-align: center;">HappiBUDDY, HappiLEARN</td>
                    </tr>
                    <tr>
                      <td style="text-align: center;">3-5</td>
                      <td style="text-align: center;">HappiGUIDE, HappiSELF</td>
                    </tr>
                    <tr>
                      <td style="text-align: center;"><3</td>
                      <td style="text-align: center;">HappiTALK</td>
                    </tr>
                  </table>

                <br>
                <b>HappiGUIDE</b> ihelps you to make the most out of your HappiLIFE summary with a summary reading session by our emotional wellbeing expert.
                <br>
                <br>
                <b>HappiLEARN</b> is our online self-help library that enriches you with a 24*7 access to 5000+ minutes of curated, well researched content that includes video, audio, blogs and more.
                <br>
                <br>
                <b>HappiBUDDY</b> allows you to connect with a professional expert buddy in a personal emotional log room that is non-judgemental, anonymous, and 100% confidential.
                <br>
                <br>
                 <b>HappiSELF</b> is our mobile Application that enablesSelf-management of emotional wellbeing with a globally validated, interactive program with Cognitive Behavior Therapy at its core.
                <br>
                <br>
                 <b>HappiTALK</b> offers you a safe space to discuss life, aspirations, personal issues, relationships and more with the best of our country’s experts from the comfort of your home.
                <br>
              </p>
            </div>
            <div class="report__content__disclaimer">
              <h2>Contact Details</h2>
              <p>For further details you may contact us at info@happimynd.com or 9110599581 or visit our website at <a href="{{ url('/') }}">www.happimynd.com</a> to explore more.</p>
              <br>
              <h2>Disclaimer</h2>


              <p>
                
                <b>A.</b> If the services are availed by a person who belongs/works with a company/organization which are enrolled with the services for its employees or has a tie up with HappiMynd, the services/tools available  to the users are subject to the following terms:
                <br>
                <br>
                1. The user can avail only those services which the affiliated company has subscribed/purchased for its employees.
                <br>
                2. If the user is willing to avail services which are not covered/subscribed/purchased by the affiliated company, then the user can make an individual/personal purchase of the required services. 
                <br>
                3. The services available and their prices for an individual user can be found on the dashboard of the HappiMynd app or website itself.
                <br>
                <br>
                <b>B.</b> This summary can support you in discovering yourself, knowing the areas of improvement and living a holistic life. However, it is indicative and not a replacement for medical advice. The statements used in HappiLIFE awareness tool are inspired by ICD-10 (WHO) & DSM-5® guidelines.
                <br>
                <br>
                 

                If you are having difficult thoughts or going through rough times, consider calling the below listed helpline numbers,
                <br>
                -National Emergency No. - 112
                <br>
                -Women Helpline - 1091
                <br>
                -Senior Citizen Helpline - 14567
                <br>
                -Suicide Prevention - 9820466726 (AASRA)
                <br>

              </p>


              <p class="footer__copyright__text">Copyright &copy; <script>document.write(new Date().getFullYear())</script> HappiMynd, All rights reserved</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P3VGFBK"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  
  <script src="{{ asset('assets/Backend/js/plugins/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/Frontend/js/report.js') }}"></script>
  <script src="https://www.amcharts.com/lib/4/core.js"></script>
  <script src="https://www.amcharts.com/lib/4/charts.js"></script>
  <script type="text/javascript">
    am4core.ready(function () {

      // the chart
      var chart = am4core.create("wheeloflife", am4charts.RadarChart);
      chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
      chart.innerRadius = am4core.percent(30);
      chart.width = am4core.percent(100);
      chart.height = am4core.percent(100);
      chart.seriesContainer.zIndex = -1; // grid over series
      //chart.seriesContainer.background.fill = '#0f0'
      //chart.seriesContainer.opacity = 0.5
      //chart.padding(20, 20, 20, 20);

      // chart data
      var wheel_data = {!! ($WOL_object) !!};


      // interaction
      var categoryIndex = 0;
      chart.cursor = new am4charts.RadarCursor();
      chart.cursor.innerRadius = am4core.percent(25);
      chart.cursor.behavior = "none"; // disable zoom
      //chart.cursor.lineX.disabled = true;
      //chart.cursor.lineY.fillOpacity = 0.1;
      //chart.cursor.lineY.fill = am4core.color("#000000");
      //chart.cursor.lineY.strokeOpacity = 0;
      //chart.cursor.fullWidthLineY = true;
      chart.cursor.events.on("cursorpositionchanged", function (ev) { // up
        var xAxis = ev.target.chart.xAxes.getIndex(0);
        var yAxis = ev.target.chart.yAxes.getIndex(0);
        categoryIndex = xAxis.positionToIndex(xAxis.toAxisPosition(ev.target.xPosition));
        //console.log(yAxis.toAxisPosition(ev.target.yPosition));
        //console.log("y: ", yAxis.positionToValue(yAxis.toAxisPosition(ev.target.yPosition)));
      });

      // var interaction = am4core.getInteraction();
      // interaction.events.on("up", function (event) {
        //     var point = am4core.utils.documentPointToSprite(event.pointer.point, chart.seriesContainer);
        //     var empty = 4.2;
        //     var x = (valueAxis.max + empty) - valueAxis.xToValue(point.x);
        //     var y = (valueAxis.max + empty) - valueAxis.yToValue(point.y);
        //     var r = Math.sqrt(x * x + y * y) - empty;
        //     //console.log(x,y,r);
        //     if (r > valueAxis.min - 1 && r < valueAxis.max) {
          //         //console.log(r);
          //         setValue(categoryIndex, Math.ceil(r));
          //     }
          // });

          // set value
          function setValue(index, value) {
            chart.data[index].value = value;
            chart.invalidateRawData();
          }

          // categoryAxis
          var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
          // categoryAxis.renderer.lebels.template.background = 'black';
          categoryAxis.dataFields.category = "category";
          categoryAxis.renderer.labels.template.location = 0.5;
          categoryAxis.renderer.tooltipLocation = 0.5;
          categoryAxis.renderer.labels.template.bent = true;
          categoryAxis.renderer.labels.template.padding(0, 0, 0, 0);
          categoryAxis.renderer.labels.template.fill = am4core.color("#414042");
          // categoryAxis.renderer.fill = am4core.color('red')

          categoryAxis.renderer.labels.template.disabled = true; //hide label name

          categoryAxis.renderer.grid.template.strokeDasharray = "1,2"
          categoryAxis.renderer.labels.template.adapter.add("radius", (innerRadius, target) => {
            return -valueAxis.valueToPoint(-3.8).y;
          });
          categoryAxis.tooltip.defaultState.properties.opacity = 0.; // hide tooltip
          /*categoryAxis.renderer.axisFills.template.disabled = false;
          categoryAxis.renderer.axisFills.template.fillOpacity = 1;
          categoryAxis.renderer.axisFills.template.fill = am4core.color("#e7e8e8");
          */

          // valueAxis
          var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
          //valueAxis.renderer.labels.template.disabled = true;
          valueAxis.renderer.labels.template.fill = am4core.color("#414042");
          valueAxis.min = 0;
          valueAxis.max = 10;
          valueAxis.renderer.minGridDistance = 10;
          valueAxis.fontSize = '10px';
          // valueAxis.renderer.labels.template.adapter.add("dy", (innerRadius, target) => {
            //     return -valueAxis.valueToPoint(-3.7).y;
            // });

            // series
            var series = chart.series.push(new am4charts.RadarColumnSeries());
            series.columns.template.width = am4core.percent(100);
            series.columns.template.strokeWidth = 0;
            series.columns.template.column.propertyFields.fill = "color";
            series.dataFields.categoryX = "category";
            series.dataFields.valueY = "value";

            // series tootltip
            series.columns.template.tooltipText = "{categoryX}: {valueY.value}";
            series.tooltip.getFillFromObject = false;
            series.tooltip.background.fill = am4core.color("#f7aa00");
            series.tooltip.label.fill = am4core.color("#414042");
            series.tooltip.label.fontWeight = 'bold';
            series.tooltip.background.strokeOpacity = 0;

            // center image
            var image = categoryAxis.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "middle";
            image.href = "{{ asset('/images/icons/happimynd-logo.png') }}";
            image.width = am4core.percent(23);
            image.height = am4core.percent(23);
            image.zIndex = -1; // grid over series

            var circle = chart.seriesContainer.createChild(am4core.Circle);
            circle.horizontalCenter = "middle";
            circle.verticalCenter = "middle";
            circle.fill = am4core.color('#e7e8e8');
            circle.zIndex = -5; // grid over seriesd
            categoryAxis.events.on('sizechanged', (ev) => {
              circle.radius = -valueAxis.valueToPoint(11.5).y;
            });


            function generateRadarData() {
              let data = [];
              for (let i in wheel_data) {

                // capitalize
                for (let t in wheel_data[i].data) {
                  wheel_data[i].data[t].category = wheel_data[i].data[t].category.toUpperCase();
                }
                data = data.concat(wheel_data[i].data);
                createRange(wheel_data[i].range, wheel_data[i].data, i);
              }
              //console.log(data);
              return data;
            }

            function createRange(name, data, index) {
              let axisRange = categoryAxis.axisRanges.create();
              axisRange.text = name.toUpperCase();

              // first country
              axisRange.category = data[0].category;

              // last country
              axisRange.endCategory = data[data.length - 1].category;

              // range grid
              //axisRange.grid.disabled = true;
              axisRange.label.mouseEnabled = false;
              axisRange.grid.stroke = am4core.color("#FFFFFF");
              axisRange.grid.strokeWidth = 1;
              axisRange.grid.strokeOpacity = 1;
              axisRange.grid.strokeDasharray = "0,0";
              axisRange.grid.adapter.add("radius", (innerRadius, target) => {
                return -valueAxis.valueToPoint(13.8).y;
              });

              // range background
              let axisFill = axisRange.axisFill;
              axisFill.fill = data[0].color; // chart.colors.next

              axisFill.disabled = false;
              axisFill.fillOpacity = 1;
              axisFill.adapter.add("innerRadius", (innerRadius, target) => {
                return -valueAxis.valueToPoint(11.5).y;
              });
              axisFill.adapter.add("radius", (innerRadius, target) => {
                return -valueAxis.valueToPoint(13.8).y;
              });
              //axisFill.togglable = true;
              //axisFill.showSystemTooltip = true;
              //axisFill.readerTitle = "click to zoom";
              //axisFill.cursorOverStyle = am4core.MouseCursorStyle.pointer;

              // range label
              let axisLabel = axisRange.label;
              axisLabel.location = 0.5;
              axisLabel.fill = am4core.color("#FFFFFF");
              axisLabel.fontWeight = 'bold';
              axisLabel.fontSize = '10px';
              axisLabel.adapter.add("radius", (innerRadius, target) => {
                return -valueAxis.valueToPoint(-1.9).y;
              });
            }

            chart.data = generateRadarData();
            $('g:has(> g[stroke="#3cabff"])').hide(); //to hide amcharts logo

            // not to remove this,
            // this is to handle if user open report in new tab then printview doesn't show wheel of life
            chart.events.on('ready', () => {
              setTimeout(function(){window.print()}, 1000);
            });
          });
        </script>
      </body>
      </html>