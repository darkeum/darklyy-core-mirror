<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Console\Scheduling;

use Illuminate\Support\Carbon;
use InvalidArgumentException;

trait ManagesFrequencies
{
    /**
     * Выражение Cron, представляющее частоту события.
     *
     * @param  string  $expression
     * @return $this
     */
    public function cron($expression)
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * Запланируйте событие, чтобы запустить между началом и окончанием.
     *
     * @param  string  $startTime
     * @param  string  $endTime
     * @return $this
     */
    public function between($startTime, $endTime)
    {
        return $this->when($this->inTimeInterval($startTime, $endTime));
    }

    /**
     * Запланируйте событие, чтобы не работать между началом и окончанием.
     *
     * @param  string  $startTime
     * @param  string  $endTime
     * @return $this
     */
    public function unlessBetween($startTime, $endTime)
    {
        return $this->skip($this->inTimeInterval($startTime, $endTime));
    }

    /**
     * Запланируйте событие, чтобы запустить между началом и окончанием.
     *
     * @param  string  $startTime
     * @param  string  $endTime
     * @return \Closure
     */
    private function inTimeInterval($startTime, $endTime)
    {
        [$now, $startTime, $endTime] = [
            Carbon::now($this->timezone),
            Carbon::parse($startTime, $this->timezone),
            Carbon::parse($endTime, $this->timezone),
        ];

        if ($endTime->lessThan($startTime)) {
            if ($startTime->greaterThan($now)) {
                $startTime = $startTime->subDay(1);
            } else {
                $endTime = $endTime->addDay(1);
            }
        }

        return fn () => $now->between($startTime, $endTime);
    }

    /**
     * Запланируйте мероприятие, чтобы запустить каждую секунду.
     *
     * @return $this
     */
    public function everySecond()
    {
        return $this->repeatEvery(1);
    }

    /**
     * Запланируйте мероприятие, чтобы запустить каждые две секунды.
     *
     * @return $this
     */
    public function everyTwoSeconds()
    {
        return $this->repeatEvery(2);
    }

    /**
     * Запланируйте мероприятие, чтобы работать каждые пять секунд.
     *
     * @return $this
     */
    public function everyFiveSeconds()
    {
        return $this->repeatEvery(5);
    }

    /**
     * Запланируйте мероприятие, чтобы запустить каждые десять секунд.
     *
     * @return $this
     */
    public function everyTenSeconds()
    {
        return $this->repeatEvery(10);
    }

    /**
     * Запланируйте мероприятие, чтобы пробежать каждые пятнадцать секунд.
     *
     * @return $this
     */
    public function everyFifteenSeconds()
    {
        return $this->repeatEvery(15);
    }

    /**
     * Запланируйте мероприятие, чтобы пробежать каждые двадцать секунд.
     *
     * @return $this
     */
    public function everyTwentySeconds()
    {
        return $this->repeatEvery(20);
    }

    /**
     * Запланируйте мероприятие, чтобы запустить каждые тридцать секунд.
     *
     * @return $this
     */
    public function everyThirtySeconds()
    {
        return $this->repeatEvery(30);
    }

    /**
     * Запланируйте мероприятие, чтобы работать несколько раз в минуту.
     *
     * @param  int<0, 59>  $seconds
     * @return $this
     */
    protected function repeatEvery($seconds)
    {
        if (60 % $seconds !== 0) {
            throw new InvalidArgumentException("The seconds [$seconds] are not evenly divisible by 60.");
        }

        $this->repeatSeconds = $seconds;

        return $this->everyMinute();
    }

    /**
     * Запланируйте мероприятие, чтобы запустить каждую минуту.
     *
     * @return $this
     */
    public function everyMinute()
    {
        return $this->spliceIntoPosition(1, '*');
    }

    /**
     * Запланируйте мероприятие, чтобы работать каждые две минуты.
     *
     * @return $this
     */
    public function everyTwoMinutes()
    {
        return $this->spliceIntoPosition(1, '*/2');
    }

    /**
     * Запланируйте мероприятие, чтобы работать каждые три минуты.
     *
     * @return $this
     */
    public function everyThreeMinutes()
    {
        return $this->spliceIntoPosition(1, '*/3');
    }

    /**
     * Запланируйте мероприятие, чтобы работать каждые четыре минуты.
     *
     * @return $this
     */
    public function everyFourMinutes()
    {
        return $this->spliceIntoPosition(1, '*/4');
    }

    /**
     * Запланируйте мероприятие, чтобы работать каждые пять минут.
     *
     * @return $this
     */
    public function everyFiveMinutes()
    {
        return $this->spliceIntoPosition(1, '*/5');
    }

    /**
     * Запланируйте мероприятие, чтобы пробежать каждые десять минут.
     *
     * @return $this
     */
    public function everyTenMinutes()
    {
        return $this->spliceIntoPosition(1, '*/10');
    }

