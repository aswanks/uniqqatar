<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Errorlog;  // Make sure this matches your model's namespace


class ErrorLogController extends Controller
{
    public function index()
    {
        $errorLogs = ErrorLog::all();
        return view('admin.error_logs', compact('errorLogs'));
    }

    public function store(Request $request)
{
   // \Log::info('Request data', ['data' => $request->all()]);
        \Log::info('Store method called');

    $validator = Validator::make($request->all(), [
        'user_id' => 'nullable|integer',
        'error_message' => 'required|string',
        'stack_trace' => 'nullable|string',
        'device_info' => 'nullable|string',
        'os_version' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed', ['errors' => $validator->errors()]);
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        $data = $request->all();

        // Optionally cast data if needed
        $data['user_id'] = (int) $data['user_id'];
        $data['error_message'] = (string) $data['error_message'];

        $errorLog = Errorlog::create($data);
        \Log::info('Error log stored', ['errorLog' => $errorLog]);
        return response()->json(['message' => 'Error logged successfully'], 201);
    } catch (\Exception $e) {
        \Log::error('Error storing log', ['exception' => $e->getMessage()]);
        return response()->json(['message' => 'Failed to log error'], 500);
    }
}


}
