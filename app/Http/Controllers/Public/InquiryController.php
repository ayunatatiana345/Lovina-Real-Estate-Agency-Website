<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Property;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'property_id' => 'nullable|exists:properties,id',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'source' => 'nullable|string|max:255',
        ]);

        if (empty($validated['subject']) && !empty($validated['property_id'])) {
            $prop = Property::find($validated['property_id']);
            if ($prop) {
                $validated['subject'] = 'Inquiry for ' . $prop->name;
            }
        }

        $validated['status'] = 'new';
        if (empty($validated['source'])) {
            $validated['source'] = !empty($validated['property_id']) ? 'Property Detail Page' : 'Contact Us Form';
        }
        $inquiry = Inquiry::create($validated);

        \App\Models\InquiryStatusLog::create([
            'inquiry_id' => $inquiry->id,
            'status' => 'new',
            'changed_at' => now(),
        ]);

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
