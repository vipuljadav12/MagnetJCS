<?php

use App\Modules\EditCommunication\Excel\MailCommunicationExcel;
use App\Modules\EditCommunication\Models\EditCommunicationLog;
use Illuminate\Support\Facades\Mail;
/**
 *  EmailScripts Helper  
 */

function sendCommunicationMail($emailData)
{
    if (!empty($emailData))
    {
       
        $sendto=[];
        $sendemailData['logo'] = "{{url('resources/assets/admin/images/login.png')}}";
        foreach($emailData['content'] as $key=>$value){
            $sendemailData['email_text'] = $value;
            $data['content'][$key]=$value;
            $data['date']=$emailData['date'];
            $pdf = PDF::loadView('EditCommunication::PDF.communication', compact('data'));
            $sendemailData['sendto'] = "krishnavala68@gmail.com";
            // $sendemailData['sendto']=$emailData['sendto'][$key];
            $subject = $emailData['subject'];
           try{
                Mail::send('emails.index', ['data' => $sendemailData], function ($message) use ($sendemailData, $subject,$pdf) {
                    $message->to($sendemailData['sendto']);
                    $message->subject($subject);
                    $message->attachData($pdf->output(), $subject.".pdf");
                });

                $saveexcel[]=[
                    $emailData['district_id'],
                    $emailData['name'][$key],
                    $emailData['sendto'][$key],
                    $emailData['final_status'],
                ];

           }catch(Exception $e) {
              echo 'Message: ' .$e->getMessage(); 
            }
        }
        saveMailExcel($saveexcel,$emailData);
        
    } 
    return 'success';
}
function saveMailExcel($saveexcel,$emailData){

    $main_path =base_path('resources/assets/admin/communication/EmailExcel');
    if(!is_dir($main_path))
    {
        $file_path = mkdir($main_path);
    }
    $final_status=$emailData['final_status'];
    $filename=$final_status.'Email'.date('d-m-y')."-".time().'.xlsx';
    if(isset($saveexcel) && !empty($saveexcel))
    {    
        Excel::store(new MailCommunicationExcel($saveexcel), $filename,'EmailExcel_Upload');
        $insertpdf=[
        'district_id'=>$emailData['district_id'],
        'final_status'=> $emailData['final_status'],
        'file_name'=> $filename,
        'log_type'=> 'EE',
        ];
        EditCommunicationLog::create($insertpdf);
   }
}
