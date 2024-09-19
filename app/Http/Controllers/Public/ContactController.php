<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactRequest;
use App\Jobs\Auth\SendFeedbackMail;
use App\Models\Contact;
use Exception;
use Illuminate\Support\Facades\DB;

final class ContactController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\ContactRequest $request
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
            // $mailV = [...$request->validated()];
            // if (!$request->phone) $mailV['phone'] = ' - ';
            // if (!$request->salutation) $mailV['salutation'] = ' - ';

            // Mail::to('kontakt@xfinity.de')->send(new ContactMail($mailV));

            // Mail::to($request->email)->send(new FeedbackMail);
            dispatch(new SendFeedbackMail($request->email));

            if ($saved) {
                DB::commit();

                return response()->json([
                    'status' => true,
                ], 200);
            }

            DB::rollBack();

            return response()->json([
                'status' => false,
            ], 500);
        } catch (Exception $e) {
            DB::rollBack();
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
        }
    }
}