    /**
     * Запланируйте мероприятие, чтобы пробежать каждые пятнадцать минут.
     *
     * @return $this
     */
    public function everyFifteenMinutes()
    {
        return $this->spliceIntoPosition(1, '*/15');
    }

    /**
     * Запланируйте мероприятие, чтобы запустить каждые тридцать минут.
     *
     * @return $this
     */
    public function everyThirtyMinutes()
    {
        return $this->spliceIntoPosition(1, '*/30');
    }

    /**
     * Запланируйте мероприятие, чтобы работать ежечасно.
     *
     * @return $this
     */
    public function hourly()
    {
        return $this->spliceIntoPosition(1, 0);
    }

    /**
     * Запланируйте мероприятие, чтобы работать ежечасно при заданном смещении в час.
     *
     * @param  array|string|int<0, 59>|int<0, 59>[]  $offset
     * @return $this
     */
    public function hourlyAt($offset)
    {
        return $this->hourBasedSchedule($offset, '*');
    }

    /**
     * Запланируйте мероприятие, чтобы запустить каждый нечетный час.
     *
     * @param  array|string|int  $offset
     * @return $this
     */
    public function everyOddHour($offset = 0)
    {
        return $this->hourBasedSchedule($offset, '1-23/2');
    }

    /**
     * Запланируйте мероприятие, чтобы работать каждые два часа.
     *
     * @param  array|string|int  $offset
     * @return $this
     */
    public function everyTwoHours($offset = 0)
    {
        return $this->hourBasedSchedule($offset, '*/2');
    }

    /**
     * Запланируйте мероприятие, чтобы работать каждые три часа.
     *
     * @param  array|string|int  $offset
     * @return $this
     */
    public function everyThreeHours($offset = 0)
    {
        return $this->hourBasedSchedule($offset, '*/3');
    }

    /**
     * Запланируйте мероприятие, чтобы работать каждые четыре часа.
     *
     * @param  array|string|int  $offset
     * @return $this
     */
    public function everyFourHours($offset = 0)
    {
        return $this->hourBasedSchedule($offset, '*/4');
    }

    /**
     * Запланируйте мероприятие, чтобы работать каждые шесть часов.
     *
     * @param  array|string|int  $offset
     * @return $this
     */
    public function everySixHours($offset = 0)
    {
        return $this->hourBasedSchedule($offset, '*/6');
    }

    /**
     * Запланируйте мероприятие ежедневно.
     *
     * @return $this
     */
    public function daily()
    {
        return $this->hourBasedSchedule(0, 0);
    }

    /**
     * Запланируйте команду на данный момент времени.
     *
     * @param  string  $time
     * @return $this
     */
    public function at($time)
    {
        return $this->dailyAt($time);
    }

    /**
     * Запланируйте мероприятие ежедневно в данный момент времени (10:00, 19:30 и т. Д.).
     *
     * @param  string  $time
     * @return $this
     */
    public function dailyAt($time)
    {
        $segments = explode(':', $time);

        return $this->hourBasedSchedule(
            count($segments) === 2 ? (int) $segments[1] : '0',
            (int) $segments[0]
        );
    }

    /**
     * Запланируйте мероприятие, чтобы работать два раза в день.
     *
     * @param  int<0, 23>  $first
     * @param  int<0, 23>  $second
     * @return $this
     */
    public function twiceDaily($first = 1, $second = 13)
    {
        return $this->twiceDailyAt($first, $second, 0);
    }

    /**
     * Запланируйте мероприятие, чтобы запустить два раза в день в данном смещении.
     *
     * @param  int<0, 23>  $first
     * @param  int<0, 23>  $second
     * @param  int<0, 59>  $offset
     * @return $this
     */
    public function twiceDailyAt($first = 1, $second = 13, $offset = 0)
    {
        $hours = $first.','.$second;

        return $this->hourBasedSchedule($offset, $hours);
    }

    /**
     * Запланируйте мероприятие, чтобы запустить в заданные минуты и часы.
     *
     * @param  array|string|int<0, 59>  $minutes
     * @param  array|string|int<0, 23>  $hours
     * @return $this
     */
    protected function hourBasedSchedule($minutes, $hours)
    {
        $minutes = is_array($minutes) ? implode(',', $minutes) : $minutes;

        $hours = is_array($hours) ? implode(',', $hours) : $hours;

        return $this->spliceIntoPosition(1, $minutes)
            ->spliceIntoPosition(2, $hours);
    }

    /**
     * Запланируйте мероприятие, чтобы работать только в будние дни.
     *
     * @return $this
     */
    public function weekdays()
    {
        return $this->days(Schedule::MONDAY.'-'.Schedule::FRIDAY);
    }

    /**
     * Запланируйте мероприятие, чтобы работать только по выходным.
     *
     * @return $this
     */
    public function weekends()
    {
        return $this->days(Schedule::SATURDAY.','.Schedule::SUNDAY);
    }

    /**
     * Запланируйте мероприятие, чтобы работать только по понедельникам.
     *
     * @return $this
     */
    public function mondays()
    {
        return $this->days(Schedule::MONDAY);
    }

