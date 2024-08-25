<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactRequest;
use App\Mail\Contact\ContactMail;
use App\Mail\Contact\FeedbackMail;
use App\Models\Contact;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class ContactController extends Controller
{
    /**
     * 
     * Applies middleware to check user permissions before allowing access to
     * specific routes. Users without the appropriate permissions will receive
     * a 403 Unauthorized response.
     * 
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $id = Auth::id();
            $user = User::findOrFail($id);

            if ($request->routeIs('contact.index') || $request->routeIs('contact.show')) {
                if (!$user->hasPermission('read')) {

                    return response()->json([
                        'status' => false,
                        'error' => __('auth.unauthenticated'),
                    ], 403);
                }
            }

            if ($request->routeIs('contact.update')) {
                if (!$user->hasPermission('edit') || !$user->hasPermission('update')) {

                    return response()->json([
                        'status' => false,
                        'error' => __('auth.unauthenticated'),
                    ], 403);
                }
            }

            if ($request->routeIs('contact.destroy')) {
                if (!$user->hasPermission('delete')) {

                    return response()->json([
                        'status' => false,
                        'error' => __('auth.unauthenticated'),
                    ], 403);
                }
            }

            return $next($request);
        });
    }

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
     * @param  \Illuminate\Http\ContactRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function create(ContactRequest $request)
    {
        DB::beginTransaction();

        try {
            // Preparation save new contact message.
            $values = [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'message' => $request->message,
            ];

            if ($request->phone) $values['phone'] = $request->phone;
            if ($request->salutation) $values['salutation'] = $request->salutation;

            $saved = Contact::insert($values);

            // Preparation mail values.
            // $mailV = [...$request->all()];
            // if (!$request->phone) $mailV['phone'] = ' - ';
            // if (!$request->salutation) $mailV['salutation'] = ' - ';

            // Mail::to('kontakt@xfinity.de')->send(new ContactMail($mailV));

            Mail::to($request->email)->send(new FeedbackMail);

            if ($saved) DB::commit();

            return response()->json([
                'status' => $saved,
                'message' => $saved ? __("messages.contact") : __('error.500'),
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
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
        //
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
    public function destroy($id)
    {
        //
    }
}
