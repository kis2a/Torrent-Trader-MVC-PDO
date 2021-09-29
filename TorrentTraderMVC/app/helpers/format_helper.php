<?php
// Function That Allows HTML Encoding
function encodehtml($s, $linebreaks = true)
{
    $s = str_replace("<", "&lt;", str_replace("&", "&amp;", $s));
    if ($linebreaks) {
        $s = nl2br($s);
    }
    return $s;
}

// URL Formatting
function format_urls($s)
{
    return preg_replace(
        "/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps|irc):\/\/[^<>\s]+)/i",
        "\\1<a href='\\2'>\\2</a>", $s);
}

// Parse String (BBcode)
function format_comment($text)
{
    global $smilies;
    $s = $text;
    $s = htmlspecialchars($s);
    $s = format_urls($s);
    // [*]
    $s = preg_replace("/\[\*\]/", "<li>", $s);
    // [center]Centrer[/center] ou [CENTER]center[/CENTER]
    $s = preg_replace("#\[center\](.+)\[/center\]#isU", "<center>$1</center>", $s);
    // [b]Bold[/b]
    $s = preg_replace("/\[b\]((\s|.)+?)\[\/b\]/", "<b>\\1</b>", $s);
    // [i]Italic[/i]
    $s = preg_replace("/\[i\]((\s|.)+?)\[\/i\]/", "<i>\\1</i>", $s);
    // [u]Underline[/u]
    $s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/", "<u>\\1</u>", $s);
    // [s]Line[/s]
    $s = preg_replace("#\[s\](.+)\[/s\]#isU", "<span style='text-decoration:line-through;'>$1</span>", $s);
    // List 1
    $s = preg_replace("#\[list\](.+)\[/list\]#isU", "<li style= 'margin-left: 20px;'>$1</li>", $s);
    // List 2
    //$s = preg_replace("#\[*\](.+)\[/*\]#isU", "<li style= 'margin-left: 20px;'>$1</li>", $s);
    // Quote 1
    while (preg_match("#\[quote\](.+)\[/quote\]#isU", $s)) {
        $s = preg_replace("#\[quote\](.+)\[/quote\]#isU", "<blockquote><strong> Quote : </strong></blockquote><table class='main' border='1' cellspacing='0' cellpadding='10'><tr><td style='border: 1px black dotted'>$1</td></tr></table><br />", $s);
    }

    // Quote 2
    while (preg_match("#\[quote=(.+)\](.+)\[/quote\]#isU", $s)) {
        $s = preg_replace("#\[quote=(.*?)\](.+)\[/quote\]#isU", "<blockquote><strong>$1 Quote : </strong></blockquote><table class='main' border='1' cellspacing='0' cellpadding='10'><tr><td style='border: 1px black dotted'>$2</td></tr></table><br />", $s);
    }

    ///Multi Quote For Private Message
    while (preg_match('#\[reponse(.*)\](.+)\[/reponse\]#isU', $s)) {
        $s = preg_replace('#\[reponse(.*)\](.+)\[/reponse\]#isU', '<table class=main border=2 cellspacing=0 cellpadding=10><tr><td style=border: 1px black dotted>$2</td></tr></table><br />', $s);
    }

    // Extract the Code
    $s = preg_replace("#\[code\](.+)\[/code\]#isU", "<b> Code : </b>
    <pre><code><div class='ttmobile' style='max-height:400px;white-space: nowrap'  readonly='readonly'>$1
    </div> </code></pre><br />", $s);
    // Links
    // [url=http://www.example.com]Text[/url]
    $s = preg_replace("#\[url=((?:ftp|https?)://.*?)\](.*?)\[/url\]#i", "<a href='$1'>$2</a>", $s);
    // [url]http://www.exemple.fr[/url]
    $s = preg_replace("#\[url\]((?:ftp|https?)://.*?)\[/url\]#i", "<a href='$1'>$1</a>", $s);
    // Image
    // [img]http://image.gif[/img]
    $s = preg_replace("#\[img\]((?:ftp|https?)://[a-z0-9._/-]+(\.gif|\.jpg|\.png|\.bmp|\.jpeg))\[/img\]#isU", "<div class='table-responsive ttmobile'><img src='$1' /></div>", $s);
    // [img=http://www/image.gif]
    $s = preg_replace("#\[img=((?:ftp|https?)://[a-z0-9._/-]+(\.gif|\.jpg|\.png|\.bmp|\.jpeg))\]\[/img\]#isU", "<img src='$1' />", $s);
    // [video]http://youtube.com/[/video]
    $s = preg_replace("#\[video\](https://www.youtube.com.*v=.+)\[\/video\]#isU", "<center><embed src='https://www.youtube.com/v/$1' type='video' height='140'></embed></center>", $s);
    // [audio]http://image.gif[/audio]
    $s = preg_replace("#\[audio\]((?:ftp|https?)://[a-z0-9._/-]+(\.mp3|\.wav|\.wma|\.aac|\.bwf|\.ogg|\.ac3|\.flac|\.asx|\.pls|\.alac))\[/audio\]#isU", "<img src='$1' />", $s);
    // Polices
    // [color=xxxx]Text[/color]
    $s = preg_replace("#\[color=([a-zA-Z]+)\](.+)\[/color\]#isU", "<font color='$1'>$2</font>", $s);
    // [color=#XXX ou #XXXXXX]Text[/color]
    $s = preg_replace("#\[color=\#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})\](.+)\[/color\]#isU", "<font color='$1'>$2</font>", $s);
    // Size of Style
    $s = preg_replace("#\[size=([1-7])\](.+)\[/size\]#isU", "<font size='$1'>$2</font>", $s);
    // Type of Style
    $s = preg_replace("#\[font=([a-zA-Z].*?)\](.+)\[/font\]#isU", "<font face='$1'>$2</font>", $s);
    // Blink
    $s = preg_replace("#\[blink\](.+)\[/blink\]#isU", "<div id='blink'>$1</div>", $s);
    // Scroller
    $s = preg_replace("#\[df\](.+)\[/df\]#isU", "<div class='marquee'>$1</div>", $s);
    // Text Alignment
    //[align=(center|left|right)]text[/align]
    $s = preg_replace(
        "/\[align=([a-zA-Z]+)\](.+?)\[\/align\]/is",
        "<div style=\"text-align:\\1\">\\2</div>", $s);
    // HTML HTML tag If Need Horizontal Line
    // Attention The Color Is Considered Only On IE For Info
    //[hr]
    $s = preg_replace("/\[hr\]/i", "<hr />", $s);
    //[hr=#ffffff] [hr=red]
    $s = preg_replace("/\[hr=((#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])|([a-zA-z]+))\]/i", "<hr color=\"\\1\"/>", $s);
    //[hide]Link[/hide]
    if (Config::TT()['HIDEBBCODE'] && Users::has('loggedin')) {
        $id = (int) Input::get("topicid");
        $reply = DB::run("SELECT * FROM forum_posts WHERE topicid=$id AND userid=$_SESSION[id]");
        if ($reply->rowCount() == 0) {
            $s = preg_replace(
                "/\[hide\]\s*((\s|.)+?)\s*\[\/hide\]\s*/i",
                "<p style='border: 3px solid red; width:50%'><font color=red><b>Please reply to view Links</b></font></p>",
                $s
            );
        }
    }
    // Linebreaks
    $s = nl2br($s);
    // Maintain spacing
    $s = str_replace("  ", " &nbsp;", $s);

    // Smilies
    reset($smilies);
    //while (list($code, $url) = thisEach($smilies)) {
    foreach($smilies as $code => $url) {
        $s = str_replace($code, '<img border="0" src="' . URLROOT . '/assets/images/smilies/' . $url . '" alt="' . $code . '" title="' . $code . '" />', $s);
    }

    if (Config::TT()['OLD_CENSOR']) {
        $r = DB::run("SELECT * FROM censor");
        while ($rr = $r->fetch(PDO::FETCH_ASSOC)) {
            $s = preg_replace("/" . preg_quote($rr[0]) . "/i", $rr[1], $s);
        }
    } else {
        $f = @fopen(LOGGER . "/censor.txt", "r");
        if ($f && filesize(LOGGER . "/censor.txt") != 0) {
            $bw = fread($f, filesize(LOGGER . "/censor.txt"));
            $badwords = explode("\n", $bw);

            for ($i = 0; $i < count($badwords); ++$i) {
                $badwords[$i] = trim($badwords[$i]);
            }
            $s = str_replace($badwords, "<img src='" . URLROOT . "/assets/images/censored.png' border='0' alt='Censored' title='Censored' />", $s);
        }
        @fclose($f);
    }
    return $s;
}