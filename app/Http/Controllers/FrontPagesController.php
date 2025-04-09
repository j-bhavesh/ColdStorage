<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\GetQuoteMail;

class FrontPagesController extends Controller
{
    public function privacy()
    {
        return view('frontend.privacy');
    }

    public function contact()
    {
        return view('frontend.contact');
    }

    public function submitQuote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'mobile_code' => 'required|digits:10',
            'comments' => 'required|string|max:500',
        ], [
            'mobile_code.digits' => 'Phone number must be exactly 10 digits.',
        ]);

        if ($validator->fails()) {
            // Return validation errors in JSON format (for AJAX)
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            Mail::to('bhavesh@raindropsinfotech.com')->send(new GetQuoteMail($request->all()));

            return response()->json([
                'status' => 'success',
                'message' => 'Quote submitted successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }
    }
}