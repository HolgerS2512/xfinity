<?php

namespace App\Http\Controllers;

use App\Models\VersionManager;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;
use App\Traits\Security\Cryptography;
use Exception;
use Illuminate\Support\Facades\Log;

class CookieController extends Controller
{
    /**
     * Category name & id
     *
     * @var string
     */
    private string $categoryName = 'L_CD';
    private string $categoryId = 'QMwMbD9y2Brej92G20240805192529';

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Get the version of the hash
            $categoryVM = VersionManager::findOrFail($this->categoryId);

            // Set cookie for frontend hash (30 Days)
            $cookie = Cookie::make(
                $this->categoryName,
                $categoryVM->hash,
                (60 * 24 * 30),
                '/',
                str_replace('www.', '', substr(URL::to('/'), strpos(URL::to('/'), '://') + 3)),
                false,
                false,
            );

            return response()->cookie($cookie);
        } catch (Exception $e) {
            Log::channel('database')->error('CookieController|destroy: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }
}
