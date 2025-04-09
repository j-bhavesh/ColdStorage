<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $authKey;
    protected $authToken;
    protected $baseUrl;
    protected $defaultSenderId;
    protected $defaultNotifyUrl;
    protected $defaultNotifyMethod;

    public function __construct()
    {
        $this->authKey = config('services.sms.auth_key');
        $this->authToken = config('services.sms.auth_token');
        $this->defaultSenderId = config('services.sms.sender_id');
        $this->defaultNotifyUrl = config('services.sms.notify_url');
        $this->defaultNotifyMethod = config('services.sms.notify_method', 'POST');
        $this->baseUrl = 'https://restapi.smscountry.com/v0.1/Accounts/' . $this->authKey;

        // Validate configuration
        if (empty($this->authKey) || empty($this->authToken)) {
            Log::error('SMS Service configuration missing', [
                'auth_key_exists' => !empty($this->authKey),
                'auth_token_exists' => !empty($this->authToken),
                'sender_id' => $this->defaultSenderId
            ]);
            throw new \Exception('SMS Service configuration is incomplete. Please check your .env file.');
        }
    }

    /**
     * Generate Basic Auth header for SMS Country API
     *
     * @return string
     */
    protected function getAuthHeader(): string
    {
        $credentials = $this->authKey . ':' . $this->authToken;
        return 'Basic ' . base64_encode($credentials);
    }

    /**
     * Get list of available Sender IDs
     *
     * @return array Response containing list of Sender IDs
     */
    public function getSenderIds()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/SenderIDs/');

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info('Successfully retrieved Sender IDs', [
                    'response' => $responseData
                ]);

                // Extract just the SenderId values from the SenderIds array
                $senderIds = collect($responseData['SenderIds'] ?? [])
                    ->map(function ($item) {
                        return [
                            'id' => $item['SenderId'],
                            'expiry_date' => $item['ExpiryDate']
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'success' => true,
                    'sender_ids' => $senderIds,
                    'raw_response' => $responseData
                ];
            }

            Log::error('Failed to get Sender IDs', [
                'response' => $responseData
            ]);

            return [
                'success' => false,
                'message' => $responseData['Message'] ?? 'Failed to get Sender IDs',
                'raw_response' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('Exception while getting Sender IDs', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
                'raw_response' => null
            ];
        }
    }

    /**
     * Get list of available SMS Templates
     *
     * @return array Response containing list of Templates
     */
    public function getTemplates()
    {
        // dd([
        //     'authKey' => $this->authKey,
        //     'authToken' => $this->authToken,
        //     'defaultSenderId' => $this->defaultSenderId,
        //     'defaultNotifyUrl' => $this->defaultNotifyUrl,
        //     'baseUrl' => $this->baseUrl,
        // ]);
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/Templates/');

            $responseData = $response->json();
            // dd($responseData);
            if ($response->successful()) {
                Log::info('Successfully retrieved SMS Templates', [
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'templates' => $responseData['Templates'] ?? [],
                    'raw_response' => $responseData
                ];
            }

            Log::error('Failed to get SMS Templates', [
                'response' => $responseData
            ]);

            return [
                'success' => false,
                'message' => $responseData['Message'] ?? 'Failed to get SMS Templates',
                'raw_response' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('Exception while getting SMS Templates', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
                'raw_response' => null
            ];
        }
    }

    /**
     * Get template text by ID
     *
     * @param string $templateId
     * @return string|null Template text if found, null otherwise
     */
    public function getTemplateText($templateId)
    {
        $templates = $this->getTemplates();

        if (!$templates['success']) {
            return null;
        }

        foreach ($templates['templates'] as $template) {
            if ($template['TemplateId'] === $templateId) {
                return $template['Template'] ?? $template['Content'] ?? null;
            }
        }

        return null;
    }

    /**
     * Send SMS using approved template
     *
     * @param string $phone Phone number to send SMS to
     * @param string $templateId ID of the template to use
     * @param array $variables Variables to replace in template (if any)
     * @return array Response from SMS API
     */
    public function sendTemplateSms($phone, $templateId, $variables = [])
    {
        try {
            // Validate input parameters
            if (empty($phone)) {
                throw new \Exception('Phone number is required');
            }
            if (empty($templateId)) {
                throw new \Exception('Template ID is required');
            }

            // Get the template text
            $templateText = $this->getTemplateText($templateId);
            if (!$templateText) {
                throw new \Exception('Invalid template ID or template not found');
            }

            // Replace variables in template if any
            if (!empty($variables)) {
                // If variables is a sequential array, replace * in sequence
                if (is_array($variables)) {
                    $count = 0;
                    $templateText = preg_replace_callback('/\*/', function($matches) use ($variables, &$count) {
                        return isset($variables[$count]) ? $variables[$count++] : $matches[0];
                    }, $templateText);
                } else {
                    // If variables is an associative array, replace {key} with value
                    foreach ($variables as $key => $value) {
                        $templateText = str_replace('{' . $key . '}', $value, $templateText);
                    }
                }
            }

            $payload = [
                'Number' => $phone,
                'Text' => $templateText,
                'SenderId' => $this->defaultSenderId,
            ];

            if ($this->defaultNotifyUrl) {
                $payload['DRNotifyUrl'] = $this->defaultNotifyUrl;
                $payload['DRNotifyHttpMethod'] = $this->defaultNotifyMethod;
            }

            // Log the request payload
            Log::info('Sending SMS with template', [
                'phone' => $phone,
                'template_id' => $templateId,
                'template_text' => $templateText,
                'variables' => $variables,
                'sender_id' => $this->defaultSenderId
            ]);

            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/SMSes/', $payload);

            // Log the raw response
            Log::info('SMS API Raw Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            // Check if response is empty
            if (empty($response->body())) {
                Log::error('Empty response from SMS API', [
                    'status' => $response->status(),
                    'headers' => $response->headers()
                ]);
                return [
                    'success' => false,
                    'message' => 'Empty response from SMS API',
                    'error_code' => 'EMPTY_RESPONSE',
                    'raw_response' => null
                ];
            }

            // Check for HTML response (API error)
            if (strpos($response->body(), '<!DOCTYPE html>') !== false) {
                Log::error('API returned HTML error page', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [
                    'success' => false,
                    'message' => 'SMS API is currently unavailable. Please try again later.',
                    'error_code' => 'API_ERROR',
                    'raw_response' => null
                ];
            }

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'phone' => $phone,
                    'template_id' => $templateId,
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'raw_response' => $responseData
                ];
            }

            // Log detailed error information
            Log::error('Failed to send SMS', [
                'phone' => $phone,
                'template_id' => $templateId,
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'response_data' => $responseData,
                'request_payload' => $payload
            ]);

            return [
                'success' => false,
                'message' => $responseData['Message'] ?? 'Failed to send SMS',
                'error_code' => $responseData['ApiId'] ?? 'API_ERROR',
                'raw_response' => $responseData
            ];

        } catch (\Exception $e) {
            // Log the full exception details
            Log::error('Exception while sending SMS', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'phone' => $phone,
                'template_id' => $templateId,
                'variables' => $variables
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
                'error_code' => 'EXCEPTION',
                'raw_response' => null
            ];
        }
    }

    /**
     * Send bulk SMS using a template
     */
    public function sendBulkTemplateSms($phones, $templateId, $variables = [])
    {
        try {
            // Validate input parameters
            if (empty($phones) || !is_array($phones)) {
                throw new \Exception('Phone numbers array is required');
            }
            if (empty($templateId)) {
                throw new \Exception('Template ID is required');
            }

            // Log the request payload
            Log::info('Sending bulk template SMS request', [
                'phones' => $phones,
                'template_id' => $templateId,
                'variables' => $variables,
                'sender_id' => $this->defaultSenderId
            ]);

            $payload = [
                'Numbers' => $phones,
                'TemplateId' => $templateId,
                'Variables' => $variables,
                'SenderId' => $this->defaultSenderId,
            ];

            if ($this->defaultNotifyUrl) {
                $payload['NotifyUrl'] = $this->defaultNotifyUrl;
                $payload['NotifyMethod'] = $this->defaultNotifyMethod;
            }

            // Log the complete payload
            Log::info('Bulk SMS API Request Payload', [
                'payload' => $payload,
                'headers' => [
                    'Authorization' => 'Basic ' . substr($this->getAuthHeader(), 0, 10) . '...',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/BulkMessages/', $payload);

            // Log the raw response
            Log::info('Bulk SMS API Raw Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            // Check if response is empty
            if (empty($response->body())) {
                Log::error('Empty response from Bulk SMS API', [
                    'status' => $response->status(),
                    'headers' => $response->headers()
                ]);
                return [
                    'success' => false,
                    'message' => 'Empty response from Bulk SMS API',
                    'error_code' => 'EMPTY_RESPONSE',
                    'raw_response' => null
                ];
            }

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info('Bulk SMS sent successfully using template', [
                    'phones' => $phones,
                    'template_id' => $templateId,
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message' => 'Bulk SMS sent successfully',
                    'raw_response' => $responseData
                ];
            }

            // Log detailed error information
            Log::error('Failed to send bulk template SMS', [
                'phones' => $phones,
                'template_id' => $templateId,
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'response_data' => $responseData,
                'request_payload' => $payload
            ]);

            return [
                'success' => false,
                'message' => $responseData['Message'] ?? 'Failed to send bulk SMS',
                'error_code' => $responseData['ApiId'] ?? 'API_ERROR',
                'raw_response' => $responseData
            ];

        } catch (\Exception $e) {
            // Log the full exception details
            Log::error('Exception while sending bulk template SMS', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'phones' => $phones,
                'template_id' => $templateId,
                'variables' => $variables
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
                'error_code' => 'EXCEPTION',
                'raw_response' => null
            ];
        }
    }
}
