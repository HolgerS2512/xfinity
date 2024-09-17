<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactRequest;
use App\Mail\Contact\ContactMail;
use App\Mail\Contact\FeedbackMail;
use App\Models\Contact;
use App\Models\User;
use App\Traits\Middleware\PermissionServiceTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class ContactController extends Controller
{
    use PermissionServiceTrait;

    /**
     * The permission name for permissionService.
     *
     * @var string
     */
    private string $permissionName = 'contact';

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

            // Exclude routes
            if ($request->routeIs('create')) {
                return $next($request);
            }

            if ($this->permisssionService($request, $next, $this->permissionName)) {

                return response()->json([
                    'status' => false,
                ], 403);
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

            Mail::to($request->email)->send(new FeedbackMail);

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
