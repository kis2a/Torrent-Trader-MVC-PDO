<?php
class Ban
{
    public static function whereIn($ids)
    {
        $res = DB::run("SELECT * FROM bans WHERE id IN ($ids)");
        return $res;
    }

    public static function delete($id)
    {
        DB::run("DELETE FROM bans WHERE id=?", [$id]);
    }
    
    public static function insert($added, $addedby, $first, $last, $comment)
    {
        $bins = DB::run("INSERT INTO bans (added, addedby, first, last, comment) VALUES(?,?,?,?,?)", [$added, $addedby, $first, $last, $comment]);
        $err = $bins->errorCode();
        switch ($err) {
            case 1062:
                Redirect::autolink(URLROOT . '/adminbans/ip', "Duplicate ban.");
                break;
            case 0:
                Redirect::autolink(URLROOT . '/adminbans/ip', "Ban added.");
                break;
            default:
                Redirect::autolink(URLROOT . '/adminbans/ip', Lang::T("THEME_DATEBASE_ERROR"));
        }
    }

    public static function deleteemail($id)
    {
        DB::run("DELETE FROM email_bans WHERE id=$id");
    }
        
    public static function insertemail($added, $addedby, $mail_domain, $comment)
    {
        DB::run("INSERT INTO email_bans (added, addedby, mail_domain, comment) VALUES(?,?,?,?)", [$added, $addedby, $mail_domain, $comment]);
    }

}