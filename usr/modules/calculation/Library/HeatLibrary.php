<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2021 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */

namespace Calculation\Library;

class HeatLibrary
{

	//конструктор класса
	function __construct()
	{
	}

	//обработка типа теплоносителя
	public function prepareEnvironment($type, $returnType = 'name')
	{
		$result = array();
		switch (mb_strtolower($type)) {
			default:
			case "вода":
			case "254fd4a4-7657-4075-bf51-911053b2b4b9":
				$result['name'] = 'Вода';
				$result['key'] = '254FD4A4-7657-4075-BF51-911053B2B4B9';
				break;
			case "этиленгликоль":
			case "эт. глик.":
			case "25fb8684-4742-4778-8d80-0c857ec465d4":
				$result['name'] = 'Эт. Глик.';
				$result['key'] = '25FB8684-4742-4778-8D80-0C857EC465D4';
				break;
			case "пропиленгликоль":
			case "проп. глик.":
			case "95bf98fa-f1d5-4300-ab77-545738f9ddbf":
				$result['name'] = 'Проп. Глик.';
				$result['key'] = '95BF98FA-F1D5-4300-AB77-545738F9DDBF';
				break;
			case "экосол 30":
			case "48289b8e-cc87-4ac4-8fad-ca3f8028b710":
				$result['name'] = 'Экосол 30';
				$result['key'] = '48289B8E-CC87-4AC4-8FAD-CA3F8028B710';
				break;
			case "экосол 40":
			case "8aaec999-2228-4443-a5d8-da6cea12fe5e":
				$result['name'] = 'Экосол 40';
				$result['key'] = '8AAEC999-2228-4443-A5D8-DA6CEA12FE5E';
				break;
			case "экосол 65":
			case "4773a430-3391-4110-9492-61a7d767356e":
				$result['name'] = 'Экосол 65';
				$result['key'] = '4773A430-3391-4110-9492-61A7D767356E';
				break;
			case "thermagent industrial":
			case "e71fd73c-abb6-4214-9ab7-20dc4111fa9a":
				$result['name'] = 'Thermagent industrial';
				$result['key'] = 'E71FD73C-ABB6-4214-9AB7-20DC4111FA9A';
				break;
			case "аква терма":
			case "62c2b56b-84a6-40f7-88e8-caf5c44eeac2":
				$result['name'] = 'Аква Терма';
				$result['key'] = '62C2B56B-84A6-40F7-88E8-CAF5C44EEAC2';
				break;
		}
		return $result[$returnType];
	}

	//обработка назначения теплообменного оборудования
	public function prepareAim($type, $returnType = 'name')
	{
		switch (mb_strtolower($type)) {
			default:
			case "теплоснабжение":
			case "вентиляция":
			case "отопление":
			case "гвс одноступенчатая":
			case "другое (в примечании)":
			case "6d61cd2b-c08a-4a1d-821c-34e7a0f5ecfa":
				$result['name'] = 'Теплоснабжение';
				$result['key'] = '6d61cd2b-c08a-4a1d-821c-34e7a0f5ecfa';
				break;
			case "холодоснабжение":
			case "90762729-45d7-4821-a3cd-96730e14b657":
				$result['name'] = 'Холодоснабжение';
				$result['key'] = '90762729-45d7-4821-a3cd-96730e14b657';
				break;
			case "технология":
			case "технологические нужды":
			case "1f80af87-6c99-4310-a2e7-0bfeabcc1819":
				$result['name'] = 'Технология';
				$result['key'] = '1f80af87-6c99-4310-a2e7-0bfeabcc1819';
				break;
			case "гвс двухступенчатая":
			case "00000000-0000-0000-0000-000000000000":
				$result['name'] = 'ГВС двухступенчатая';
				$result['key'] = '00000000-0000-0000-0000-000000000000';
				break;
		}
		return $result[$returnType];
	}

	public function prepareFlowDirection($type, $returnType = 'name')
	{
		switch (mb_strtolower($type)) {
			default:
			case "0":
			case "6d61cd2b-c08a-4a1d-821c-34e7a0f5ecfa":
				$result['name'] = 'Противоток';
				$result['key'] = '0';
				break;
		}
		return $result[$returnType];
	}

