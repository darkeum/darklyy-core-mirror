<?php

class jqCols extends jqGrid
{
    protected function init()
    {
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'asc',
        );

        $this->query = "
			SELECT {fields}
			FROM JMY_catalog				
			WHERE {where}
		";

        #Set columns
        $this->cols = array(

            #Real id is hidden
            'id' => array('hidden' => false,
                'db' => 'id',
            ),
			  'title' => array('hidden' => false,
                'db' => 'title',
            ),

            
        );

        $this->render_filter_toolbar = true;
    }

    protected function parseRow($r)
    {
        #Calc diff_price in PHP
        $r['diff_price'] = $r['orig_price'] - $r['item_price'];

        #Highlight customers with discount > 0.1
        $r['_class'] = array('c_name' => ($r['discount'] > 0.1) ? 'bold font-green' : null);

        return $r;
    }
}