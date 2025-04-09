<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\AdvancePaymentRequest;
use App\Http\Resources\AdvancePaymentResource;
use App\Models\AdvancePayment;
use App\Services\AdvancePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdvancePaymentsController extends ApiController
{
    protected $advancePaymentService;

    public function __construct(AdvancePaymentService $advancePaymentService)
    {
        $this->advancePaymentService = $advancePaymentService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $advancePayments = $this->advancePaymentService->getAll($search,
        $sortField = null,$sortDirection = null,$perPage);
        return $this->successResponse(
            AdvancePaymentResource::collection($advancePayments),
            'Advance Payment retrieved successfully'
        );
    }

    public function store(AdvancePaymentRequest $request)
    {
        try {
            $advancePayment = $this->advancePaymentService->create($request->validated());

            $advancePayment->load(['farmer']);

            return $this->successResponse(
                new AdvancePaymentResource($advancePayment),
                'Advance Payment created successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'An error occurred while creating the advance payment: ' . $e->getMessage(),
                200
            );
        }
    }

    public function show($id)
    {
        try {
            $advancePayment = AdvancePayment::with(['farmer'])->findOrFail($id);
            
            if (!$advancePayment) {
                return $this->errorResponse('Advance Payment not found', 200);
            }

            return $this->successResponse(
                new AdvancePaymentResource($advancePayment),
                'Advance Payment retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving Advance Payment: ' . $e->getMessage(), 200);
        }
    }

    public function update(AdvancePaymentRequest $request, $id)
    {
        try {
            
            $advancePayment = AdvancePayment::findOrFail($id);
            
            if (empty($request->validated())) {
                return $this->errorResponse('No data provided for update', 200);
            }

            $advancePayment = $this->advancePaymentService->update($id, $request->validated());

            return $this->successResponse(
                new AdvancePaymentResource($advancePayment),
                'Advance Payment updated successfully'
            );

        } catch (\Exception $e){

            return $this->errorResponse('An error occurred while updating the advance payment: '.$e->getMessage(), 200);

        }
    }


    public function destroy($id)
    {
        try {
            $this->advancePaymentService->delete($id);
            return $this->successResponse(
                null,
                'Advance payment deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while deleting the advance payment', 200);
        }
    }

    public function filter(Request $request)
    {
        try {
            $filters = [
                'farmer_id' => $request->input('farmer_id'),
                'farmer_name' => $request->input('farmer_name'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'taken_by' => $request->input('taken_by'),
                'taken_by_name' => $request->input('taken_by_name'),
                'per_page' => $request->input('per_page', 10),
                'page' => $request->input('page', 1),
                'sort_field' => $request->input('sort_field', 'created_at'),
                'sort_direction' => $request->input('sort_direction', 'desc')
            ];

            $advancePayments = $this->advancePaymentService->filter($filters);

            return $this->successResponse(
                AdvancePaymentResource::collection($advancePayments),
                'Filtered Advance Payments retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error filtering Advance Payments: ' . $e->getMessage(), 200);
        }
    }

    public function downloadAdvancePaymentPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ap_id' => 'required|exists:advance_payments,id'
        ],
        [
            'ap_id.required' => 'Advance Payment is required.',
            'ap_id.exists' => 'Advance Payment is not exists.'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 200);
        }


        try {
            // Get PDF bytes from service
            $pdfContent = $this->advancePaymentService->downloadAdvancePaymentPDF($request->ap_id);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while downloading the advance payment: '.$e->getMessage(), 200);
        }
     
        $fileName = 'advance-payment-'.$request->ap_id.'-'. now()->format('Ymd-His') .'.pdf';
     
        // Stream PDF response
        return response()->stream(function () use ($pdfContent) {
            echo $pdfContent;
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Content-Length' => strlen($pdfContent),
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}