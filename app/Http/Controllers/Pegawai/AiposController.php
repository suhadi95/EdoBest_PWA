<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Session;

class AiposController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $settings = AppSetting::getMany([
            'aipos_url' => 'https://www.aiposystem.com/my/dashboard',
            'aipos_email' => '',
            'aipos_password' => '',
        ]);

        $configured = filled($settings['aipos_email']) && filled($settings['aipos_password']);

        return view('pegawai.aipos', compact('settings', 'configured'));
    }
}
