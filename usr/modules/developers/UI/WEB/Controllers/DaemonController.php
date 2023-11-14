<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Developers\UI\WEB\Controllers;

use Decrees\Models\Decrees;
use Decrees\Tasks\TypesTask;
use Organizations\Model\Org;
use Boot\System\Http\Request;
use Developers\Models\Daemon;
use Decrees\UI\WEB\Grids\Index;
use Decrees\Actions\DocCreateAction;
use Developers\Support\DaemonProcess;

use Decrees\Actions\CreateDecreeAction;
use Boot\Abstracts\Controllers\WebController;
use Decrees\UI\WEB\Requests\DocCreateRequest;
use Decrees\UI\WEB\Requests\CreateDecreeRequest;


class DaemonController extends WebController
{
    static public function index()
    {
        return view('developers::manager.daemon');
    }

    static public function show($id, Request $request)
    {
        $item = Decrees::findOrFail($id);        
        return view('decrees::manager.show', [
            'id' => $id,
            'item' => $item,
            'type' => app(TypesTask::class)->run($item->type, "default")
        ]);
    }

    static public function create()
    {
        return view('decrees::manager.create', [
            'org' => Org::with('organization')->get(),
            'types' => app(TypesTask::class)->run(),
        ]);
    }

    static public function store(CreateDecreeRequest $request)
    {
        $decree = app(CreateDecreeAction::class)->run($request);
        return redirect()->route('admin.decrees.show', $decree->id);
    }
   
    public function startDaemon() {
		if (isset($_POST['id'])) {
			$daemonTable = Daemon::findOrFail($_POST['id']);
			$process = new DaemonProcess($daemonTable->command);
			$processId = $process->getPid();
			$daemonTable->update(['pid'=>$processId]);
			print_r($processId);
		}
	}

    public function stopDaemon($setId = false) {
		$id = $setId ? $_POST['removeId'] : $_POST['id'];			
		$process = new DaemonProcess();
		$result = Daemon::findOrFail(intval($id));
		$process->setPid($result->pid);
		$process->stop();
	}
    
}
