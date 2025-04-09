<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SmsTestController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index()
    {
        $templates = $this->smsService->getTemplates();
        return view('admin.sms-test', compact('templates'));
    }

    public function getSenderIds()
    {
        try {
            $result = $this->smsService->getSenderIds();
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error getting sender IDs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTemplates()
    {
        try {
            $result = $this->smsService->getTemplates();
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error getting templates', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error getting templates: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testSingleSms(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string|regex:/^[0-9]{10}$/',
                'template_id' => 'required|string',
            ], [
                'phone.required' => 'Phone number is required',
                'phone.regex' => 'Phone number must be 10 digits',
                'template_id.required' => 'Template ID is required',

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $result = $this->smsService->sendTemplateSms(
                $request->phone,
                $request->template_id,
                $request->variables
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error sending single SMS', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error sending SMS: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testBulkSms(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phones' => 'required|array',
                'phones.*' => 'required|string',
                'template_id' => 'required|string',
                'variables' => 'required|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $result = $this->smsService->sendBulkTemplateSms(
                $request->phones,
                $request->template_id,
                $request->variables
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error sending bulk SMS', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error sending bulk SMS: ' . $e->getMessage()
            ], 500);
        }
    }
}
