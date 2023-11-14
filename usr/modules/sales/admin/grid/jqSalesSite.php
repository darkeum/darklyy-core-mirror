<?php

require_once(ROOT."/usr/modules/sales/admin/grid/jqSales.php");

class jqSalesSite extends jqSales
{
	protected $ss_where = "s.status_client = 2";
}