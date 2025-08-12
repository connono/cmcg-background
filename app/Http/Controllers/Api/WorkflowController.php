<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CamundaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WorkflowController extends Controller
{
    protected $camundaService;
    
    public function __construct(CamundaService $camundaService)
    {
        $this->camundaService = $camundaService;
    }
    
    /**
     * 获取所有流程定义
     */
    public function getDefinitions()
    {
        try {
            $definitions = $this->camundaService->getProcessDefinitions();
            return response()->json(['definitions' => $definitions]);
        } catch (\Exception $e) {
            Log::error('获取流程定义失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '获取流程定义失败: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 部署工作流
     */
    public function deploy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'xml' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // 保存BPMN文件到本地存储
            $filename = $request->name . '.bpmn';
            Storage::disk('local')->put('workflows/' . $filename, $request->xml);
            
            // 部署到Camunda
            $result = $this->camundaService->deployWorkflow($request->name, $request->xml);
            
            return response()->json([
                'success' => true,
                'deployment' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('部署工作流失败', [
                'name' => $request->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '部署工作流失败: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 获取特定工作流的XML
     */
    public function getWorkflow($name)
    {
        // 首先尝试从本地存储获取
        $path = 'workflows/' . $name . '.bpmn';
        if (Storage::disk('local')->exists($path)) {
            $xml = Storage::disk('local')->get($path);
            return response()->json([
                'success' => true,
                'xml' => $xml
            ]);
        }
        
        // 如果本地没有，尝试从Camunda获取
        try {
            $xml = $this->camundaService->getProcessDefinitionXml($name);
            
            // 如果成功获取，存储到本地
            if ($xml) {
                Storage::disk('local')->put($path, $xml);
            }
            
            return response()->json([
                'success' => true,
                'xml' => $xml
            ]);
        } catch (\Exception $e) {
            Log::error('获取工作流失败', [
                'name' => $name,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '获取工作流失败: ' . $e->getMessage()
            ], 404);
        }
    }
    
    /**
     * 启动流程实例
     */
    public function startProcess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'processKey' => 'required|string',
            'variables' => 'nullable|array',
            'businessKey' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $result = $this->camundaService->startProcess(
                $request->processKey,
                $request->variables ?? [],
                $request->businessKey
            );
            
            return response()->json([
                'success' => true,
                'instance' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('启动流程失败', [
                'processKey' => $request->processKey,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '启动流程失败: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 获取任务列表
     */
    public function getTasks(Request $request)
    {
        try {
            $assignee = $request->assignee;
            $processInstanceId = $request->processInstanceId;
            
            $tasks = $this->camundaService->getTasks($assignee, $processInstanceId);
            
            return response()->json([
                'success' => true,
                'tasks' => $tasks
            ]);
        } catch (\Exception $e) {
            Log::error('获取任务列表失败', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '获取任务列表失败: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 完成任务
     */
    public function completeTask(Request $request, $taskId)
    {
        $validator = Validator::make($request->all(), [
            'variables' => 'nullable|array'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $this->camundaService->completeTask(
                $taskId,
                $request->variables ?? []
            );
            
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::error('完成任务失败', [
                'taskId' => $taskId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '完成任务失败: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 获取流程实例列表
     */
    public function getProcessInstances(Request $request)
    {
        try {
            $businessKey = $request->businessKey;
            $processDefinitionKey = $request->processDefinitionKey;
            
            $instances = $this->camundaService->getProcessInstances(
                $businessKey, 
                $processDefinitionKey
            );
            
            return response()->json([
                'success' => true,
                'instances' => $instances
            ]);
        } catch (\Exception $e) {
            Log::error('获取流程实例列表失败', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '获取流程实例列表失败: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 获取流程历史
     */
    public function getProcessHistory($processInstanceId)
    {
        try {
            $response = $this->client->get('history/activity-instance', [
                'query' => [
                    'processInstanceId' => $processInstanceId,
                    'sortBy' => 'startTime',
                    'sortOrder' => 'asc'
                ]
            ]);
            
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            
            if ($statusCode >= 200 && $statusCode < 300) {
                $data = json_decode($body, true);
                return is_array($data) ? $data : [];
            } else {
                throw new \Exception('获取流程历史失败: HTTP ' . $statusCode . ' - ' . $body);
            }
        } catch (\Exception $e) {
            Log::error('获取流程历史失败', [
                'processInstanceId' => $processInstanceId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
