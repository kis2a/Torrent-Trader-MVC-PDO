<?php
class Exceptions extends Controller
{
    public function __construct()
    {
        $this->session = Auth::user(0, 0);
    }

    public function index()
    {
        Redirect::autolink(URLROOT, Lang::T("Oops somwthing went wrong, Admin have been notified if this continues please contact a member of staff. Thank you"));
    }

}