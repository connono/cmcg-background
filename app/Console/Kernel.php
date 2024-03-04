<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Notification; 
use App\Models\PaymentPlan;
use App\Models\PaymentProcess;
use App\Models\Contract;
use App\Models\EquipmentApplyRecord;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function (){
            $plans = PaymentPlan::where('status', 'wait')->whereNotNull('next_date')->whereYear('next_date', date('Y'))->whereMonth('next_date', date('m'))->get();
            foreach($plans as $plan) {
                $plan->update([
                    'status' => 'apply',
                ]);
                $notification = Notification::create([
                    'permission' => $plan->department,
                    'title' => $plan->contract_name,
                    'body' => json_encode($plan),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentPlan',
                    'type' => 'apply',
                    'link' => '/purchase/paymentMonitor/detail#apply&' . $plan->id . '&' . $plan->current_payment_record_id,
                ]);
                $plan->notification()->delete();
                $plan->notification()->save($notification);
            }
        })->everyMinute();
        $schedule->call(function (){
            $processes = PaymentProcess::where('status', 'wait')->whereNotNull('next_date')->whereYear('next_date', date('Y'))->whereMonth('next_date', date('m'))->get();
            foreach($processes as $process) {
                $process->update([
                    'status' => 'apply',
                ]);
                $contract = Contract::find($process->contract_id);
                $record = EquipmentApplyRecord::find($contract->equipment_apply_record_id);
                $recordJSON = json_encode($record, true);
                $record_array = json_decode($recordJSON, true);
                $processJSON = json_encode($process, true);
                $process_array = json_decode($processJSON, true);
                $information = (object) array_merge($record_array, $process_array);
                $notification = Notification::create([
                    'permission' => $process->department,
                    'title' => $process->contract_name,
                    'body' => json_encode($information, true),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentProcess',
                    'type' => 'apply',
                    'link' => '/purchase/paymentProcess/detail#apply&' . $process->id . '&' . $process->current_payment_record_id,
                ]);
                // $process->notification()->delete();
                $process->notification()->save($notification);
            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
