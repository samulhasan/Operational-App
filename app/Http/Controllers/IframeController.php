<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Iframe;

class IframeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'iframeUrl' => 'required|url',
        ]);

        Iframe::create([
            'url' => $request->iframeUrl,
        ]);

        return redirect()->route('aws');
    }

    public function index()
    {
        $iframes = Iframe::all();
        return view('aws', compact('iframes'));
    }

    public function destroy(Iframe $iframe)
    {
        $iframe->delete();
        return response()->json(['success' => true]);
    }
}