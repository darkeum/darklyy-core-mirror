<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2021 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */

namespace Project\Traits\Calculation;

trait DiameterTrait
{
	/**
	 * diameter_dn
	 *
	 * @var array
	 */
	private $diameter_dn = [15, 20, 25, 32, 40, 50, 65, 80, 100, 125, 150, 200, 250, 300];	
	
	/**
	 * diameter_dn_n
	 *
	 * @var array
	 */
	private $diameter_dn_n = [
		'15' => '21.3',
		'20' => '25',
		'25' => '32',
		'32' => '38',
		'40' => '45',
		'50' => '57',
		'65' => '76',
		'80' => '89',
		'100' => '108',
		'125' => '133',
		'150' => '159',
		'200' => '219',
		'250' => '273',
		'300' => '325'
	];
	
	/**
	 * getDiameterArray
	 *
	 * @return void
	 */
	public function getDiameterArray(): array
	{
		return $this->diameter_dn;
	}
	
	/**
	 * getDiameterOutsideArray
	 *
	 * @return void
	 */
	public function getDiameterOutsideArray(): array
	{
		return $this->diameter_dn_n;
	}
	
	/**
	 * getDiameter
	 *
	 * @return void
	 */
	public function getDiameter($dn)
	{
	}
	
	/**
	 * getDiameterOutside
	 *
	 * @return void
	 */
	public function getDiameterOutside()
	{
	}
}
