<?php
function check_email($email) {
    $email = strtolower($email); 
    $pattern = '/^[a-z][a-z0-9]{1,}@[a-z0-9]{2,}\.[a-z]{2,}$/';
    if (preg_match($pattern, $email)){
        return true;
    } 

    {
        return false;
    }
}
$emails = [
    "john@isae.edu.lb",
    "john@cnam.fr",
    "j0hn@cnam.fr", // Zero à la place de o
    "john@cnam.fr",
    "john@cnam.fr",
    "j4@cnam.fr",
    "j@cnam.fr",
    "jo@cnam.fr",
    "jo@c.fr",
    "jo@cnam.f",
    "jo@cnam.com",
    "jo@cnam.group",
    "jo@cnam.com@Liban",
];
function check_phone($nums) {
    $pattern1 = '/^(?:\+961|00961)(3|70|71|76|81)\d{6}$/';
    if (preg_match($pattern1, $nums)) {
        return true;
    } else {
        return false;
    }
}
$nums = [
    "+961 70 500 560", // false
    "+96170500560", // true
    "0096170500560", // true
    "0096103500560", // false
    "009613500560", // true
    "009617050056", // false
    "009619500560", // false
    "0096129500560", // false
    "@Henry0096170500560Liban", // false
];

foreach($emails as $element){
    echo $element." ".check_email($element)."<br>"
}
?>

