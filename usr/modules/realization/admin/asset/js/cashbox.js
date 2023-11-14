export class Cashbox {
    constructor(request){
        if(KkmServer === undefined) throw new Error(`Plugin is not install`);
        if(request?.CashierName === undefined || request?.CashierName == "") throw new Error(`Field "CashierName" is empty!`);
        this.CashierName = request.CashierName;
        if(request?.CashierVATIN === undefined || request?.CashierVATIN == "") throw new Error(`Field "CashierVATIN" is empty!`);
        this.CashierVATIN = request.CashierVATIN;
        if(request?.NumDevice === undefined || request?.NumDevice == "") throw new Error(`Field "NumDevice" is empty!`);
        this.NumDevice = request.NumDevice;
    }
    OpenShift() {
        return new Promise((resolve, reject) => {
            KkmServer.OpenShift(this.NumDevice, this.CashierName, this.CashierVATIN).Execute(data => resolve(data));
        });
    }
    CloseShift() {
        return new Promise((resolve, reject) => {
            KkmServer.CloseShift(this.NumDevice, this.CashierName, this.CashierVATIN).Execute(data => resolve(data));
        });
    }
    XReport() {
        return new Promise((resolve, reject) => {
            KkmServer.XReport(this.NumDevice).Execute(data => resolve(data));
        });
    }
    OfdReport() {
        return new Promise((resolve, reject) => {
            KkmServer.OfdReport(this.NumDevice).Execute(data => resolve(data));
        });
    }
    GetDataForCheck(FiscalNumber = 0, NumberCopies = 0) {
        return new Promise((resolve, reject) => {
            KkmServer.GetDataForCheck(this.NumDevice, FiscalNumber, NumberCopies).Execute(data => resolve(data));
        });
    }
    GetDataKKT() {
        return new Promise((resolve, reject) => {
            KkmServer.GetDataKKT(this.NumDevice).Execute(data => resolve(data));
        });
    }
    RegisterCheck(request){
        return new Promise((resolve, reject) => {
            console.log(request);
            if(request?.TypeCheck === undefined || request?.TypeCheck === "") throw new Error(`Field "TypeCheck" is empty!`);
            const TypeCheck = request.TypeCheck;

            if(request?.items === undefined || request?.items == "") throw new Error(`Field "items" is empty!`);
            const items = request.items;

            if(request?.tableLinkItems === undefined || request?.tableLinkItems == "") throw new Error(`Field "tableLinkItems" is empty!`);
            const tableLinkItems = request.tableLinkItems;
            if(tableLinkItems?.name === undefined || tableLinkItems?.name == "") throw new Error(`Field "tableLinkItems.name" is empty!`);
            if(tableLinkItems?.count === undefined || tableLinkItems?.count == "") throw new Error(`Field "tableLinkItems.count" is empty!`);
            if(tableLinkItems?.price === undefined || tableLinkItems?.price == "") throw new Error(`Field "tableLinkItems.price" is empty!`);
            if(tableLinkItems?.total === undefined || tableLinkItems?.total == "") throw new Error(`Field "tableLinkItems.total" is empty!`);
            if(tableLinkItems?.unit_name === undefined || tableLinkItems?.unit_name == "") throw new Error(`Field "tableLinkItems.unit_name" is empty!`);
            if(tableLinkItems?.ofd === undefined || tableLinkItems?.ofd == "") throw new Error(`Field "tableLinkItems.ofd" is empty!`);

            if(request?.mainData === undefined || request?.mainData == "") throw new Error(`Field "mainData" is empty!`);
            const mainData = request.mainData;
            if(mainData?.org_name === undefined || mainData?.org_name == "") throw new Error(`Field "mainData.org_name" is empty!`);
            if(mainData?.total === undefined || mainData?.total == "") throw new Error(`Field "mainData.total" is empty!`);

            const cash = request.cash ?? 0;
            const electronicPayment = request.electronicPayment ?? 0;
            if((cash == 0 && electronicPayment == 0) || (cash + electronicPayment != +mainData.total)) 
            throw new Error(`cash: ${cash}; electronicPayment: ${electronicPayment}; total: ${mainData.total}`);
            
            const Data = KkmServer.GetDataCheck(TypeCheck, this.NumDevice, "", this.CashierName, this.CashierVATIN);
    
            //При вставке в текст символов ">#10#<" строка при печати выровнеется по центру, где 10 - это на сколько меньше станет строка ККТ
            //ООО \"ТТ-ЭНЕРГО\"
            Data.AddTextString(`>#2#<${mainData.org_name}`, 1);
    
            items.forEach(item => {
                Data.AddRegisterString(
                    // НаименованиеТовара(64 символа)
                    item[ tableLinkItems.name ],
                    // Количество (3 знака после запятой)
                    item[ tableLinkItems.count ],
                    // ЦенаБезСкидки (2 знака после запятой)
                    item[ tableLinkItems.price ],
                    // СуммаСтроки (2 знака после запятой)
                    item[ tableLinkItems.total ],
                    // СтавкаНДС(0(НДС 0%), 10(НДС 10%), 20(НДС 20%), -1(НДС не облагается), 120 (НДС 20/120), 110 (НДС 10/110))
                    -1,
                    // Отдел
                    0,
                    // Код товара EAN13 - не обязательно
                    "",
                    // Признак способа расчета. тег ОФД 1214. Для ФФД.1.05 и выше обязательное поле
                    // 1: "ПРЕДОПЛАТА 100% (Полная предварительная оплата до момента передачи предмета расчета)"
                    // 2: "ПРЕДОПЛАТА (Частичная предварительная оплата до момента передачи предмета расчета)"
                    // 3: "АВАНС"
                    // 4: "ПОЛНЫЙ РАСЧЕТ (Полная оплата, в том числе с учетом аванса в момент передачи предмета расчета)"
                    // 5: "ЧАСТИЧНЫЙ РАСЧЕТ И КРЕДИТ (Частичная оплата предмета расчета в момент его передачи с последующей оплатой в кредит )"
                    // 6: "ПЕРЕДАЧА В КРЕДИТ (Передача предмета расчета без его оплаты в момент его передачи с последующей оплатой в кредит)"
                    // 7: "ОПЛАТА КРЕДИТА (Оплата предмета расчета после его передачи с оплатой в кредит )"
                    4,
                    // Признак предмета расчета. тег ОФД 1212. Для ФФД.1.05 и выше обязательное поле
                    // 1: "ТОВАР (наименование и иные сведения, описывающие товар)"
                    // 2: "ПОДАКЦИЗНЫЙ ТОВАР (наименование и иные сведения, описывающие товар)"
                    // 3: "РАБОТА (наименование и иные сведения, описывающие работу)"
                    // 4: "УСЛУГА (наименование и иные сведения, описывающие услугу)"
                    // 5: "СТАВКА АЗАРТНОЙ ИГРЫ (при осуществлении деятельности по проведению азартных игр)"
                    // 6: "ВЫИГРЫШ АЗАРТНОЙ ИГРЫ (при осуществлении деятельности по проведению азартных игр)"
                    // 7: "ЛОТЕРЕЙНЫЙ БИЛЕТ (при осуществлении деятельности по проведению лотерей)"
                    // 8: "ВЫИГРЫШ ЛОТЕРЕИ (при осуществлении деятельности по проведению лотерей)"
                    // 9: "ПРЕДОСТАВЛЕНИЕ РИД (предоставлении прав на использование результатов интеллектуальной деятельности или средств индивидуализации)"
                    // 10: "ПЛАТЕЖ (аванс, задаток, предоплата, кредит, взнос в счет оплаты, пени, штраф, вознаграждение, бонус и иной аналогичный предмет расчета)"
                    // 11: "АГЕНТСКОЕ ВОЗНАГРАЖДЕНИЕ (вознаграждение (банковского)платежного агента/субагента, комиссионера, поверенного или иным агентом)"
                    // 12: "СОСТАВНОЙ ПРЕДМЕТ РАСЧЕТА (предмет расчета, состоящем из предметов, каждому из которых может быть присвоено вышестоящее значение"
                    // 13: "ИНОЙ ПРЕДМЕТ РАСЧЕТА (предмет расчета, не относящемуся к предметам расчета, которым может быть присвоено вышестоящее значение"
                    // 14: "ИМУЩЕСТВЕННОЕ ПРАВО" (передача имущественных прав)
                    // 15: "ВНЕРЕАЛИЗАЦИОННЫЙ ДОХОД"
                    // 16: "СТРАХОВЫЕ ВЗНОСЫ" (суммы расходов, уменьшающих сумму налога (авансовых платежей) в соответствии с пунктом 3.1 статьи 346.21 Налогового кодекса Российской Федерации)
                    // 17: "ТОРГОВЫЙ СБОР" (суммы уплаченного торгового сбора)
                    // 18: "КУРОРТНЫЙ СБОР"
                    // 19: "ЗАЛОГ"
                    item[ tableLinkItems.ofd ],
                    //штрих - код маркировки товара со сканера(нужно настроить сканер так чтобы не проглатывал управляющие символы)
                    //КИЗ(контрольный идентификационный знак) товарной номенклатуры Тег ОФД 1162(честный знак), можно не указывать
                    // Поддерживаются ШК: 
                    // Без идентификатора экземпляра товара: EAN8, EAN13, ITF14
                    // С идентификатором экземпляра товара: GS1, ШК шуб, ШК табачной продукции., ЕГАИС-2, ЕГАИС-3
                    '',
                    // Единица измерения предмета расчета. Можно не указывать
                    item[ tableLinkItems.unit_name ],
                    // Цифровой код страны происхождения товара (CountryOfOrigin) в соответствии с Общероссийским классификатором стран мира 3 симв. Тег 1230
                    '',
                    // Регистрационный номер таможенной декларации (CustomsDeclaration) 32 симв. Тег 1231
                    '',
                    // Сумма акциза (ExciseAmount) с учетом копеек, включенная в стоимость предмета расчета Тег 1229
                    null,
                    // Дополнительный реквизит предмета расчета тег 1191, Только для ФФД 1.1 !
                    ''
                );
            });
    
            // Наличная оплата (2 знака после запятой)
            Data.Cash = cash;
    
            // Сумма электронной оплаты (2 знака после запятой)
            Data.ElectronicPayment = electronicPayment; 
            // Сумма из предоплаты (зачетом аванса) (2 знака после запятой)
            Data.AdvancePayment = 0;
            // Сумма постоплатой(в кредит) (2 знака после запятой)
            Data.Credit = 0;
            // Сумма оплаты встречным предоставлением (сертификаты, др. мат.ценности) (2 знака после запятой)
            Data.CashProvision = 0;
    
    
            //#if Relese
            // Скидываем данные об агенте - т.к.у Вас неверняка ККТ не зарегистрирована как Агент.
            Data.AgentSign = null; Data.AgentData = null; Data.PurveyorData = null;
            //#endif
            for (let i = 0; i < Data.CheckStrings.length; i++) {
                if (Data.CheckStrings[i] != undefined && Data.CheckStrings[i].Register != undefined) {
                    Data.CheckStrings[i].Register.AgentSign = null;
                    Data.CheckStrings[i].Register.AgentData = null;
                    Data.CheckStrings[i].Register.PurveyorData = null;
                };
            };
    
            console.log(Data);

            Data.Execute(data => resolve(data));
        });
    }
}