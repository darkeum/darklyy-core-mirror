<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Home\UI\WEB\Components;

use Boot\Models\Users;
use Boot\Support\Carbon;
use Task\Support\TaskSupport;
use Illuminate\Support\Facades\DB;
use Boot\System\Livewire\Component;

class Calendar extends Component
{
    public $hour;
    public $users;
    public $usersCount;
    public $taskTodayCount;
    public $holiday;
    public $holiday_marker;
    public $birthday;

    public function mount()
    {
        $query = Users::select(DB::raw('*, birthday + INTERVAL (YEAR(CURRENT_DATE) - YEAR(birthday)) YEAR AS currBirthday,
        birthday + INTERVAL (YEAR(CURRENT_DATE) - YEAR(birthday)) + 1 YEAR AS nextBirthday'))->where('group', 1)->where('active', 1)->orderByRaw('ISNULL(birthday), CASE
        WHEN currBirthday >= CURRENT_DATE THEN currBirthday ELSE nextBirthday END');
        $this->usersCount = $query->count();
        $this->users = $query->limit(5)->get();
        $this->hour =  Carbon::now()->isoFormat('k');
        $this->taskTodayCount = $this->taskTodayCount = TaskSupport::getTaskTodayCount();
        $this->holiday = null;
        $birthday = Users::where('birthday', 'like', '%' . Carbon::now()->format('m-d'))->where('group', 1)->where('active', 1)->get();
        $holiday_array = [
            '01.01' => __('home::calendar.holiday.new.year'),
            '01.07' => __('home::calendar.holiday.christmas'),
            '01.14' => __('home::calendar.holiday.old.year'),
            '02.14' => __('home::calendar.holiday.valentine'),
            '02.23' => __('home::calendar.holiday.defender'),
            '03.08' => __('home::calendar.holiday.woman'),
            '04.01' => __('home::calendar.holiday.stupid'),
            '05.01' => __('home::calendar.holiday.work'),
            '05.09' => __('home::calendar.holiday.victory'),
            '06.12' => __('home::calendar.holiday.russia'),
            Carbon::parse('second sunday of August')->format('m.d') => __('home::calendar.holiday.build'),
            '09.01' => __('home::calendar.holiday.knowledge'),
            '10.16' => __('home::calendar.holiday.father'),
            '11.04' => __('home::calendar.holiday.unity'),
            '11.27' => __('home::calendar.holiday.mother'),
        ];
        $this->holiday_marker = 0;
        $this->birthday = null;
        if (array_key_exists(Carbon::now()->format('m.d'), $holiday_array)) {
            $this->holiday = __('home::calendar.today', ['event' => $holiday_array[Carbon::now()->format('m.d')]]);
            if ($birthday->count() != 0) {
                $this->holiday_marker = 3;
            } else {
                $this->holiday_marker = 1;
            }
        } else {
            if ($birthday->count() != 0) {
                $this->holiday_marker = 2;
            } else {
                $this->holiday_marker = 0;
            }
        }
        if ($birthday->count() != 0) {
            $this->birthday = $birthday;
            if (!empty($this->holiday)) {
                $this->holiday = __('home::calendar.today.more', ['event' => __('home::calendar.today.birthday')]);
            } else {
                $this->holiday = __('home::calendar.today', ['event' => __('home::calendar.today.birthday')]);
            }
            foreach ($birthday as $key => $value) {
                $this->holiday .= ' ' . $value->surname . ' ' . $value->name;
            }
        }
    }

    public function render()
    {
        return view('home::manager.components.calendar');
    }
}
