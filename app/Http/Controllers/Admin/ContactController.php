<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactRequest;
use App\Mail\Contact\ContactMail;
use App\Mail\Contact\FeedbackMail;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Http\Request;

final class ContactController extends Controller
{
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

            $saveInDB = Contact::insert($values);

            // Preparation mail values.
            // $mailV = [...$request->all()];
            // if (!$request->phone) $mailV['phone'] = ' - ';
            // if (!$request->salutation) $mailV['salutation'] = ' - ';

            // Mail::to('kontakt@xfinity.de')->send(new ContactMail($mailV));
            
            // Mail::to($request->email)->send(new FeedbackMail);

            return response()->json([
                'status' => $saveInDB,
                'message' => $saveInDB ? __("messages.contact") : __('error.500'),
            ], 200);
        } catch (Exception $e) {

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
