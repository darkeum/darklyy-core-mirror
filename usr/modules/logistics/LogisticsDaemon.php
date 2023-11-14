<?php

namespace Logistics;

class LogisticsDaemon{
    public function start(){
        \Logistics\Support\LogisticsDetailSupport::auto_add();
        \Logistics\Support\LogisticsDetailSupport::logistics_status();
    }
}