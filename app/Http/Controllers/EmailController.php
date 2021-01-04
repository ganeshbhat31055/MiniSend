<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\EmailAttachments;
use App\Models\EmailBody;
use Illuminate\Http\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request): JsonResponse
    {
        sleep(4);
        $validator = Validator::make($request->all(),[
                        'from'=>'required|email',
                        'to'=>'required|email',
                        'subject'=>'string|nullable',
                        'text'=>'required|string',
                        'html'=>'string|nullable',
                    ],$messages = [
                        'required' => 'The :attribute field is required.',
                        'email' =>'Email in incorrect format'
                    ]);


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

                $files = $request->file('attachments');

                if($request->hasFile('attachments'))
                {
                    foreach ($files as $file) {
                        EmailAttachments::create([
                            'email_id'=>$emailId->id,
                            'file_name'=>'attachment-'.$emailId->id.'-'.$file->getClientOriginalName(),
                            'name'=>$file->getClientOriginalName()
                        ]);
                        $file->storePubliclyAs('public/attachments','attachment-'.$emailId->id.'-'.$file->getClientOriginalName());
                    }
                }

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
        $total = Email::count();
        $success = Email::where('status',0)->count();
        $failed = Email::where('status',1)->count();
        $posted = Email::where('status',2)->count();



        return response()->json([
            'total'=>$total,
            'success'=>$success,
            'failed'=>$failed,
            'posted'=>$posted
        ]);
    }

    public function show(Email $email){

        $files = $email->attachments;

        $attachments_data = ['length'=>$email->attachments->count()];
        foreach($files as $file){
            $array = ['url'=> 'http://127.0.0.1:8000'.Storage::url('attachments/'.$file->file_name),'name'=>$file->name];
            array_push($attachments_data,$array);
        }

        return response()->json([
            'to'=>$email->to,
            'from'=>$email->from,
            'subject'=>$email->subject,
            'text'=>is_null($email->body) ? null : $email->body->body,
            'html'=>is_null($email->body) ? null :$email->body->html_body,
            'attachments'=>$attachments_data
        ]);
    }

}
