<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Property;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $settings = CompanySetting::getSettings();
        $properties = Property::where('status', 'published')->orderBy('name', 'asc')->get();

        return view('public.contact', compact('settings', 'properties'));
    }
}
