<?php

// Tatiana handles inquiry data here.

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        // Defensive aliasing: accept 'name' as 'customer_name' and 'property' as 'property_id'
        if (!$request->filled('customer_name') && $request->filled('name')) {
            $request->merge(['customer_name' => $request->name]);
        }
        if (!$request->filled('property_id') && $request->filled('property')) {
            $request->merge(['property_id' => $request->property]);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'property_id' => 'nullable|exists:properties,id',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'source' => 'nullable|string|max:255',
        ]);

        if (empty($validated['subject'])) {
            if (!empty($validated['property_id'])) {
                $prop = Property::find($validated['property_id']);
                $validated['subject'] = $prop ? ('Inquiry for ' . $prop->name) : 'Property Inquiry';
            } else {
                $validated['subject'] = 'General Inquiry';
            }
        }

        $validated['status'] = 'new';
        if (empty($validated['source'])) {
            $validated['source'] = !empty($validated['property_id']) ? 'Property Detail Page' : 'Contact Us Form';
        }

        try {
            $inquiry = DB::transaction(function () use ($validated) {
                $inquiry = Inquiry::create($validated);

                \App\Models\InquiryStatusLog::create([
                    'inquiry_id' => $inquiry->id,
                    'status' => 'new',
                    'changed_at' => now(),
                ]);

                return $inquiry;
            });
        } catch (\Throwable $e) {
            Log::error('Inquiry store failed: ' . $e->getMessage(), ['exception' => $e]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to send message at this moment. Please try again.',
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['inquiry' => 'Unable to send message at this moment. Please try again.'])
                ->withInput();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Message Sent Successfully!',
                'inquiry' => $inquiry,
            ]);
        }

        return redirect()->back()->with('success_modal', true);
    }
}
