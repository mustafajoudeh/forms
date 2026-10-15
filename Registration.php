
<?php

class Registration
{
    public $name;
    public $email;
    public $mobile;
    public $governorate;
    public $track;
    public $skills;
    public $message;

    public function __construct($data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->mobile = $data['mobile'];
        $this->governorate = $data['governorate'];
        $this->track = $data['track'];
        $this->skills = $data['skills'];
        $this->message = $data['message'];
    }
}
