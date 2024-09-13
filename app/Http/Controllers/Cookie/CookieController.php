<?php

namespace App\Http\Controllers\Cookie;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cookie\CookieRequest;
use Illuminate\Http\Request;
use App\Models\Consent;
use App\Models\ConsentCookie;
use App\Models\Cookie as CookieModel;
use App\Traits\Helpers\BooleanManager;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CookieController extends Controller
{
    use BooleanManager;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Cookie\CookieRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CookieRequest $request)
    {
        $saved = [];
        $user = [];
        DB::beginTransaction();

        try {
            // Check if user exists
            $this->middleware('auth');

            if (Auth::check()) {
                $userId = Auth::id();
                $user['user_id'] = $userId;
            }

            $values = [
                'consent_token' => Str::random(40),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'consent_given' => $request->consented,
            ];

            // Merge values and user id if exist
            $consentRecord = new Consent(array_merge($user, $values));

            // saved consents in db
            $saved[] = $consentRecord->save();

            if ($saved[0]) {

                // saved consent cookie in db (pivot table)
                foreach ($request->all() as $cookieCategory => $consented) {
                    if ($cookieCategory === 'consented') continue;
                    $cookiesM = CookieModel::where('category', $cookieCategory)->get();

                    foreach ($cookiesM as $cookieM) {
                        $model = new ConsentCookie([
                            'consent_id' => $consentRecord->id,
                            'cookie_id' => $cookieM->id,
                            'consented' => $consented,
                        ]);
                        $saved[] = $model->save();
                    }
                }
            }

            // Shows are all values in saved array true
            if ($this->evaluateBoolByAnd($saved)) {
                DB::commit();

                return response()->json([
                    'status' => true,
                ], 200);
            } else {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                ], 500);
            }
        } catch (HttpException $e) {
            DB::rollBack();
            Log::channel('database')->error('CookieController|store: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], $e->getStatusCode() ?? 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('database')->error('CookieController|store: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd('show');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {}

    /**
     * Remove resources from storage then older 10 years.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyOlderThen10Years()
    {
        DB::beginTransaction();

        try {
            DB::table('consents')->where('created_at', '<', Carbon::now()->subYears(10))->delete();
            DB::commit();

            return response()->json([
                'status' => true,
            ], 200);
        } catch (HttpException $e) {
            DB::rollBack();
            Log::channel('database')->error('CookieController|destroyOlderThen10Years: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], $e->getStatusCode() ?? 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('database')->error('CookieController|destroyOlderThen10Years: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], 500);
        }
    }
}

// Search cookie and get values as array
// if ($request->hasCookie($this->consentCookie)) {
//     $consent = json_decode($request->cookie($this->consentCookie), true);
// }