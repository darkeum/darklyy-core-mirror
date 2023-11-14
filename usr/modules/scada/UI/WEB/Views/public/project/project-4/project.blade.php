@extends('scada::public.project.layout')
@section('main')
    <main class="col-md-12 ms-sm-auto col-lg-12 px-md-4" style="background-color: #fff; ">
        <div id="simplescadadiv">
            <div id="simplescadaintf" style="transform-origin: left top; transform: scale(1);">
                <div id="sswndblock"></div>
                <div id="sbpMainPanel" data-ob="p0sow_"
                    style="position: absolute; display: flex; visibility: visible; top: 61px; max-width: 1920px; height: 48px; z-index: 1000; background-color: rgb(77, 77, 77); box-shadow: rgba(0, 0, 0, 0.3) 0px 2px 3px, rgba(0, 0, 0, 0.05) 0px 2px 3px;">
                </div>
                <div id="p0s0o0w_rot" style="opacity: 0.35; display: block;">
                    <div id="p0s0o0w_" data-w="1904" data-h="1008" data-clw="1904" data-clh="1008" data-i="-1" data-cl="#fff" style="position: relative; width: 1904px; height: 700px;"></div>
                </div>
                <div id="p0s0o1w_rot" style="position: absolute; left: 1032px; top: 155px; width: 96px; height: 64px; transform: rotate(90deg); opacity: 1; display: block;">
                    <div id="p0s0o1w_" data-w="96" data-h="64" data-clw="96" data-clh="64" data-i="-1" data-cl="#FFFFFF"
                        style="left: 1032px; top: 320px; width: 96px; height: 64px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/manometer.png&quot;) 0px 0px / 96px 64px rgb(255, 255, 255); background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/manometer.png&quot;); -webkit-mask-size: 96px 64px; -webkit-mask-position: 0px 0px;">
                    </div>
                </div>
                <div id="p0s0o2w_rot" style="position: absolute; left: 1048px; top: 712px; width: 275px; height: 216px; opacity: 0.588; display: block;">
                    <div id="p0s0o2w_" data-w="275" data-h="216" data-clw="275" data-clh="216" data-i="-1" data-cl="#fff"
                        style="left: 1048px; top: 712px; width: 275px; height: 216px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/imgonline-com-ua-Transparent-backgr-dUdbeQk0d4bPg.png&quot;) 0px 0px / 275px 216px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/imgonline-com-ua-Transparent-backgr-dUdbeQk0d4bPg.png&quot;); -webkit-mask-size: 275px 216px; -webkit-mask-position: 0px 0px;">
                    </div>
                </div>
                <div id="p0s0o3w_" data-cl="#2F8DED" style="position: absolute; left: 584px; top: 190px; width: 640px; height: 408px; color: rgb(47, 141, 237); opacity: 1; display: block;">
                    <div data-pch="1"
                        style="position: absolute; left: 569px; top: 64px; width: 48px; height: 180px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/p20.png&quot;) 0% 0% / 48px rgb(47, 141, 237); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/p20.png&quot;);">
                    </div>
                    <div data-pch="1"
                        style="position: absolute; left: 569px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/p14.png&quot;) 0% 0% / 48px rgb(47, 141, 237); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/p14.png&quot;);">
                    </div>
                    <div data-pch="1"
                        style="position: absolute; left: 569px; top: 244px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/pfl15.png&quot;) 0% 0% / 48px rgb(47, 141, 237); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/pfl15.png&quot;);">
                    </div>
                    <div data-pch="1"
                        style="position: absolute; left: 63px; top: 16px; width: 513px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/p2.png&quot;) 0% 0% / 48px rgb(47, 141, 237); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/p2.png&quot;);">
                    </div>
                    <div data-pch="1"
                        style="position: absolute; left: 15px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(47, 141, 237); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/pfl1.png&quot;);">
                    </div>
                </div>
                <div id="p0s0o4w_rot" style="position: absolute; left: 728px; top: 163px; width: 85px; height: 88px; opacity: 1; display: block;">
                    <div id="p0s0o4w_" data-w="85" data-h="88" data-clw="85" data-clh="88" data-i="-1" data-cl="#fff"
                        style="left: 728px; top: 325px; width: 85px; height: 88px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/imgonline-com-ua-Transparent-backgr-aq2ErG44OUiQRrV.png&quot;) 0px 0px / 85px 88px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/imgonline-com-ua-Transparent-backgr-aq2ErG44OUiQRrV.png&quot;); -webkit-mask-size: 85px 88px; -webkit-mask-position: 0px 0px;">
                    </div>
                </div>
                <div id="p0s0o4w2_rot" style="position: absolute; left: 1144.5px; top: 436px; width: 85px; height: 88px; opacity: 1; display: block;">
                    <div id="p0s0o4w2_" data-w="85" data-h="88" data-clw="85" data-clh="88" data-i="-1" data-cl="#fff"
                        style="left: 728px; top: 325px; width: 65px; height: 179px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pump_sk.png&quot;); /* background-blend-mode: multiply; */ /* -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pump_sk.png&quot;); */ /* -webkit-mask-size: 85px 88px; */ /* -webkit-mask-position: 0px 0px; */">
                    </div>
                </div>
                <div id="p0s0o4w3_rot" style="position: absolute; left: 1032px; top: 250px; width: 85px; height: 88px; opacity: 1; display: block;">
                    <div id="p0s0o4w3_" data-w="85" data-h="88" data-clw="85" data-clh="88" data-i="-1" data-cl="#fff"
                        style="left: 728px; top: 325px; width: 198px; height: 397px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/sk.png&quot;); /* background-blend-mode: multiply; */ /* -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/sk.png&quot;); */ /* -webkit-mask-size: 85px 88px; */ /* -webkit-mask-position: 0px 0px; */">
                    </div>
                </div>

                <table id="p0s0o8w_tb" style="position: absolute; border-spacing: 0px; left: 140px; top: 205px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o8w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75"
                                style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(0, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Потребитель</td>
                        </tr>
                    </tbody>
                </table>

                <div id="p0s0o5w_" data-min="0" data-max="100" data-fz="0" data-ps="0"
                    style="position: absolute; left: 1232px; top: 220px; width: 24px; height: 398px; box-sizing: border-box; background-color: rgb(119, 119, 119); border-color: rgb(0, 0, 0); border-style: solid; border-width: 2px;"
                    data-cl="#BAFFFFFF">
                    <div id="p0s0o5w_lvl" style="position: absolute; background-color: rgb(186, 255, 255); width: 100%; bottom: 0px; height: 0%"></div>
                </div>
                <div id="p0s0o6w_rot" style="position: absolute; left: 569px; top: 195px; width: 59px; height: 61px; opacity: 1; display: block;">
                    <div id="p0s0o6w_" data-w="59" data-h="61" data-clw="59" data-clh="61" data-i="-1" data-cl="#fff"
                        style="left: 569px; top: 357px; width: 59px; height: 61px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;) 0px 0px / 59px 61px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/imgonline-com-ua-Transparent-backgr-gKYyBlyLw8.png&quot;); -webkit-mask-size: 59px 61px; -webkit-mask-position: 0px 0px;">
                    </div>
                </div>
                <div id="p0s0o7w_" data-cl="#2F8DED" style="position: absolute; left: 240px; top: 190px; width: 373px; height: 80px; color: rgb(47, 141, 237); opacity: 1; display: block;">
                    <div data-pch="1"
                        style="position: absolute; left: 64px; top: 16px; width: 245px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/p2.png&quot;) 0% 0% / 48px rgb(47, 141, 237); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/p2.png&quot;);">
                    </div>
                    <div data-pch="1"
                        style="position: absolute; left: 16px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/pfl1.png&quot;) 0% 0% / 48px rgb(47, 141, 237); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/pfl1.png&quot;);">
                    </div>
                    <div data-pch="1"
                        style="position: absolute; left: 309px; top: 16px; width: 48px; height: 48px; background: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/pfl19.png&quot;) 0% 0% / 48px rgb(47, 141, 237); -webkit-mask-size: 48px; background-blend-mode: multiply; -webkit-mask-image: url(&quot;/usr/modules/scada/UI/WEB/Views/public/project/assets/show/pipes/pfl19.png&quot;);">
                    </div>
                </div>
                <table id="p0s0o8w_tb" style="position: absolute; border-spacing: 0px; left: 640px; top: 260px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o8w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75"
                                style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(0, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle; text-align: center; display: table-cell;">
                                Расход</td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o9w_" class="s-unsel" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0"
                    style="position: absolute; left: 741px; top: 270px; border-radius: 2px; width: 64px; height: 32px; line-height: 32px; text-align: center; color: rgba(0, 0, 0, 0.7); background-color: rgb(147, 147, 147); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;"
                    data-cl="#BFBFBFFF">0</div>
                <table id="p0s0o10w_tb" style="position: absolute; border-spacing: 0px; left: 1040px; top: 65px; width: 128px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o10w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75"
                                style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(0, 0, 0); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 128px; text-indent: 0px; vertical-align: middle;  display: table-cell;">
                                Давление</td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o11w_" class="s-unsel" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0"
                    style="position: absolute; left: 1048px; top: 110px; border-radius: 2px; width: 64px; height: 32px; line-height: 32px; text-align: center; color: rgba(0, 0, 0, 0.7); background-color: rgb(191, 191, 191); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;"
                    data-cl="#BFBFBFFF">0</div>
                <table id="p0s0o12w_tb" style="position: absolute; border-spacing: 0px; left: 208px; top: 330px; width: 200px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o12w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75"
                                style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 200px; text-indent: 0px; vertical-align: middle;  display: table-cell;">
                                Режим работы</td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o13w_" class="s-unsel" tabindex="0" data-vl="Авто" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0"
                    style="position: absolute; left: 400px; top: 340px; border-radius: 2px; width: 152px; height: 32px; line-height: 32px; text-align: center; color: rgba(0, 0, 0, 0.7); background-color: rgb(0, 255, 128); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;"
                    data-cl="#00FF80FF">Авто</div>
                <table id="p0s0o14w_tb" style="position: absolute; border-spacing: 0px; left: 208px; top: 262px; width: 200px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o14w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75"
                                style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: #000; background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 200px; text-indent: 0px; vertical-align: middle;  display: table-cell;">
                                Авария</td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o15w_" class="s-unsel" tabindex="0" data-vl="Норма" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0"
                    style="position: absolute; left: 400px; top: 272px; border-radius: 2px; width: 152px; height: 32px; line-height: 32px; text-align: center; color: rgba(0, 0, 0, 0.7); background-color: rgb(0, 255, 128); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;"
                    data-cl="#00FF80FF">Норма</div>
                <button id="p0s0o16w_" class="s-unsel k-button" data-role="button" role="button" aria-disabled="false" tabindex="0" data-cl="#6D6D6D"
                    style="display: none; position: absolute; left: 400px; top: 408px; width: 152px; height: 32px; padding: 0px; background: rgb(109, 109, 109); color: rgba(255, 255, 255, 0.7); border-width: 2px; border-color: rgb(109, 109, 109); overflow-x: hidden; white-space: nowrap; font-size: 15px; font-family: RobotoRegular; letter-spacing: 0.21px;">Включить</button>
                <table id="p0s0o17w_tb" style="position: absolute; border-spacing: 0px; left: 208px; top: 400px; width: 200px; height: 48px;">
                    <tbody>
                        <tr>
                            <td id="p0s0o17w_" class="s-unsel" data-cl="#FFFFFFFF" data-spw="4.75"
                                style="font-size: 20px; font-family: RobotoRegular; line-height: 26px; padding: 0px; color: rgb(255, 255, 255); background-color: rgba(191, 191, 191, 0); border-style: solid; border-width: 0px; border-color: rgb(255, 255, 255); max-width: 200px; text-indent: 0px; vertical-align: middle;  display: table-cell;">
                                Сброс аварии</td>
                        </tr>
                    </tbody>
                </table>
                <div id="p0s0o18w_" data-grid="1" data-cl="rgba(109,109,109,1)" style="position: absolute; left: 675px; top: 320px;  display: block;">
                    <div id="p0s0o18w_gr"
                        style="overflow: hidden auto; white-space: nowrap; color: rgb(255, 255, 0); border-color: rgb(109, 109, 109);  font-size: 14px; font-family: RobotoRegular; letter-spacing: 0.6px; box-shadow: rgba(0, 0, 0, 0.4) 0px 1px 3px; background: rgb(109, 109, 109);">
                        <table id="p0s0o18w_tbl" class="s-unsel s-tbl" data-fc="1" data-fr="1" data-rs="1" data-rc="0" style="left: 1368px; top: 576px; ">
                            <colgroup>
                                <col style="width: 200px; background: rgba(0, 0, 0, 0.08);">
                                <col style="width: 80px;">
                            </colgroup>
                            <tr style="height: 28px; background: rgba(0, 0, 0, 0.08);">
                                <td style="border-color: rgba(0, 0, 0, 0.157); color: rgb(255, 255, 0); font-size: 14px; font-family: RobotoRegular; letter-spacing: 0.6px;">
                                    Задание давления</td>
                                <td id="wd01" style="border-color: rgba(0, 0, 0, 0.157); color: rgb(255, 255, 0); font-size: 14px; font-family: RobotoRegular; letter-spacing: 0.6px;">
                                    0
                                </td>
                            </tr>
                            <tr style="height: 28px;">
                                <td style="border-color: rgba(0, 0, 0, 0.157); color: rgb(255, 255, 0); font-size: 14px; font-family: RobotoRegular; letter-spacing: 0.6px;">
                                    Ток, А</td>
                                <td id="ampere" style="border-color: rgba(0, 0, 0, 0.157); color: rgb(255, 255, 0); font-size: 14px; font-family: RobotoRegular; letter-spacing: 0.6px;">
                                    0
                                </td>
                            </tr>
                            <tr style="height: 28px;">
                                <td style="border-color: rgba(0, 0, 0, 0.157); color: rgb(255, 255, 0); font-size: 14px; font-family: RobotoRegular; letter-spacing: 0.6px;">
                                    Уровень, м</td>
                                <td id="p0s0o5w_lvl_txt" style="border-color: rgba(0, 0, 0, 0.157); color: rgb(255, 255, 0); font-size: 14px; font-family: RobotoRegular; letter-spacing: 0.6px;">
                                    0
                                </td>
                            </tr>
                            <tr style="height: 28px;">
                                <td style="border-color: rgba(0, 0, 0, 0.157); color: rgb(255, 255, 0); font-size: 14px; font-family: RobotoRegular; letter-spacing: 0.6px;">
                                    Частота , Гц</td>
                                <td id="frequency" style="border-color: rgba(0, 0, 0, 0.157); color: rgb(255, 255, 0); font-size: 14px; font-family: RobotoRegular; letter-spacing: 0.6px;">
                                    0
                                </td>
                            </tr>
                            <tr style="height: 28px;">
                                <td style="border-color: rgba(0, 0, 0, 0.157); color: rgb(255, 255, 0); font-size: 14px; font-family: RobotoRegular; letter-spacing: 0.6px;">
                                    Аварии</td>
                                <td style="border-color: rgba(0, 0, 0, 0.157); color: rgb(255, 255, 0); font-size: 14px; font-family: RobotoRegular; letter-spacing: 0.6px;">
                                    Нет
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="p0s0o19w_" class="s-unsel" tabindex="0" data-vl="100" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0"
                    style="position: absolute; left: 1264px; top: 220px; border-radius: 2px; width: 64px; height: 32px; line-height: 32px; text-align: center; color: rgba(0, 0, 0, 0.7); background-color: rgb(0, 255, 128); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;"
                    data-cl="#00FF80FF">150</div>
                <div id="p0s0o20w_" class="s-unsel" tabindex="0" data-vl="0" data-un="" data-pas="0" data-inp="1" data-en="1" data-ai="1" data-act="0"
                    style="position: absolute; left: 1264px; top: 585px; border-radius: 2px; width: 64px; height: 32px; line-height: 32px; text-align: center; color: rgba(0, 0, 0, 0.7); background-color: rgb(191, 191, 191); white-space: nowrap; text-overflow: clip; overflow: hidden; border: none; font-size: 24px; font-family: RobotoRegular; cursor: text;"
                    data-cl="#BFBFBFFF">0</div>
            </div>
        </div>
        <div id="ssrepdiv" style="position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; background: rgb(50, 54, 57); display: none;">
            <div style="position: relative; width: 100%; height: 32px;">
                <button id="btnRepViewClose" class="k-button k-primary" style="position: static; width: 100%; height: 32px; background-color: rgb(0, 151, 167); border-color: rgb(0, 151, 167);">Нажмите
                    здесь, чтобы закрыть просмотр отчета.</button>
            </div>
            <div style="position: absolute; top: 32px; width: 100%; bottom: 0px;">
                <iframe id="ssrepframe" data-viewer="0" src="about:blank" style="width: 100%; height: 100%; border-width: 0px;"></iframe>
            </div>
        </div>
        <div id="sstrendsdiv" style="display: none;"></div>
        <div id="ssmessagesdiv" style="display: none;"></div>
    </main>
@endsection
