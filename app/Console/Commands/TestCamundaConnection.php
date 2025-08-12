<?php

namespace App\Console\Commands;

use App\Services\CamundaService;
use Illuminate\Console\Command;

class TestCamundaConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'camunda:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试Camunda连接';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CamundaService $camundaService)
    {
        $this->info('正在测试Camunda连接...');
        
        try {
            $definitions = $camundaService->getProcessDefinitions();
            
            if ($definitions === null) {
                $this->info('连接成功! 但未找到流程定义或返回格式不正确。');
                return Command::SUCCESS;
            }
            
            $this->info('连接成功! 找到 ' . count($definitions) . ' 个流程定义.');
            
            if (!empty($definitions)) {
                foreach ($definitions as $index => $definition) {
                    $this->line(($index + 1) . '. ' . $definition['name'] . ' (Key: ' . $definition['key'] . ')');
                }
            } else {
                $this->line('没有找到流程定义。');
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('连接失败: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
