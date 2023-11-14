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

use \Catalog_v2\Model\Catalog;

trait CatalogTrait
{
		
	/**
	 * getDiameterArray
	 *
	 * @return void
	 */
	public function getCatalog($parameter): array
	{
        $r =  Catalog::getCatalog($parameter);
		return $this->diameter_dn;
	}	

    public function structureSpecification($i, $catalog, $count, $old = false)
	{
		global $db;
		if ($old) {
			return $this->structureSpecificationOld($i, $catalog, $count);
		}
		if (!empty($catalog)) {
			$catalog = $catalog[0];
			$sku = $catalog['sku'];
			$unit =  get_unit('data', $catalog['unit'], 'sname');
			$name = $catalog['title'];
			$model = ' ';
			$manufacter = '';
			if (!empty($catalog['man'])) {
				$query = $db->query("SELECT * FROM JMY_catalog_manufacture WHERE id = '" . $catalog['man'] . "' LIMIT 1");
				if ($db->numRows($query) > 0) {
					$manData = $db->getRow($query);
					$manufacter = $manData['name'];
				}
			}
			$spec_array = $catalog['spec'];
			$weight	 = $catalog['weight'];
			if (!empty($spec_array['1'])) {
				$name = $spec_array['1'];
			}
			if (!empty($spec_array['2'])) {
				$model = str_replace("&lt;br&gt;", "<w:br/>", $spec_array['2']);
			}
			if (!empty($spec_array['3'])) {
				$manufacter = $spec_array['3'];
			}
			$weight_good = '';
			if (!empty($weight['1'])) {
				$weight_good = $weight['1'];
			}
			$aid = 0;
			$attache = $catalog['attach_id'];
			if (!empty($attache)) {
				$aid = $attache[0];
			}
			$order_data_spec_tm =  array(
				'tm_cid'        	=> $catalog['id'],
				'tm_aid'        	=> $aid,
				'tm_n'        		=> $i,
				'tm_title' 			=> $name,
				'tm_model' 			=> $model,
				'tm_sku' 			=> $sku,
				'tm_manufacter' 	=> $manufacter,
				'tm_unit' 			=> $unit,
				'tm_count' 			=> $count,
				'tm_weight' 		=> $weight_good,
			);
			return $order_data_spec_tm;
		} else {
			// dd($i);
			return   array(
				'tm_cid'        	=> '',
				'tm_aid'        	=> '',
				'tm_n'        		=> $i,
				'tm_title' 			=> '',
				'tm_model' 			=> '',
				'tm_sku' 			=> '',
				'tm_manufacter' 	=> '',
				'tm_unit' 			=> '',
				'tm_count' 			=> '',
				'tm_weight' 		=> '',
			);
		}
	}
	
}