	public function prepareFlange($type, $returnType = 'name')
	{
		switch (mb_strtolower($type)) {
			default:
			case "черные":
			case "4dfc02d7-1750-4bcf-bc25-5a3cd589aa1a":
				$result['name'] = 'Черные';
				$result['key'] = '4dfc02d7-1750-4bcf-bc25-5a3cd589aa1a';
				break;
			case "нержавеющие":
			case "75ba9806-272a-4a62-8f39-ba270116888b":
				$result['name'] = 'Нержавеющие';
				$result['key'] = '75ba9806-272a-4a62-8f39-ba270116888b';
				break;
		}
		return $result[$returnType];
	}

	public function prepareGMeasure($type, $returnType = 'name')
	{
		switch (mb_strtolower($type)) {
			default:
			case "т/ч":
			case "07db4c19-2b41-4ddf-bf95-a11fe7879abd":
				$result['name'] = 'т/ч';
				$result['key'] = '07db4c19-2b41-4ddf-bf95-a11fe7879abd';
				break;
			case "л/ч":
			case "560eeaad-d972-4114-893a-6285ddb7906c":
				$result['name'] = 'л/ч';
				$result['key'] = '560eeaad-d972-4114-893a-6285ddb7906c';
				break;
			case "кг/с":
			case "23dca111-a4c6-4df2-9d39-5d2c59721f38":
				$result['name'] = 'кг/с';
				$result['key'] = '23dca111-a4c6-4df2-9d39-5d2c59721f38';
				break;
			case "кг/ч":
			case "481a6bde-dc30-4b49-964c-59a89dc219c4":
				$result['name'] = 'кг/ч';
				$result['key'] = '481a6bde-dc30-4b49-964c-59a89dc219c4';
				break;
			case "м3/ч":
			case "5b7b08a0-1b05-4e1f-9326-2c6a25f215c4":
				$result['name'] = 'м3/ч';
				$result['key'] = '5b7b08a0-1b05-4e1f-9326-2c6a25f215c4';
				break;
		}
		return $result[$returnType];
	}

	public function prepareGasketMaterial($type, $returnType = 'name')
	{
		switch (mb_strtolower($type)) {
			default:
			case "epdm":
			case "c4955a47-eb63-43d8-b2b1-acd2e4024053":
				$result['name'] = 'EPDM';
				$result['key'] = 'c4955a47-eb63-43d8-b2b1-acd2e4024053';
				break;
			case "nitril":
			case "3826ace5-1361-4755-a539-805f94fd74b5":
				$result['name'] = 'NITRIL';
				$result['key'] = '3826ace5-1361-4755-a539-805f94fd74b5';
				break;
			case "epdm ht steam":
			case "c4ecc74a-b8b3-42f9-a729-11f499c30ee2":
				$result['name'] = 'EPDM HT STEAM';
				$result['key'] = 'c4ecc74a-b8b3-42f9-a729-11f499c30ee2';
				break;
		}
		return $result[$returnType];
	}

	public function prepareQMeasure($type, $returnType = 'name')
	{
		switch (mb_strtolower($type)) {
			default:
			case "квт":
			case "04399cad-00fb-4ce2-8de2-31bb946792dc":
				$result['name'] = 'кВт';
				$result['key'] = '04399cad-00fb-4ce2-8de2-31bb946792dc';
				break;
			case "мвт":
			case "273fc2c2-7683-4265-a8a6-400847fd8e0a":
				$result['name'] = 'МВт';
				$result['key'] = '273fc2c2-7683-4265-a8a6-400847fd8e0a';
				break;
			case "гкал/ч":
			case "648c0045-b97f-4a06-87ca-9af85e4aba07":
				$result['name'] = 'Гкал/ч';
				$result['key'] = '648c0045-b97f-4a06-87ca-9af85e4aba07';
				break;
			case "ккал/ч":
			case "91c829fa-6ae8-4315-91f3-02fe38d185dc":
				$result['name'] = 'ккал/ч';
				$result['key'] = '91c829fa-6ae8-4315-91f3-02fe38d185dc';
				break;
		}
		return $result[$returnType];
	}

