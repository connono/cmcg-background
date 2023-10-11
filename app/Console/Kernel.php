<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Notification; 
use App\Models\PaymentPlan;

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
                    'link' => '/paymentMonitor/detail#apply&' . $plan->id . '&' . $plan->current_payment_record_id,
                ]);
                $plan->notification()->delete();
                $plan->notification()->save($notification);
            }
        })->daily();
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
