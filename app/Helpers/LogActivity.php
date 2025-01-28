<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Request;
use App\Models\LogActivity as LogActivityModel;

class LogActivity
{
    public static function addToLog($subject,$details = NULL)
    {
        $log = [];
        $log['user_id'] = auth()->check() ? auth()->user()->id : 1;
        $log['subject'] = $subject;
        $log['details'] = $details;
        $log['url'] = Request::fullUrl();
        $log['method'] = Request::method();
        $log['ip'] = Request::ip();
        $log['agent'] = Request::header('user-agent');
        LogActivityModel::create($log);
    }

    public static function logActivityLists()
    {
        return LogActivityModel::latest()->limit(500)->get();
    }
}