	//собираем единиц измерений
	public function preparePressure($type, $returnType = 'name')
	{
		switch (mb_strtolower($type)) {
			default:
			case "16 атм":
			case "16":
				$result['name'] = '16';
				$result['key'] = '16';
				break;
			case "10 атм":
			case "10":
				$result['name'] = '10';
				$result['key'] = '10';
				break;
			case "6 атм":
			case "6":
				$result['name'] = '6';
				$result['key'] = '6';
				break;
		}
		return  $result[$returnType];
	}

	public function preparePlateMaterial($type, $returnType = 'name')
	{
		switch (mb_strtolower($type)) {
			default:
			case "aisi316":
			case "1acf494e-5860-452d-9779-3d2d4cd31c1b":
				$result['name'] = 'AISI316';
				$result['key'] = '1acf494e-5860-452d-9779-3d2d4cd31c1b';
				break;
			case "aisi304":
			case "82d59718-9612-4e2e-99e4-a6f41c047913":
				$result['name'] = 'AISI304';
				$result['key'] = '82d59718-9612-4e2e-99e4-a6f41c047913';
				break;
		}
		return $result[$returnType];
	}

	public function prepareRMeasure($type, $returnType = 'name')
	{
		switch (mb_strtolower($type)) {
			default:
			case "м.вод.ст.":
			case "2c54073f-2444-4f5b-b5eb-796185c7c2ee":
				$result['name'] = 'м.вод.ст.';
				$result['key'] = '2c54073f-2444-4f5b-b5eb-796185c7c2ee';
				break;
			case "кпа":
			case "0da74ffe-93e3-4cd8-a484-0b1c214e25ec":
				$result['name'] = 'кПа';
				$result['key'] = '0da74ffe-93e3-4cd8-a484-0b1c214e25ec';
				break;
			case "бар":
			case "1ee221c6-51ed-4352-ba21-7f5a8b7eee5c":
				$result['name'] = 'бар';
				$result['key'] = '1ee221c6-51ed-4352-ba21-7f5a8b7eee5c';
				break;
			case "кгс/см2":
			case "587c6b87-c5e6-488f-a69a-d8e84caf7eb2":
				$result['name'] = 'кгс/см2';
				$result['key'] = '587c6b87-c5e6-488f-a69a-d8e84caf7eb2';
				break;
		}
		return $result[$returnType];
	}

	public function preparePlugAssortment($type, $returnType = 'name')
	{
		switch (mb_strtolower($type)) {
			default:
			case "черные":
			case "00000000-0000-0000-0000-000000000000":
				$result['name'] = 'Черные';
				$result['key'] = '00000000-0000-0000-0000-000000000000';
				break;
			case "нержавеющие":
			case "b5068b97-1b3d-450b-8ea5-ffb99dd674c1":
				$result['name'] = 'Нержавеющие';
				$result['key'] = 'b5068b97-1b3d-450b-8ea5-ffb99dd674c1';
				break;
		}
		return $result[$returnType];
	}

	public function preparePlateThickIndex($type, $returnType = 'name')
	{
		switch (mb_strtolower($type)) {
			default:
			case "Авто":
			case "0":
				$result['name'] = 'Авто';
				$result['key'] = '0';
				break;
			case "0,3":
			case "1":
				$result['name'] = '0,3';
				$result['key'] = '1';
				break;
			case "0,4":
			case "2":
				$result['name'] = '0,4';
				$result['key'] = '2';
				break;
			case "0,5":
			case "3":
				$result['name'] = '0,5';
				$result['key'] = '3';
				break;
			case "0,6":
			case "4":
				$result['name'] = '0,6';
				$result['key'] = '4';
				break;
			case "0,7":
			case "5":
				$result['name'] = '0,7';
				$result['key'] = '5';
				break;
		}
		return $result[$returnType];
	}
}
