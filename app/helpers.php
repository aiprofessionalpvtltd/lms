<?php

use App\Models\Allotee;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

function getSettingValue($val)
{
    $settings = Setting::getSetting();
    return $settings->$val;
}

function formatOrdinal($number)
{
    $suffixes = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
    $mod = $number % 100;
    return $number . ($suffixes[($mod - 20) % 10] ?? $suffixes[$mod] ?? 'th');
}

function showStatus($status)
{

    if ($status == 1) {
        echo '<span class="badge badge-success">Active</span>';

    } else {
        echo '<span class="badge badge-danger">InActive</span>';

    }

}

function showStatusBoostrap($status)
{

    if ($status == 1) {
        return '<span class="badge bg-success">Active</span>';

    } else {
        return '<span class="badge bg-danger">InActive</span>';

    }

}

function showCompleted($status)
{

    if ($status == 1) {
        echo 'Completed';

    } else {
        echo 'Not Completed';

    }

}

function showBoolean($status)
{

    if ($status == 1) {
        echo '<span class="badge bg-success">YES</span>';

    } else {
        echo '<span class="badge bg-danger">NO</span>';

    }

}

function showBooleanStatus($status)
{

    if ($status == 1) {
        return '<span class="badge bg-success">YES</span>';

    } else {
        return '<span class="badge bg-danger">NO</span>';

    }

}

function showApprovalStatus($status)
{

    if ($status === 'pending') {
        echo '<span class="badge bg-warning">Pending</span>';

    }
    if ($status === 'accepted') {
        echo '<span class="badge bg-success">Accepted</span>';

    }  if ($status === 'rejected') {
        echo '<span class="badge bg-danger">Rejected</span>';

    }

}


function settingImagePath($image)
{
    if ($image != null)
        return asset('storage/settings/' . $image);
}

function uploadImage($file, $dir)
{
//    $imageName = $request->file('image')->store('courses/images', 'public');
    $filename = $file->store($dir, 'public');

//    $filename = date('YmdHi') . $file->getClientOriginalName();
//    $file->move(public_path('public' . '/' . $dir), $filename);
    return $filename;

}

function showImage($image, $dir)
{
     if (!empty($image)) {
        return asset('storage/' . $image);
    } else {
        $image = 'placeholder.jpg';
        return asset('assets/' . $image);

    }

}


function currentDate()
{
    return date('d-M-Y');
}

function currentDateAgreement()
{
    return date('F d,Y');
}

function currentDatePicker()
{
    return date('m/d/Y');
}

function currentDateInput()
{
    return date('mm/dd/yyyy');
}

function currentDateInsert()
{
    return date('Y-m-d');
}

function currentDateTimeInsert()
{
    return date('Y-m-d h:i:s');
}


function currentYear()
{
    return date('Y');
}

function currentMonthStart()
{
    return date('Y-m-01');
}

function currentMonthEnd()
{
    return date('Y-m-t');
}

function currentMonth()
{
    return date('m');
}

function dateInsert($obj)
{
    return date('Y-m-d', strtotime($obj,));
}

function monthInsert($obj)
{
    return date('m', strtotime($obj));
}

function yearInsert($obj)
{
    return date('Y', strtotime($obj));
}

function showDatePicker($obj)
{
//    return date('d/m/Y', strtotime($obj));
    return date('m/d/Y', strtotime($obj));
}

function showDate($obj)
{
    if ($obj != '0000-00-00') {

        return date('d-M-Y', strtotime($obj));
    } else {
        return '';
    }
}

function showDateTime($obj)
{
    return date('d-M-Y h:i:s', strtotime($obj));
}

function showMonth($obj)
{
    return date('M', strtotime($obj));
}


function ShowFullName($firstName, $lastName)
{
    return $firstName . ' ' . $lastName;
}


function inputMaskDash($obj)
{
    return str_replace('-', '', $obj);
}

function validateTimeStamp($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function calculatePercentage($score, $total)
{
    $formatted_percentage = '';
    if ($total != 0) {
        $percentage = ($score / $total) * 100;
    } else {
        // handle division by zero error
        $percentage = 0; // or some other default value
    }


    if ($percentage <= 100) {
        $formatted_percentage = number_format($percentage, 2);
    } else {
        $formatted_percentage = number_format(0, 2);

    }
    return $formatted_percentage;
}



function determineRiskLevel($score)
{
    if ($score >= 85 && $score <= 100) {
        return [
            'risk_level' => 'Low Risk',
            'loan_eligibility' => 'Eligible for larger loan amounts at lower interest rates.'
        ];
    } elseif ($score >= 65 && $score <= 84) {
        return [
            'risk_level' => 'Moderate Risk',
            'loan_eligibility' => 'Eligible for moderate loan amounts with standard interest rates.'
        ];
    } else {
        return [
            'risk_level' => 'High Risk',
            'loan_eligibility' => 'Eligible for small loan amounts with higher interest rates or may require stricter terms (e.g., collateral).'
        ];
    }
}



