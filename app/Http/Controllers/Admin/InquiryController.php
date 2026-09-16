<?php

// Tatiana handles inquiry data here.

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
        
        // Tab segmentation: 'unreplied' (default), 'replied', or 'all'
        $currentTab = $request->get('tab', 'unreplied');
        if (!in_array($currentTab, ['unreplied', 'replied', 'all'])) {
            $currentTab = 'unreplied';
        }

        // Live badge counts
        $unrepliedCount = Inquiry::whereIn('status', ['new', 'in_progress'])->count();
        $repliedCount = Inquiry::where('status', 'responded')->count();
        $allCount = Inquiry::count();

        $query = Inquiry::with('property');

        // Apply Tab scope
        if ($currentTab === 'unreplied') {
            $query->whereIn('status', ['new', 'in_progress']);
        } elseif ($currentTab === 'replied') {
            $query->where('status', 'responded');
        }

        // Apply optional refined status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter (customer name, email, phone, subject, property name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('property', function ($propQuery) use ($search) {
                      $propQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $inquiries = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('admin.inquiries.index', compact(
            'inquiries',
            'settings',
            'currentTab',
            'unrepliedCount',
            'repliedCount',
            'allCount'
        ));
    }

    public function show($id)
    {
        $settings = CompanySetting::getSettings();
        $inquiry = Inquiry::with(['property', 'statusLogs'])->findOrFail($id);

        // Viewing/reading an inquiry does NOT mark it as Replied or alter its status prematurely.
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

    public function replyEmail(Request $request, $id)
    {
        $inquiry = Inquiry::with('property')->findOrFail($id);

        if (empty($inquiry->email) || !filter_var($inquiry->email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withErrors([
                'email' => 'A valid customer email address is not available for this inquiry.'
            ])->withInput();
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($inquiry->email, $inquiry->customer_name)->send(
                new \App\Mail\InquiryReplyMail($inquiry, $validated['subject'], $validated['message'])
            );

            // Transition status to responded if inquiry was in new/in_progress
            if (in_array($inquiry->status, ['new', 'in_progress'])) {
                $inquiry->status = 'responded';
                $inquiry->save();

                \App\Models\InquiryStatusLog::create([
                    'inquiry_id' => $inquiry->id,
                    'status' => 'responded',
                    'changed_at' => now(),
                ]);
            }

            return redirect()->back()->with('success', 'Email reply sent successfully to ' . $inquiry->email);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Inquiry reply email delivery failed: ' . $e->getMessage(), [
                'inquiry_id' => $inquiry->id,
                'recipient' => $inquiry->email,
            ]);

            return redirect()->back()->withErrors([
                'email' => 'Unable to deliver the email. Please check your mail server configuration and try again.'
            ])->withInput();
        }
    }

    public function destroy($id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry deleted successfully.');
    }
}

