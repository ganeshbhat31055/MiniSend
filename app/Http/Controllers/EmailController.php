<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\EmailBody;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
                        'from'=>'required|email',
                        'to'=>'required|email',
                        'subject'=>'string|nullable',
                        'text'=>'required|string',
                        'html'=>'string|nullable',
//                        'attachments'=>'max:5000'
                    ],$messages = [
                        'required' => 'The :attribute field is required.',
                        'email' =>'Email in incorrect format'
                    ]);

        $files = $request->file('attachments');

        if($request->hasFile('attachments'))
        {
            foreach ($files as $file) {
                ($file->getClientOriginalName());
//                $file->store('users/' . $this->user->id . '/messages');
            }
        }
//        if ($request->file as $attach){
//
//        }

        if ($validator->fails()){
            return response()
                 ->json([
                     'status'=>'failure',
                     'message'=>$validator->errors()->first()
                 ]);
        }else{
            try {
                $emailId = Email::create([
                    'to'=>$request->input('to'),
                    'from'=>$request->input('from'),
                    'subject'=>$request->input('subject'),
                    'status'=>rand(0,2),
                ]);

                EmailBody::create([
                    'email_id'=>$emailId->id,
                    'body'=>$request->input('text'),
                    'html_body'=>$request->input('html')
                ]);

            }catch (\Exception $exception){
                info($exception);
            }

            return response()
                 ->json([
                     'status'=>'success',
                     'id'=>$emailId->id
                 ]);
        }
    }



    public function search(Request $request)
    {
        $param = $request->query('param');
        $search = $request->query('search');

        if ($param == 'null'){
            $emails = Email::paginate(6);
        }else{
            $emails = Email::where($param,'like','%'.$search.'%')->paginate(6);
        }

        return $emails;
    }

    public function dashboard(): JsonResponse
    {
        $success = Email::where('status',0)->count();
        $failed = Email::where('status',1)->count();
        $posted = Email::where('status',2)->count();

        return response()->json([
            'success'=>$success,
            'failed'=>$failed,
            'posted'=>$posted
        ]);
    }

    public function show(Email $email){
        return response()->json([
            'to'=>$email->to,
            'from'=>$email->from,
            'subject'=>$email->subject,
            'text'=>$email->body->body,
            'html'=>$email->body->html_body
        ]);
    }

}
