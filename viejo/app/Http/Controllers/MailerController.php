<?php

namespace App\Http\Controllers;

use Mail;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\Mailer\SendMailRequest;
use App\Mail\FileOrderMail;
use App\Mail\OrderMail;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;


class MailerController extends Controller
{
    use ApiResponse;


    public function __construct(Request $request)
    {

    }

    public function store(SendMailRequest $request)
    {
        $data = $request->all();
        try {
            //return view('mails.order', ['html' => $data['body'] ]);
            Mail::to($data['email'])->send(new OrderMail($data));
        } catch(Exception $e) {
            Log::channel('stack')->error('MailerController@store', [$e->getMessage()]);
            return $this->error();
        }

        return $this->success();
    }

    public function sendFile(SendMailRequest $request)
    {
        $data = $request->all();
        try {
            //return view('mails.order', ['html' => $data['body'] ]);
            Mail::to($data['email'])->send(new FileOrderMail($data));
        } catch(Exception $e) {
            Log::channel('stack')->error('MailerController@store', [$e->getMessage()]);
            return $this->error();
        }

        return $this->success();
    }

}
