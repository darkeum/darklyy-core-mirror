@extends('scada::public.project.layout')
@section('main')

  <main class="col-md-12 ms-sm-auto col-lg-12 px-md-4" style="background-color: #fff; ">
    <div id="simplescadadiv">
      <div id="simplescadaintf" style=" left: 0px; top: 0px; width: 2560px; height: 1600px; background-color: #fff; overflow: hidden; transform-origin: left top; transform: scale(0.605625);">
        <div id="sswndblock"></div>

        <div id="p0s0o1w_rot" style="position: absolute; left: 1565px; top: 376px; width: 65px; height: 65px; opacity: 1; display: block;">
          <div id="p0s0o1w_" data-w="65" data-h="65" data-clw="65" data-clh="65" data-i="-1" data-cl="#fff" style="left: 1565px; top: 376px; width: 65px; height: 65px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-WpFMACPxAqullyVc.png&quot;) 0px 0px / 65px 65px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-WpFMACPxAqullyVc.png&quot;); -webkit-mask-size: 65px 65px; -webkit-mask-position: 0px 0px;"></div>
        </div>

        <div id="p0s0o3w_" data-cl="#0080FF" style="position: absolute; left: 535px; top: 248px; width: 425px; height: 207px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 16px; top: 46px; width: 32px; height: 115px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl4.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl4.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 160px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p13.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p13.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 46px; top: 160px; width: 520px; height: 32px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
        
        </div>

        <div id="p0s0o5w_" data-cl="#0080FF" style="position: absolute; left: 752px; top: 232px; width: 528px; height: 1040px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 16px; top: 62px; width: 48px; height: 440px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl4.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl4.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 491px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p13.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p13.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 62px; top: 491px; width: 410px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 467px; top: 491px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o6w_" data-cl="#FF0000" style="position: absolute; left: 828px; top: 96px; width: 448px; height: 1040px; color: rgb(255, 0, 0); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 16px; top: 62px; width: 48px; height: 436px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl4.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl4.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 495px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p13.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p13.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 62px; top: 495px; width: 330px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 390px; top: 495px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o7w_" data-cl="#0080FF" style="position: absolute; left: 1762px; top: 708px; width: 289px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 165px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 225px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o8w_" data-cl="#0080FF" style="position: absolute; left: 1571px; top: 249px; width: 379px; height: 208px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 331px; top: 46px; width: 32px; height: 116px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 331px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl4.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl4.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 331px; top: 160px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p3.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p3.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 46px; top: 160px; width: 287px; height: 32px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 160px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
        </div>
        <div id="p0s0o9w_" data-cl="#0080FF" style="position: absolute; left: 1815px; top: 233px; width: 233px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 109px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 169px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o10w_" data-cl="#0080FF" style="position: absolute; left: 1898px; top: 847px; width: 144px; height: 64px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 45px; top: 17px; width: 54px; height: 30px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 17px; top: 17px; width: 30px; height: 30px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 97px; top: 17px; width: 30px; height: 30px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o11w_rot" style="position: absolute; left: 1685px; top: 265px; width: 127px; height: 84px; opacity: 1; display: block;">
          <div id="p0s0o11w_" data-w="127" data-h="84" data-clw="127" data-clh="84" data-i="-1" data-cl="#fff" style="left: 1685px; top: 265px; width: 127px; height: 84px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;) 0px 0px / 127px 84px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;); -webkit-mask-size: 127px 84px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o12w_" data-cl="#0080FF" style="position: absolute; left: 1570px; top: 726px; width: 328px; height: 184px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 17px; top: 45px; width: 30px; height: 94px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 17px; top: 17px; width: 30px; height: 30px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl4.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl4.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 17px; top: 137px; width: 30px; height: 30px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p13.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p13.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 45px; top: 137px; width: 238px; height: 30px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 281px; top: 137px; width: 30px; height: 30px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 30px rgb(0, 128, 255); -webkit-mask-size: 30px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o13w_rot" style="position: absolute; left: 1474px; top: 593px; width: 107px; height: 193px; opacity: 1; display: block;">
          <div id="p0s0o13w_" data-w="107" data-h="193" data-clw="107" data-clh="193" data-i="-1" data-cl="#fff" style="left: 1474px; top: 1073px; width: 107px; height: 193px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pto4-new.png&quot;) 0px 0px / 107px 193px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pto4-new.png&quot;); -webkit-mask-size: 107px 193px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o14w_rot" style="position: absolute; left: 1464px; top: 115px; width: 107px; height: 193px; opacity: 1; display: block;">
          <div id="p0s0o14w_" data-w="107" data-h="193" data-clw="107" data-clh="193" data-i="-1" data-cl="#fff" style="left: 1464px; top: 115px; width: 107px; height: 193px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pto4-new.png&quot;) 0px 0px / 107px 193px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pto4-new.png&quot;); -webkit-mask-size: 107px 193px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o15w_rot" style="position: absolute; left: 1842px; top: 550px; width: 62px; height: 64px; opacity: 1; display: block;">
          <div id="p0s0o15w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1842px; top: 550px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o16w_rot" style="position: absolute; left: 1289px; top: 205px; width: 62px; height: 64px; opacity: 1; display: block;">
          <div id="p0s0o16w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1289px; top: 205px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o17w_" data-cl="#FF0000" style="position: absolute; left: 56px; top: 96px; width: 256px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 132px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e23.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e23.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 192px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o18w_" data-cl="#0080FF" style="position: absolute; left: 1754px; top: 192px; width: 136px; height: 168px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 14px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 72px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p14.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p14.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 72px; top: 62px; width: 48px; height: 44px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 72px; top: 104px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p3.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p3.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 62px; top: 104px; width: 14px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 104px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
        </div>
        <table id="p0s0o19w_tb" style="position: absolute; border-spacing: 0px; left: 16px; top: 110px; width: 56px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o19w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                T1
                
                              
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o20w_tb" style="position: absolute; border-spacing: 0px; left: 16px; top: 248px; width: 56px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o20w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                T2
                
                              
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o21w_tb" style="position: absolute; border-spacing: 0px; left: 2081px; top: 256px; width: 56px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o21w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                T21
                
                              
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o22w_tb" style="position: absolute; border-spacing: 0px; left: 2090px; top: 725px; width: 56px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o22w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                T4
                
                              
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o23w_tb" style="position: absolute; border-spacing: 0px; left: 2090px; top: 855px; width: 56px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o23w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                B1
                
                              
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o26w_tb" style="display: none; position: absolute; border-spacing: 0px; left: 184px; top: 400px; width: 56px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o26w_" class="s-unsel">
                   <div id="wrapper">
                                    <div id="termometer">
                                        <div id="temperature" style="height:0" data-value="0°C"></div>
                                        <div id="graduations"></div>
                                    </div>
                                </div>
                                <div id="playground">

                                    <p id="unit"> Температура наружного воздуха</p>
                                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o27w_tb" style="position: absolute; border-spacing: 0px; left: 1770px; top: 157px; width: 56px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o27w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                М1
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o27w_tb_no" style="position: absolute; border-spacing: 0px; left: 1748px; top: 179px; width: 180px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o27w_no" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 18px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #9e9e9e; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 180px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                Не опрашивается
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o28w_tb" style="position: absolute; border-spacing: 0px; left: 1771px; top: 336px; width: 56px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o28w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                М2       
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o28w_tb_no" style="position: absolute; border-spacing: 0px; left: 1748px; top: 360px; width: 180px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o28w_no" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 18px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #9e9e9e; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 180px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
              Не опрашивается    
              </td>
            </tr>
          </tbody>
        </table>
        <div id="p0s0o30w_rot" style="position: absolute; left: 1333px; top: 89px; width: 62px; height: 64px; opacity: 1; display: block;">
          <div id="p0s0o30w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1333px; top: 89px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-IeIc3zzwPc9AUMYx.png?v1&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-IeIc3zzwPc9AUMYx.png?v1&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o31w_rot" style="position: absolute; left: 268px; top: 96px; width: 66px; height: 68px; opacity: 1; display: block;">
          <div id="p0s0o31w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 268px; top: 96px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png?v1&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o32w_rot" style="position: absolute; left: 1231px; top: 98px; width: 66px; height: 68px; opacity: 1; display: block;">
          <div id="p0s0o32w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 1231px; top: 98px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o33w_" data-cl="#FF0000" style="position: absolute; left: 1252px; top: 97px; width: 132px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 64px; top: 16px; width: 4px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 67px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o34w_rot" style="position: absolute; left: 1768px; top: 72px; width: 62px; height: 64px; opacity: 1; display: block;">
          <div id="p0s0o34w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1768px; top: 72px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-qi0bEbGKsCU19c.png&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o35w_" data-cl="#FF0000" style="position: absolute; left: 1349px; top: 97px; width: 188px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 63px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 124px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e14.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e14.png&quot;);"></div>
        </div>
        <div id="p0s0o36w_" data-cl="#FF0000" style="position: absolute; left: 1499px; top: 97px; width: 547px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 423px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e12.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e12.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 483px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p19.png&quot;);"></div>
        </div>
        <div id="p0s0o37w_rot" style="position: absolute; left: 2000px; top: 97px; width: 66px; height: 68px; opacity: 1; display: block;">
          <div id="p0s0o37w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 2000px; top: 97px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o38w_rot" style="position: absolute; left: 2002px; top: 233px; width: 66px; height: 68px; opacity: 1; display: block;">
          <div id="p0s0o38w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 2002px; top: 233px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o39w_" data-cl="#0080FF" style="position: absolute; left: 1500px; top: 231px; width: 176px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 52px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e12.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e12.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 112px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o40w_" data-cl="#0080FF" style="position: absolute; left: 1600px; top: 192px; width: 136px; height: 168px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 12px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p7.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p7.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 72px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 64px; width: 48px; height: 42px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p20.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 104px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p13.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p13.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 62px; top: 104px; width: 12px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 72px; top: 104px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o41w_rot" style="position: absolute; left: 267px; top: 231px; width: 66px; height: 68px; opacity: 1; display: block;">
          <div id="p0s0o41w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 267px; top: 231px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o42w_" data-cl="#0080FF" style="position: absolute; left: 56px; top: 230px; width: 258px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 134px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e26.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e26.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 194px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o43w_" data-cl="#0080FF" style="position: absolute; left: 1258px; top: 708px; width: 288px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 63px; top: 16px; width: 164px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 224px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e14.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e14.png&quot;);"></div>
        </div>
        <div id="p0s0o44w_rot" style="position: absolute; left: 1238px; top: 576px; width: 66px; height: 68px; opacity: 1; display: block;">
          <div id="p0s0o44w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 1238px; top: 576px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o45w_" data-cl="#FF0000" style="position: absolute; left: 1258px; top: 576px; width: 132px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 8px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 68px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o46w_rot" style="position: absolute; left: 1338px; top: 569px; width: 62px; height: 64px; opacity: 1; display: block;">
          <div id="p0s0o46w_" data-w="62" data-h="64" data-clw="62" data-clh="64" data-i="-1" data-cl="#fff" style="left: 1338px; top: 569px; width: 62px; height: 64px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-IeIc3zzwPc9AUMYx.png?v1&quot;) 0px 0px / 62px 64px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-IeIc3zzwPc9AUMYx.png?v1&quot;); -webkit-mask-size: 62px 64px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o47w_" data-cl="#FF0000" style="position: absolute; left: 1354px; top: 576px; width: 192px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 68px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 128px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e14.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e14.png&quot;);"></div>
        </div>
        <div id="p0s0o48w_rot" style="position: absolute; left: 1238px; top: 708px; width: 66px; height: 68px; opacity: 1; display: block;">
          <div id="p0s0o48w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 1238px; top: 1191px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o49w_rot" style="position: absolute; left: 2004px; top: 576px; width: 66px; height: 68px; opacity: 1; display: block;">
          <div id="p0s0o49w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 2004px; top: 576px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o50w_rot" style="position: absolute; left: 2007px; top: 708px; width: 66px; height: 68px; opacity: 1; display: block;">
          <div id="p0s0o50w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 2007px; top: 1191px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o51w_rot" style="position: absolute; left: 2008px; top: 856px; width: 38px; height: 39px; opacity: 1; display: block;">
          <div id="p0s0o51w_" data-w="38" data-h="39" data-clw="38" data-clh="39" data-i="-1" data-cl="#fff" style="left: 2008px; top: 1329px; width: 38px; height: 39px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 38px 39px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 38px 39px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o52w_" data-cl="#FF0000" style="position: absolute; left: 1509px; top: 576px; width: 540px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 416px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e12.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e12.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 476px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <table id="p0s0o53w_tb" style="position: absolute; border-spacing: 0px; left: 2080px; top: 112px; width: 56px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o53w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                T11         
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o54w_tb" style="position: absolute; border-spacing: 0px; left: 2090px; top: 590px; width: 56px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o54w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                T3    
              </td>
            </tr>
          </tbody>
        </table>
        <div id="p0s0o55w_rot" style="position: absolute; left: 1685px; top: 177px; width: 127px; height: 84px; opacity: 1; display: block;">
          <div id="p0s0o55w_" data-w="127" data-h="84" data-clw="127" data-clh="84" data-i="-1" data-cl="#fff" style="left: 1685px; top: 177px; width: 127px; height: 84px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;) 0px 0px / 127px 84px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;); -webkit-mask-size: 127px 84px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o56w_rot" style="position: absolute; left: 1721px; top: 296px; width: 48px; height: 48px; opacity: 1; display: block;">
          <div id="p0s0o56w_" data-w="144" data-h="96" data-clw="48" data-clh="48" data-i="-1" data-cl="#FF0000" style="left: 1721px; top: 296px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Animated_5.sbm&quot;) -48px -48px / 144px 96px #adadad;; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Animated_5.sbm&quot;); -webkit-mask-size: 144px 96px; -webkit-mask-position: -48px -48px;"></div>
        </div>
        <div id="p0s0o57w_rot" style="position: absolute; left: 1721px; top: 209px; width: 48px; height: 48px; opacity: 1; display: block;">
          <div id="p0s0o57w_" data-w="144" data-h="96" data-clw="48" data-clh="48" data-i="-1" data-cl="#FF0000" style="left: 1721px; top: 209px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Animated_5.sbm&quot;) -48px -48px / 144px 96px #adadad;; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Animated_5.sbm&quot;); -webkit-mask-size: 144px 96px; -webkit-mask-position: -48px -48px;"></div>
        </div>
        <table id="p0s0o58w_cpttb" style="display: none; position: absolute; border-spacing: 0px; left: 1320px; top: 176px; width: 40px; height: 32px;">
          <tbody>
            <tr>
              <td id="p0s0o58w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="display: none; font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: #4caf50; font-weight: bold;  border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                °С
                    
              </td>
            </tr>
          </tbody>
        </table>
        <div id="p0s0o58w_" class="s-unsel" tabindex="0" data-vl="60" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="display: none; position: absolute; left: 1280px; top: 176px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: #4caf50; font-weight: bold; background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
          60
        </div>
        <table id="p0s0o59w_cpttb" style="position: absolute; border-spacing: 0px; left: 1872px; top: 500px; width: 40px; height: 32px;">
          <tbody>
            <tr>
              <td id="p0s0o59w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: #4caf50; font-weight: bold; background-color: rgba(109, 109, 109, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                °С
              </td>
            </tr>
          </tbody>
        </table>
        <div id="p0s0o59w_" class="s-unsel" tabindex="0" data-vl="60" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1832px; top: 500px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center; color: #4caf50; font-weight: bold; background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
          60
          
        </div>
        <table id="p0s0o60w_cpttb" style="display: none; position: absolute; border-spacing: 0px; left: 1800px; top: 32px; width: 40px; height: 32px;">
          <tbody>
            <tr>
              <td id="p0s0o60w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: #4caf50;     font-weight: bold; border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                °С     
              </td>
            </tr>
          </tbody>
        </table>
        <div id="p0s0o60w_" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style=" display: none;position: absolute; left: 1760px; top: 32px; border-radius: 2px; width: 40px; height: 32px; line-height: 32px; text-align: center;     color: #4caf50;     font-weight: bold; white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
          0
        </div>
        <div id="p0s0o61w_rot" style="position: absolute; left: 1370px; top: 1217px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o61w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1370px; top: 1217px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o62w_rot" style="position: absolute; left: 1074px; top: 1130px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o62w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1074px; top: 1130px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o63w_rot" style="position: absolute; left: 962px; top: 937px; width: 49px; height: 21px; transform: rotate(270deg); opacity: 1; display: block;">
          <div id="p0s0o63w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 962px; top: 937px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o64w_rot" style="position: absolute; left: 996px; top: 976px; width: 49px; height: 21px; transform: rotate(270deg); opacity: 1; display: block;">
          <div id="p0s0o64w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 996px; top: 976px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o65w_rot" style="position: absolute; left: 1448px; top: 260px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o65w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1448px; top: 260px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o66w_" data-cl="#0080FF" style="position: absolute; left: 1256px; top: 231px; width: 280px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 156px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 216px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e14.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e14.png&quot;);"></div>
        </div>
        <div id="p0s0o67w_rot" style="position: absolute; left: 1416px; top: 259px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o67w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1416px; top: 259px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o68w_rot" style="position: absolute; left: 768px; top: 262px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o68w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 768px; top: 262px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o69w_rot" style="position: absolute; left: 836px; top: 125px; width: 57px; height: 21px; opacity: 1; display: block;">
          <div id="p0s0o69w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 836px; top: 125px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o70w_rot" style="position: absolute; left: 1424px; top: 126px; width: 57px; height: 21px; opacity: 1; display: block;">
          <div id="p0s0o70w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1424px; top: 126px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o71w_rot" style="position: absolute; left: 1744px; top: 126px; width: 57px; height: 21px; opacity: 1; display: block;">
          <div id="p0s0o71w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1744px; top: 126px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o72w_rot" style="position: absolute; left: 1576px; top: 260px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o72w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1576px; top: 260px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o73w_rot" style="position: absolute; left: 1912px; top: 262px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o73w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1912px; top: 262px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o74w_rot" style="position: absolute; left: 1075px; top: 256px; width: 57px; height: 21px; transform: rotate(90deg); opacity: 1; display: block;">
          <div id="p0s0o74w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1075px; top: 256px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o75w_rot" style="position: absolute; left: 1037px; top: 920px; width: 57px; height: 21px; transform: rotate(90deg); opacity: 1; display: block;">
          <div id="p0s0o75w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1037px; top: 920px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o76w_rot" style="position: absolute; left: 1162px; top: 1166px; width: 57px; height: 21px; opacity: 1; display: block;">
          <div id="p0s0o76w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1162px; top: 1166px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o77w_rot" style="position: absolute; left: 1415px; top: 1084px; width: 57px; height: 21px; opacity: 1; display: block;">
          <div id="p0s0o77w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1415px; top: 1084px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o78w_rot" style="position: absolute; left: 1298px; top: 1086px; width: 57px; height: 24px; opacity: 1; display: block;">
          <div id="p0s0o78w_" data-w="1140" data-h="24" data-clw="57" data-clh="24" data-i="-1" data-cl="#fff" style="left: 1298px; top: 1086px; width: 57px; height: 24px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px -24px / 1140px 24px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 24px; -webkit-mask-position: 0px -24px;"></div>
        </div>
        <div id="p0s0o79w_rot" style="position: absolute; left: 1882px; top: 1084px; width: 57px; height: 21px; opacity: 1; display: block;">
          <div id="p0s0o79w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1882px; top: 1084px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o80w_rot" style="position: absolute; left: 1572px; top: 1084px; width: 57px; height: 21px; opacity: 1; display: block;">
          <div id="p0s0o80w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1572px; top: 1084px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o81w_rot" style="position: absolute; left: 1625px; top: 1218px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o81w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1625px; top: 1218px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o82w_rot" style="position: absolute; left: 1802px; top: 1344px; width: 31px; height: 13px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o82w_" data-w="620" data-h="13" data-clw="31" data-clh="13" data-i="-1" data-cl="#fff" style="left: 1802px; top: 1344px; width: 31px; height: 13px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 620px 13px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 620px 13px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o83w_rot" style="position: absolute; left: 1235px; top: 232px; width: 66px; height: 68px; opacity: 1; display: block;">
          <div id="p0s0o83w_" data-w="66" data-h="68" data-clw="66" data-clh="68" data-i="-1" data-cl="#fff" style="left: 1235px; top: 232px; width: 66px; height: 68px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 66px 68px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 66px 68px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o85w_rot" style="position: absolute; left: 1853px; top: 840px; width: 96px; height: 72px; opacity: 1; display: block;">
          <div id="p0s0o85w_" data-w="96" data-h="72" data-clw="96" data-clh="72" data-i="-1" data-cl="#fff" style="left: 1853px; top: 840px; width: 96px; height: 72px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-TPmbdwFt6zpDNJ.png&quot;) 0px 0px / 96px 72px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-TPmbdwFt6zpDNJ.png&quot;); -webkit-mask-size: 96px 72px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <table id="p0s0o86w_tb" style="position: absolute; border-spacing: 0px; left: 1778px; top: 665px; width: 56px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o86w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 34px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 56px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                М3
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o86w_tb_no" style="position: absolute; border-spacing: 0px; left: 1755px; top: 692px; width: 180px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o86w_no" class="s-unsel" data-cl="#FFFFFFFF" data-spw="8" style="font-size: 18px; font-family: RobotoLight; letter-spacing: -0.6px; line-height: 40px; padding: 0px;color: #9e9e9e; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 180px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                Не опрашивается
              </td>
            </tr>
          </tbody>
        </table>

       

        <div id="p0s0o88w_" data-cl="#0080FF" style="position: absolute; left: 1510px; top: 708px; width: 234px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 110px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e12.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/e12.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 170px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o89w_rot" style="position: absolute; left: 1693px; top: 693px; width: 127px; height: 84px; opacity: 1; display: block;">
          <div id="p0s0o89w_" data-w="127" data-h="84" data-clw="127" data-clh="84" data-i="-1" data-cl="#fff" style="left: 1693px; top: 1174px; width: 127px; height: 84px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;) 0px 0px / 127px 84px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/imgonline-com-ua-Transparent-backgr-7Xc7fREVZp9Wo1.png&quot;); -webkit-mask-size: 127px 84px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o90w_rot" style="position: absolute; left: 1729px; top: 723px; width: 48px; height: 48px; opacity: 1; display: block;">
          <div id="p0s0o90w_" data-w="144" data-h="96" data-clw="48" data-clh="48" data-i="-1" data-cl="#FF0000" style="left: 1729px; top: 1206px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Animated_5.sbm&quot;) -48px -48px / 144px 96px #adadad; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Animated_5.sbm&quot;); -webkit-mask-size: 144px 96px; -webkit-mask-position: -48px -48px;"></div>
        </div>
        <div id="p0s0o91w_rot" style="position: absolute; left: 1586px; top: 1217px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o91w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1586px; top: 1217px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
  
   
        <table id="p0s0o93w_cpttb" style="position: absolute; border-spacing: 0px; left: 1900px; top: 32px; width: 40px; height: 32px; display:none;">
          <tbody>
            <tr>
              <td id="p0s0o93w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px;    color: #4caf50;
                                                                                                                                                                                                                                                                                                                                font-weight: bold; background-color: rgba(109, 109, 109, 0.196); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                °С
              </td>
            </tr>
          </tbody>
        </table>
        <div id="p0s0o93w_" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1840px; top: 32px; border-radius: 2px; width: 60px; height: 32px; line-height: 32px; text-align: center; color: #4caf50;font-weight: bold;  background-color: rgba(109, 109, 109, 0.196); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text; display:none;" data-cl="#6D6D6D00">
          0 
        </div>
        <table id="p0s0o94w_cpttb" style="position: absolute; border-spacing: 0px; left: 1976px; top: 500px; width: 40px; height: 32px;">
          <tbody>
            <tr>
              <td id="p0s0o94w_cpt" class="s-unsel" data-cl="#00FF40FF" data-spw="6" style="font-size: 24px; font-family: RobotoRegular; line-height: 30px; padding: 0px; color: #4caf50; font-weight: bold; background-color: rgba(109, 109, 109, 0.196); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 40px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                °С
                
                              
              </td>
            </tr>
          </tbody>
        </table>
        <div id="p0s0o94w_" class="s-unsel" tabindex="0" data-vl="60" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1916px; top: 500px; border-radius: 2px; width: 60px; height: 32px; line-height: 32px; text-align: center; color: #4caf50; font-weight: bold; background-color: rgba(109, 109, 109, 0.196); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
          60
        </div>
        <table id="p0s0o97w_tb" style="position: absolute; border-spacing: 0px; left: 1196px; top: 40px; width: 128px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o97w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75" style="font-size: 24px;     font-weight: bold; font-family: RobotoRegular; line-height: 24px; padding: 0px;color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                Клапан отопления
              </td>
            </tr>
          </tbody>
        </table>
        <div id="p0s0o98w_" class="s-unsel" tabindex="0" data-vl="Авто" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1342px; top: 47px; border-radius: 2px; width: 64px; height: 32px; font-weight: bold; line-height: 32px; text-align: center; color: rgba(0, 0, 0, 0.7); background-color: rgb(0, 255, 128); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#00FF80FF">
          Авто
          
          
                  
        </div>
        <table id="p0s0o99w_tb" style="position: absolute; border-spacing: 0px; left: 1202px; top: 510px; width: 128px; height: 48px;">
          <tbody>
            <tr>
              <td id="p0s0o99w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75" style="font-size: 24px;   font-weight: bold; font-family: RobotoRegular; line-height: 26px; padding: 0px;color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                Клапан ГВС
                
                
                              
              </td>
            </tr>
          </tbody>
        </table>
        <div id="p0s0o100w_" class="s-unsel" tabindex="0" data-vl="Авто" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1348px; top: 520px; border-radius: 2px; width: 64px; height: 32px; line-height: 32px; text-align: center; color: rgba(0, 0, 0, 0.7); background-color: rgb(0, 255, 128); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text; font-weight: bold;" data-cl="#00FF80FF">
          Авто
          
                  
        </div>
        
       

 
        
        <table id="p0s0o106w_tb" style="position: absolute; border-spacing: 0px; left: 1874px; top: 530px; width: 128px; height: 48px;">
          <tbody>
            <tr>
              <td id="crash_rvd_50" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="display:none; font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display:none;">
                Авария 
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o107w_tb" style="position: absolute; border-spacing: 0px; left: 1808px; top: 60px; width: 128px; height: 48px;">
          <tbody>
            <tr>
              <td id="crash_rvd_30" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: none;">
                Авария
              </td>
            </tr>
          </tbody>
        </table>
        <table id="p0s0o108w_tb" style="position: absolute; border-spacing: 0px; left: 1328px; top: 200px; width: 128px; height: 48px;">
          <tbody>
            <tr>
              <td id="crash_rvd_40" class="s-unsel" data-cl="#FF0000FF" data-spw="4.75" style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display:none;">
                Авария
                              
              </td>
            </tr>
          </tbody>
        </table>
         
 
  
        <div id="p0s0o138w_rot" style="position: absolute; left: 992px; top: 808px; width: 49px; height: 21px; transform: rotate(270deg); opacity: 1; display: block;">
          <div id="p0s0o138w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 992px; top: 808px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o139w_rot" style="position: absolute; left: 1448px; top: 716px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o139w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1448px; top: 716px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
    
        <div id="p0s0o141w_rot" style="position: absolute; left: 1416px; top: 715px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o141w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1416px; top: 715px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o142w_rot" style="position: absolute; left: 768px; top: 718px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o142w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 768px; top: 718px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o143w_rot" style="position: absolute; left: 856px; top: 717px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o143w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 856px; top: 717px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px -21px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px -21px;"></div>
        </div>
        <div id="p0s0o144w_rot" style="position: absolute; left: 840px; top: 1085px; width: 57px; height: 21px; opacity: 1; display: block;">
          <div id="p0s0o144w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 840px; top: 1085px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o145w_rot" style="position: absolute; left: 1424px; top: 582px; width: 57px; height: 21px; opacity: 1; display: block;">
          <div id="p0s0o145w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1424px; top: 582px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o146w_rot" style="position: absolute; left: 1744px; top: 582px; width: 57px; height: 21px; opacity: 1; display: block;">
          <div id="p0s0o146w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1744px; top: 582px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o147w_rot" style="position: absolute; left: 1576px; top: 716px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o147w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1576px; top: 716px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o148w_rot" style="position: absolute; left: 1912px; top: 718px; width: 49px; height: 21px; transform: rotate(180deg); opacity: 1; display: block;">
          <div id="p0s0o148w_" data-w="980" data-h="21" data-clw="49" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1912px; top: 718px; width: 49px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 980px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 980px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
        <div id="p0s0o149w_rot" style="position: absolute; left: 1075px; top: 712px; width: 57px; height: 21px; transform: rotate(90deg); opacity: 1; display: block;">
          <div id="p0s0o149w_" data-w="1140" data-h="21" data-clw="57" data-clh="21" data-i="-1" data-cl="#fff" style="left: 1075px; top: 712px; width: 57px; height: 21px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;) 0px 0px / 1140px 21px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/Без%20имени-5.png&quot;); -webkit-mask-size: 1140px 21px; -webkit-mask-position: 0px 0px;"></div>
        </div>
   
        <div id="p0s0o151w_" data-cl="#0080FF" style="position: absolute; left: 1051px; top: 392px; width: 557px; height: 64px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 46px; top: 16px; width: 465px; height: 32px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>

          <div data-pch="1" style="position: absolute; left: 509px; top: 16px; width: 32px; height: 32px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 32px rgb(0, 128, 255); -webkit-mask-size: 32px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
       
        
       
      
        
        <div id="p0s0o161w_" data-cl="#FF0000" style="position: absolute; left: 288px; top: 96px; width: 992px; height: 80px; color: rgb(255, 0, 0); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 868px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 928px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(255, 0, 0); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
        <div id="p0s0o162w_" data-cl="#0080FF" style="position: absolute; left: 288px; top: 230px; width: 992px; height: 80px; color: rgb(0, 128, 255); opacity: 1; display: block;">
          <div data-pch="1" style="position: absolute; left: 62px; top: 16px; width: 868px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/p2.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl1.png&quot;);"></div>
          <div data-pch="1" style="position: absolute; left: 928px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(0, 128, 255); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/images/pipes/pfl19.png&quot;);"></div>
        </div>
     
      
       
        
        <div id="p0s0o172w_up" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1392px; top: 80px; border-radius: 2px; width: 42px; height: 32px; line-height: 32px; text-align: center;     color: #4caf50; font-weight: bold; background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
        ▲  
        </div>
        <div id="p0s0o172w_down" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1392px; top: 80px; border-radius: 2px; width: 42px; height: 32px; line-height: 32px; text-align: center;     color: #f44336; font-weight: bold; background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
        ▼  
        </div>

        <div id="p0s0o174w_up" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1400px; top: 560px; border-radius: 2px; width: 42px; height: 32px; line-height: 32px; text-align: center; color: #4caf50; font-weight: bold; background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
        ▲
        </div>
        <div id="p0s0o174w_down" class="s-unsel s-wi" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="5" data-en="1" data-ai="1" data-act="0" style="position: absolute; left: 1400px; top: 560px; border-radius: 2px; width: 42px; height: 32px; line-height: 32px; text-align: center; color: #f44336; font-weight: bold; background-color: rgba(109, 109, 109, 0); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;" data-cl="#6D6D6D00">
        ▼  
        </div>
      </div>
    </div>
  </main>
@endsection

