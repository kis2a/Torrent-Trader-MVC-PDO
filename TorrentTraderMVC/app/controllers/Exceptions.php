<?php
class Exceptions extends Controller
{
    public function __construct()
    {}

    public function index()
    {
        Session::flash('info', Lang::T("Oops somwthing went wrong, Admin have been notified if this continues please contact a member of staff. Thank you"), URLROOT."/index");
    }

}