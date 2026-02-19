<?php

function setNewNameForPhoto($photo_original_name)
{
    // remove extension
    $without_ext = removeExtension($photo_original_name);
    //return $without_ext;
    return Str::slug($without_ext, '-');
}
/* remove extension from file name */
function removeExtension($original_name)
{
    return preg_replace('/\\.[^.\\s]{3,4}$/', '', $original_name);
}
/* Generate random number or string */
function generateRandomString($prefix = "", $alpha_numeric = 0, $count = 10)
{
    if ($alpha_numeric = 0) {
        $characters = '123456789';
    } else if ($alpha_numeric = 1) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    } else {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    }
    // generate a pin based on 2 * 7 digits + a random character
    $pin = mt_rand(1000000, 9999999) . mt_rand(1000000, 9999999) . $characters[rand(0, strlen($characters) - 1)];
    // shuffle the result
    $string = str_shuffle($pin);
    if (!empty($prefix)) {
        $string = $prefix . "-" . $string;
    }
    $string = substr($string, 0, $count);
    return trim($string);
}
function validateUser($user_id,$api_token)
{

    $user_data = \App\Models\User::where('id', $user_id)
        ->where('api_token', $api_token)
        ->select('id', 'api_token', 'firstname', 'lastname', 'email')
        ->first();

    // Check if $user_data is null
    if ($user_data) {
        // If user exists, assign and return the values
        $user_id = $user_data->id;
        $api_token = $user_data->api_token;
        return $user_id;
        return $api_token;
    } else {
        // If user_data is null, log an error and handle it
        \Log::error('User not found for ID: ' . $user_id . ' and API token: ' . $api_token);
        return null; // Return null or handle accordingly
    }

}
