<?php

namespace App\Http\Controllers;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    //


    public function show()
{
    $data = QrCode::size(512)
        ->format('png')
        ->style('square')
        // ->color(152, 135, 78) // Main QR code in bronze
        // ->eyeColor(0, 181, 159, 84) // Top-left: Soft gold
        // ->eyeColor(1, 167, 139, 61) // Top-right: Rich ochre
        // ->eyeColor(2, 138, 121, 66) // Bottom-left: Warm tan
        ->merge('/storage/app/base_icon_white_background_1.png', 0.3)
        ->errorCorrection('H')
        // ->generate('https://tinyurl.com/bdz6wmh3'); // gojo shop android
        ->generate('http://gojotech.et/b'); // gojo seller android
        // ->generate('http://gojotech.et/a'); // gojo shop ios
        // ->generate('https://linktr.ee/habeshahubtravel'); // gojo seller ios

    return response($data)
        ->header('Content-type', 'image/png');
}



}
