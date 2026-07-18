<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AiposSettingController extends Controller
{
    public function edit()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $settings = AppSetting::getMany([
            'aipos_url' => 'https://www.aiposystem.com/my/dashboard',
            'aipos_email' => '',
            'aipos_password' => '',
        ]);

        return view('admin.aipos-setting', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'aipos_url' => 'required|url|max:500',
            'aipos_email' => 'required|string|max:255',
            'aipos_password' => 'required|string|max:255',
        ]);

        AppSetting::setMany([
            'aipos_url' => $validated['aipos_url'],
            'aipos_email' => $validated['aipos_email'],
            'aipos_password' => $validated['aipos_password'],
        ]);

        Session::flash('success', 'Pengaturan AIPOS berhasil disimpan.');
        return redirect()->route('admin.aipos.edit');
    }
}