    /**
     * Запланируйте мероприятие, чтобы работать только по вторникам.
     *
     * @return $this
     */
    public function tuesdays()
    {
        return $this->days(Schedule::TUESDAY);
    }

    /**
     * Запланируйте мероприятие, чтобы работать только по средам.
     *
     * @return $this
     */
    public function wednesdays()
    {
        return $this->days(Schedule::WEDNESDAY);
    }

    /**
     * Запланируйте мероприятие, чтобы работать только по четвергам.
     *
     * @return $this
     */
    public function thursdays()
    {
        return $this->days(Schedule::THURSDAY);
    }

    /**
     * Запланируйте мероприятие, чтобы работать только по пятницам.
     *
     * @return $this
     */
    public function fridays()
    {
        return $this->days(Schedule::FRIDAY);
    }

    /**
     * Запланируйте мероприятие, чтобы работать только по субботам.
     *
     * @return $this
     */
    public function saturdays()
    {
        return $this->days(Schedule::SATURDAY);
    }

    /**
     * Запланируйте мероприятие, чтобы работать только по воскресеньям.
     *
     * @return $this
     */
    public function sundays()
    {
        return $this->days(Schedule::SUNDAY);
    }

    /**
     * Запланируйте мероприятие, чтобы работать еженедельно.
     *
     * @return $this
     */
    public function weekly()
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(5, 0);
    }

    /**
     * Запланируйте мероприятие, чтобы работать еженедельно в данный день и время.
     *
     * @param  array|mixed  $dayOfWeek
     * @param  string  $time
     * @return $this
     */
    public function weeklyOn($dayOfWeek, $time = '0:0')
    {
        $this->dailyAt($time);

        return $this->days($dayOfWeek);
    }

    /**
     * Запланируйте мероприятие, чтобы запустить ежемесячно.
     *
     * @return $this
     */
    public function monthly()
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1);
    }

    /**
     * Запланируйте мероприятие, чтобы пробежать ежемесячно в данный день и время.
     *
     * @param  int<1, 31>  $dayOfMonth
     * @param  string  $time
     * @return $this
     */
    public function monthlyOn($dayOfMonth = 1, $time = '0:0')
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $dayOfMonth);
    }

    /**
     * Запланируйте мероприятие, чтобы пробежать дважды в месяц в данный момент.
     *
     * @param  int<1, 31>  $first
     * @param  int<1, 31>  $second
     * @param  string  $time
     * @return $this
     */
    public function twiceMonthly($first = 1, $second = 16, $time = '0:0')
    {
        $daysOfMonth = $first.','.$second;

        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $daysOfMonth);
    }

    /**
     * Запланируйте мероприятие, чтобы пробежать в последний день месяца.
     *
     * @param  string  $time
     * @return $this
     */
    public function lastDayOfMonth($time = '0:0')
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, Carbon::now()->endOfMonth()->day);
    }

    /**
     * Запланируйте мероприятие, чтобы запустить ежеквартально.
     *
     * @return $this
     */
    public function quarterly()
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1)
            ->spliceIntoPosition(4, '1-12/3');
    }

    /**
     * Запланируйте мероприятие, чтобы пробежать ежеквартально в данный день и время.
     *
     * @param  int  $dayOfQuarter
     * @param  string  $time
     * @return $this
     */
    public function quarterlyOn($dayOfQuarter = 1, $time = '0:0')
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $dayOfQuarter)
            ->spliceIntoPosition(4, '1-12/3');
    }

    /**
     * Запланируйте мероприятие, чтобы работать ежегодно.
     *
     * @return $this
     */
    public function yearly()
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1)
            ->spliceIntoPosition(4, 1);
    }

    /**
     * Запланируйте мероприятие, которое будет проводиться ежегодно в определенный месяц, день и время.
     *
     * @param  int  $month
     * @param  int<1, 31>|string  $dayOfMonth
     * @param  string  $time
     * @return $this
     */
    public function yearlyOn($month = 1, $dayOfMonth = 1, $time = '0:0')
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $dayOfMonth)
            ->spliceIntoPosition(4, $month);
    }

    /**
     * Установите дни недели, на которые должна работать команда.
     *
     * @param  array|mixed  $days
     * @return $this
     */
    public function days($days)
    {
        $days = is_array($days) ? $days : func_get_args();

        return $this->spliceIntoPosition(5, implode(',', $days));
    }

    /**
     * Установите часовой пояс. Дата должна быть оценена.
     *
     * @param  \DateTimeZone|string  $timezone
     * @return $this
     */
    public function timezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Переплесьте заданное значение в заданную позицию выражения.
     *
     * @param  int  $position
     * @param  string  $value
     * @return $this
     */
    protected function spliceIntoPosition($position, $value)
    {
        $segments = preg_split("/\s+/", $this->expression);

        $segments[$position - 1] = $value;

        return $this->cron(implode(' ', $segments));
    }
}
