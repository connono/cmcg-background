<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CamundaService
{
    protected $client;
    
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://10.10.0.27:9999/engine-rest/',
            'timeout' => 15,
            'http_errors' => false
        ]);
    }
    
    /**
     * 获取流程定义列表
     */
    public function getProcessDefinitions()
    {
        try {
            $response = $this->client->get('process-definition');
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            
            // 确保返回数组
            $data = json_decode($body, true);
            return is_array($data) ? $data : [];
        } catch (\Exception $e) {
            Log::error('获取流程定义失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * 部署工作流
     */
    public function deployWorkflow($name, $bpmnXml)
    {
        try {
            $response = $this->client->post('deployment/create', [
                'multipart' => [
                    [
                        'name' => 'deployment-name',
                        'contents' => $name
                    ],
                    [
                        'name' => $name.'.bpmn',
                        'contents' => $bpmnXml,
                        'filename' => $name.'.bpmn'
                    ]
                ]
            ]);
            
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            
            if ($statusCode >= 200 && $statusCode < 300) {
                return json_decode($body, true);
            } else {
                throw new \Exception('部署失败: HTTP ' . $statusCode . ' - ' . $body);
            }
        } catch (\Exception $e) {
            Log::error('部署工作流失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * 获取特定流程定义的XML
     */
    public function getProcessDefinitionXml($processKey)
    {
        try {
            // 先获取最新版本的流程定义ID
            $response = $this->client->get('process-definition/key/' . $processKey);
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            
            if ($statusCode >= 200 && $statusCode < 300) {
                $processDefinition = json_decode($body, true);
                $processDefinitionId = $processDefinition['id'];
                
                // 使用ID获取XML
                $xmlResponse = $this->client->get('process-definition/' . $processDefinitionId . '/xml');
                $xmlStatusCode = $xmlResponse->getStatusCode();
                $xmlBody = (string) $xmlResponse->getBody();
                
                if ($xmlStatusCode >= 200 && $xmlStatusCode < 300) {
                    $xmlData = json_decode($xmlBody, true);
                    return $xmlData['bpmn20Xml'] ?? null;
                }
            }
            
            throw new \Exception('获取流程定义XML失败: HTTP ' . $statusCode . ' - ' . $body);
        } catch (\Exception $e) {
            Log::error('获取流程定义XML失败', [
                'processKey' => $processKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * 启动流程实例
     */
    public function startProcess($processKey, $variables = [], $businessKey = null)
    {
        try {
            $payload = [
                'variables' => $this->formatVariables($variables)
            ];
            
            if ($businessKey) {
                $payload['businessKey'] = $businessKey;
            }
            
            $response = $this->client->post('process-definition/key/' . $processKey . '/start', [
                'json' => $payload
            ]);
            
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            
            if ($statusCode >= 200 && $statusCode < 300) {
                return json_decode($body, true);
            } else {
                throw new \Exception('启动流程失败: HTTP ' . $statusCode . ' - ' . $body);
            }
        } catch (\Exception $e) {
            Log::error('启动流程失败', [
                'processKey' => $processKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * 获取任务列表
     */
    public function getTasks($assignee = null, $processInstanceId = null)
    {
        try {
            $query = [];
            
            if ($assignee) {
                $query['assignee'] = $assignee;
            }
            
            if ($processInstanceId) {
                $query['processInstanceId'] = $processInstanceId;
            }
            
            $response = $this->client->get('task', [
                'query' => $query
            ]);
            
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            
            if ($statusCode >= 200 && $statusCode < 300) {
                $data = json_decode($body, true);
                return is_array($data) ? $data : [];
            } else {
                throw new \Exception('获取任务列表失败: HTTP ' . $statusCode . ' - ' . $body);
            }
        } catch (\Exception $e) {
            Log::error('获取任务列表失败', [
                'assignee' => $assignee,
                'processInstanceId' => $processInstanceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * 完成任务
     */
    public function completeTask($taskId, $variables = [])
    {
        try {
            $response = $this->client->post('task/' . $taskId . '/complete', [
                'json' => [
                    'variables' => $this->formatVariables($variables)
                ]
            ]);
            
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            
            if ($statusCode >= 200 && $statusCode < 300) {
                return true;
            } else {
                throw new \Exception('完成任务失败: HTTP ' . $statusCode . ' - ' . $body);
            }
        } catch (\Exception $e) {
            Log::error('完成任务失败', [
                'taskId' => $taskId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * 获取流程实例列表
     */
    public function getProcessInstances($businessKey = null, $processDefinitionKey = null)
    {
        try {
            $query = [];
            
            if ($businessKey) {
                $query['businessKey'] = $businessKey;
            }
            
            if ($processDefinitionKey) {
                $query['processDefinitionKey'] = $processDefinitionKey;
            }
            
            $response = $this->client->get('process-instance', [
                'query' => $query
            ]);
            
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            
            if ($statusCode >= 200 && $statusCode < 300) {
                $data = json_decode($body, true);
                return is_array($data) ? $data : [];
            } else {
                throw new \Exception('获取流程实例列表失败: HTTP ' . $statusCode . ' - ' . $body);
            }
        } catch (\Exception $e) {
            Log::error('获取流程实例列表失败', [
                'businessKey' => $businessKey,
                'processDefinitionKey' => $processDefinitionKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * 格式化变量为Camunda格式
     */
    private function formatVariables($variables)
    {
        $formatted = [];
        
        foreach ($variables as $key => $value) {
            $type = gettype($value);
            
            switch ($type) {
                case 'boolean':
                    $formatted[$key] = ['value' => $value, 'type' => 'Boolean'];
                    break;
                case 'integer':
                    $formatted[$key] = ['value' => $value, 'type' => 'Integer'];
                    break;
                case 'double':
                    $formatted[$key] = ['value' => $value, 'type' => 'Double'];
                    break;
                case 'array':
                case 'object':
                    $formatted[$key] = [
                        'value' => json_encode($value),
                        'type' => 'Json'
                    ];
                    break;
                default:
                    $formatted[$key] = ['value' => $value, 'type' => 'String'];
            }
        }
        
        return $formatted;
    }

    public function testConnection()
    {
        $endpoints = [
            'engine',
            'process-definition',
            'deployment'
        ];
        
        $results = [];
        
        foreach ($endpoints as $endpoint) {
            try {
                $response = $this->client->get($endpoint);
                $statusCode = $response->getStatusCode();
                $body = (string) $response->getBody();
                
                $results[$endpoint] = [
                    'success' => $statusCode >= 200 && $statusCode < 300,
                    'status' => $statusCode,
                    'body' => substr($body, 0, 200) // 只取前200个字符以免输出过多
                ];
            } catch (\Exception $e) {
                $results[$endpoint] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
}
