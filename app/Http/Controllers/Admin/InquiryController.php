<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $settings = CompanySetting::getSettings();
        
        $query = Inquiry::with('property');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $inquiries = $query->latest()->paginate(10)->withQueryString();

        return view('admin.inquiries.index', compact('inquiries', 'settings'));
    }

    public function show($id)
    {
        $settings = CompanySetting::getSettings();
        $inquiry = Inquiry::with(['property', 'statusLogs'])->findOrFail($id);

        if ($inquiry->status === 'new') {
            $inquiry->status = 'in_progress';
            $inquiry->save();

            \App\Models\InquiryStatusLog::create([
                'inquiry_id' => $inquiry->id,
                'status' => 'in_progress',
                'changed_at' => now(),
            ]);
        }

        return view('admin.inquiries.show', compact('inquiry', 'settings'));
    }

    public function updateStatus(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:new,in_progress,responded,closed',
            'admin_notes' => 'nullable|string',
        ]);

        $oldStatus = $inquiry->status;
        $inquiry->update($validated);

        if ($oldStatus !== $inquiry->status) {
            \App\Models\InquiryStatusLog::create([
                'inquiry_id' => $inquiry->id,
                'status' => $inquiry->status,
                'changed_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Inquiry status updated successfully.');
    }

    public function destroy($id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry deleted successfully.');
    }
}
