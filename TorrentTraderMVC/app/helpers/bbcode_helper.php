<?php
// Function To Display BBCodes And Smilies
function textbbcode($form, $name, $content = "")
{
    // $form = Form Name, $name = Name of Text Area (Textarea), $content = Content Textarea (Only to edit Pages, etc ...)
    // Includen JS Function For BBCode
    require "assets/js/BBTag.js";
    ?>
    <div class="container border border-warning">
        <div class="row justify-content-md-center">
        <div class="col-10">
	    <?php
		// bbcode
        print("<br><input id='BBCode' type='button' name='Bold' 			value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/bold.gif');  height:20px; width:20px;\" 					onclick=\"bbcomment('[b]', '[/b]')\" 					alt='Bold' 				title='Bold' 				/>");
        print("<input id='BBCode' type='button' name='Italic' 			value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/Italic.png');  height:20px; width:20px;\" 					onclick=\"bbcomment('[i]', '[/i]')\" 					alt='Italic' 			title='Italic' 			/>");
        print("<input id='BBCode' type='button' name='Underline' 			value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/underline.png');  height:20px; width:20px;\" 				onclick=\"bbcomment('[u]', '[/u]')\" 					alt='Underline' 			title='Underline'			/>");
        print("<input id='BBCode' type='button' name='Strike' 				value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/strike.png');  height:20px; width:20px;\"			 		onclick=\"bbcomment('[s]', '[/s]')\" 					alt='Strike' 		title='Strike'		/>");
        print("<input id='BBCode' type='button' name='List' 				value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/indent.png');  height:20px; width:20px;\" 					onclick=\"bbcomment('[list]', '[/list]')\" 				alt='List' 				title='List'				/>");
        print("<input id='BBCode' type='button' name='Quote' 			       value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/quote.png');  height:20px; width:20px;\"			 		onclick=\"bbcomment('[quote]', '[/quote]')\" 			alt='Quote' 			title='Quote'			/>");
        print("<input id='BBCode' type='button' name='Code' 				value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/code.png');  height:20px; width:20px;\" 					onclick=\"bbcomment('[code]', '[/code]')\" 			alt='Code' 			title='Code'				/>");
        print("<input id='BBCode' type='button' name='Url' 				value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/url.png');  height:20px; width:20px;\" 					onclick=\"bbcomment('[url]', '[/url]')\"				alt='Lnk' 				title='Link'				/>");
        print("<input id='BBCode' type='button' name='Image' 				value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/image.png');  height:20px; width:20px;\" 					onclick=\"bbcomment('[img]', '[/img]')\"				alt='Image' 			title='Image'				/>");
        print("<input id='BBCode' type='button' name='Video' 				value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/Video.gif');  height:20px; width:20px;\" 					onclick=\"bbcomment('[video]', '[/video]')\" 			alt='Vidéo' 			title='Video'				/>");
        print("<input id='BBCode' type='button' name='Scroller' 			value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/scroller.png');  height:20px; width:20px;\"					onclick=\"bbcomment('[df]', '[/df]')\" 				alt='Scroller' 			title='Scroller'			/>");
        print("<input id='BBCode' type='button' name='Right'            	value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/right.gif');  height:20px; width:20px;\" 					onclick=\"bbcomment('[align=left]','[/align]')\" 			alt='Right' 		title='Right' 	/>");
        print("<input id='BBCode' type='button' name='Center'        	value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/center.gif');  height:20px; width:20px;\" 					onclick=\"bbcomment('[align=center]','[/align]')\" 		alt='Center' 		title='Center' 	/>");
        print("<input id='BBCode' type='button' name='Left'             	value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/left.gif');  height:20px; width:20px;\" 					onclick=\"bbcomment('[align=right]','[/align]')\" 		alt='eft' 		       title='Left' 	/>");
        print("<input id='BBCode' type='button' name='Hide'             	value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/hide.gif');  height:20px; width:20px;\" 					onclick=\"bbcomment('[hide]','[/hide]')\" 		alt='eft' 		       title='Hide' 	/>");
		print("<a href='#' onClick=\"window.open('https://imgur.com/upload','_blank');return(false)\">		<input id='BBCode' type='button' value='' 	style=\"background: url('" . URLROOT . "/assets/images/bbcodes/imgur.gif');  height:20px; width:20px;\" 		alt='Upload Image' 			title='Upload Image' /></a>");
        print("<a href='#' onClick=\"window.open('http://www.youtube.com','_blank');return(false)\">		<input id='BBCode' type='button' value='' 	style=\"background: url('" . URLROOT . "/assets/images/bbcodes/youtube.gif');  height:20px; width:20px;\"										alt='YouTube' 				title='YouTube' /></a>");
	    // colour
		print("<select name='color'  onChange='bbcouleur(this.value);' title='Couleur'>");
        print("<option value='0' name='color'>Colour</option>");
        print("<option value='#000000' style='BACKGROUND-COLOR:#000000'>Black</option>");
        print("<option value='#686868' style='BACKGROUND-COLOR:#686868'>Grey</option>");
        print("<option value='#708090' style='BACKGROUND-COLOR:#708090'>Dark Grey</option>");
        print("<option value='#C0C0C0' style='BACKGROUND-COLOR:#C0C0C0'>Light Grey</option>");
        print("<option value='#FFFFFF' style='BACKGROUND-COLOR:#FFFFFF'>White</option>");
        print("<option value='#FFFFE0' style='BACKGROUND-COLOR:#FFFFE0'>Beech</option>");
        print("<option value='#880000' style='BACKGROUND-COLOR:#880000'>Dark Red</option>");
        print("<option value='#B82428' style='BACKGROUND-COLOR:#B82428'>Light Red</option>");
        print("<option value='#FF0000' style='BACKGROUND-COLOR:#FF0000'>Red</option>");
        print("<option value='#FF1490' style='BACKGROUND-COLOR:#FF1490'>Dark Pink</option>");
        print("<option value='#FF68B0' style='BACKGROUND-COLOR:#FF68B0'>Pink</option>");
        print("<option value='#FFC0C8' style='BACKGROUND-COLOR:#FFC0C8'>Light Pink</option>");
        print("<option value='#FF4400' style='BACKGROUND-COLOR:#FF4400'>Dark Orange</option>");
        print("<option value='#FF6448' style='BACKGROUND-COLOR:#FF6448'>Redish Orange</option>");
        print("<option value='#FFA800' style='BACKGROUND-COLOR:#FFA800'>Orange</option>");
        print("<option value='#FFD800' style='BACKGROUND-COLOR:#FFD800'>Dark Yellow</option>");
        print("<option value='#FFFF00' style='BACKGROUND-COLOR:#FFFF00'>Yellow</option>");
        print("<option value='#FF00FF' style='BACKGROUND-COLOR:#FF00FF'>Light Purple</option>");
        print("<option value='#C01480' style='BACKGROUND-COLOR:#C01480'>Dark Purple</option>");
        print("<option value='#B854D8' style='BACKGROUND-COLOR:#B854D8'>Dark Violet</option>");
        print("<option value='#D8A0D8' style='BACKGROUND-COLOR:#D8A0D8'>Light Violet</option>");
        print("<option value='#000080' style='BACKGROUND-COLOR:#000080'>Darkest Blue</option>");
        print("<option value='#0000FF' style='BACKGROUND-COLOR:#0000FF'>Dark Blue</option>");
        print("<option value='#2090FF' style='BACKGROUND-COLOR:#2090FF'>Ble</option>");
        print("<option value='#00BCFF' style='BACKGROUND-COLOR:#00BCFF'>Light Blue</option>");
        print("<option value='#B0E0E8' style='BACKGROUND-COLOR:#B0E0E8'>Faint Blue</option>");
        print("<option value='#A02828' style='BACKGROUND-COLOR:#A02828'>Brown</option>");
        print("<option value='#F0A460' style='BACKGROUND-COLOR:#F0A460'>Brown Creme</option>");
        print("<option value='#D0B488' style='BACKGROUND-COLOR:#D0B488'>Light Brown</option>");
        print("<option value='#B8B868' style='BACKGROUND-COLOR:#B8B868'>Brown/Green</option>");
        print("<option value='#008000' style='BACKGROUND-COLOR:#008000'>Dark Green</option>");
        print("<option value='#30CC30' style='BACKGROUND-COLOR:#30CC30'>Green</option>");
        print("<option value='#00FF80' style='BACKGROUND-COLOR:#00FF80'>Light Green</option>");
        print("<option value='#98FC98' style='BACKGROUND-COLOR:#98FC98'>Light Lime</option>");
        print("<option value='#98CC30' style='BACKGROUND-COLOR:#98CC30'>Dark Lime</option>");
        print("<option value='#40E0D0' style='BACKGROUND-COLOR:#40E0D0'>Turquois</option>");
        print("<option value='#20B4A8' style='BACKGROUND-COLOR:#20B4A8'>Aquarium</option></select>");
		// Style
		print("<select name='font' onChange='bbfont(this.value);' title='Style'>");
		print("<option value='0' name='font'>Style</option><option value='Arial' style='font-family: Arial;'>Arial</option>");
		print("<option value='Comic Sans MS' style='font-family: Comic Sans MS;'>Comic</option><option value='Trebuchet MS' style='font-family: Trebuchet MS;'>Trebuchet</option>");
		print("<option value='Courier New' style='font-family: Courier New;'>Courier</option><option value='Georgia' style='font-family: Georgia;'>Georgia</option>");
		print("<option value='Impact' style='font-family: Impact;'>Impact</option><option value='Lucida Sans Unicode' style='font-family: Lucida Sans Unicode;'>Lucida</option>");
		print("<option value='Microsoft Sans Serif' style='font-family: Microsoft Sans Serif;'>Microsoft</option>");
		print("<option value='Tahoma' style='font-family:Tahoma;'>Tahoma</option><option value='Times New Roman' style='font-family:Times New Roman;'>Roman</option>");
		print("<option value='Verdana' style='font-family:Verdana;'>Verdana</option><option value='Palatino Linotype' style='font-family:Palatino Linotype;'>Palatino</option>");
        print("<option value='Ravie' style='font-family:Ravie;'>Ravie</option><option value='WESTERN' style='font-family:WESTERN;'>Western</option>");
        print("<option value='Amerika' style='font-family:Amerika;'>Amerika</option><option value='Goudy Old Style' style='font-family:Goudy Old Style;'>Goudy</option>");
        print("<option value='Papyrus' style='font-family: Papyrus;'>Papyrus</option><option value='Brush Script MT' style='font-family:Brush Script MT;'>Brush</option></select>");
	    // Size
		print("<select name='size' onchange='bbsize(this.value);' title='Size'><option value='0' name='size'>Size &nbsp;&nbsp;</option>");
        print("<option value='1'>1x</option><option value='2'>2x</option><option value='3'>3x</option><option value='4'>4x</option>");
        print("<option value='5'>5x</option><option value='6'>6x</option><option value='7'>7x</option></select>");
        ?>
        </div>
        </div>
		
		<div class="row justify-content-md-center">
		<div class="col-10">
		<a href="javascript:SmileIT(':)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/smile.png" border="0" alt=':)' title=':)' /></a>
        <a href="javascript:SmileIT(':(','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/sad.png" border="0" alt=':(' title=':(' /></a>
        <a href="javascript:SmileIT(':D','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/grin.png" border="0" alt=':D' title=':D' /></a>
        <a href="javascript:SmileIT(':P','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/razz.png" border="0" alt=':P' title=':P' /></a>
        <a href="javascript:SmileIT(':-)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/smile-big.png" border="0" alt=':-)' title=':-)' /></a>
        <a href="javascript:SmileIT('B)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/cool.png" border="0" alt='B)' title='B)' /></a>
        <a href="javascript:SmileIT('8o','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/eek.png" border="0" alt='8o' title='8o' /></a>
        <a href="javascript:SmileIT(':?','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/confused.png" border="0" alt=':?' title=':?' /></a>
        <a href="javascript:SmileIT('8)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/glasses.png" border="0" alt='8)' title='8)' /></a>
        <a href="javascript:SmileIT(';)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/wink.png" border="0" alt=';)' title=';)' /></a>
        <a href="javascript:SmileIT(':-*','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/kiss.png" border="0" alt=':-*' title=':-*' /></a>
        <a href="javascript:SmileIT(':-(','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/crying.png" border="0" alt=':-(' title=':-(' /></a>
        <a href="javascript:SmileIT(':|','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/plain.png" border="0" alt=':|' title=':|' /></a>
        <a href="javascript:SmileIT('O:-D','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/angel.png" border="0" alt='O:-D' title='0:-D' /></a>
        <a href="javascript:SmileIT(':-@','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/devilish.png" border="0" alt=':-@' title=':-@' /></a>
        <a href="javascript:SmileIT(':o)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/monkey.png" border="0" alt=':o)' title=':o)' /></a>
        <a href="javascript:SmileIT('brb','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/brb.png" border="0" alt='brb' title='brb' /></a>
        <a href="javascript:SmileIT(':warn','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/warn.png" border="0" alt=':warn' title=':warn' /></a>
        <a href="javascript:SmileIT(':help','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/help.png" border="0" alt=':help' title=':help' /></a>
        <a href="javascript:SmileIT(':bad','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/bad.png" border="0" alt=':bad' title=':bad' /></a>
        <a href="javascript:SmileIT(':love','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/love.png" border="0" alt=':love' title=':love' /></a>
        <a href="javascript:SmileIT(':idea','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/idea.png" border="0" alt=':idea' title=':idea' /></a>
        <a href="javascript:SmileIT(':bomb','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/bomb.png" border="0" alt=':bomb' title=':bomb' /></a>
        <a href="javascript:SmileIT(':!','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/important.png" border="0" alt=':!' title=':!' /></a>
        <a href="javascript:SmileIT(':gigg','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/giggle.png" border="0" alt=':|' title=':|' /></a>
        <a href="javascript:SmileIT(':rofl','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/roflmao.png" border="0" alt='O:-D' title='0:-D' /></a>
        <a href="javascript:SmileIT(':slep','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/sleep.png" border="0" alt=':-@' title=':-@' /></a>
        <a href="javascript:SmileIT(':thum','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/thumbsup.png" border="0" alt=':o)' title=':o)' /></a>
        <a href="javascript:SmileIT(':0_0','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/zpo.png" border="0" alt=':|' title=':|' /></a>
        <a href="javascript:SmileIT(':poop','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/poop.png" border="0" alt='O:-D' title='0:-D' /></a>
        <a href="javascript:SmileIT(':spechles','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/speechless.png" border="0" alt=':-@' title=':-@' /></a>
        <a href="javascript:SmileIT(':unsure','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/unsure.png" border="0" alt=':o)' title=':o)' /></a>
        <a href="javascript:SmileIT(':mad','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/mad.png" border="0" alt=':|' title=':|' /></a>
        <a href="javascript:SmileIT(':roll','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/rolleyes.png" border="0" alt='O:-D' title='0:-D' /></a>
        <a href="javascript:SmileIT(':sick','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/sick.png" border="0" alt=':-@' title=':-@' /></a>
        <a href="javascript:SmileIT(':crylol','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/crylaugh.png" border="0" alt=':|' title=':|' /></a>
        <a href="javascript:SmileIT(':confos','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/confound.png" border="0" alt='O:-D' title='0:-D' /></a>
        <a href="javascript:SmileIT(':fire','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/fire.png" border="0" alt=':-@' title=':-@' /></a>
        </div>
    	</div>
		
		<div class="row justify-content-md-center">
        <div class="col-10">
	    <textarea class="form-control" name="<?php echo $name; ?>" rows="13"><?php echo $content; ?></textarea>
        </div>
		</div>
		
		<div class="row justify-content-md-center">
        <div class="col-10">
        <?php
        // Refresh And Preview Button
        print("<center><input type='reset' value='Refresh' />&nbsp;<input type='button' value='Preview' onClick='visualisation()' /></center><br>");
        // Creation of the Preview Area
        print("<div id='previsualisation' width='200px' height='200px'></div></font></center></div><br>");
        ?>
	    </div>
        </div>
<?php
}

function shoutbbcode($form, $name, $content = "")
{
    //$form = form name
    //$name = textarea name
    //$content = textarea content (only for edit pages etc)
    require "assets/js/BBTag.js";
    print("<center><input id='BBCode' type='button' name='Bold' 			value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/bold.gif');  height:20px; width:20px;\" 					onclick=\"bbcomment('[b]', '[/b]')\" 					alt='Bold' 				title='Bold' 				/>");
    print("<input id='BBCode' type='button' name='Italic' 			value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/italic.png');  height:20px; width:20px;\" 					onclick=\"bbcomment('[i]', '[/i]')\" 					alt='Italique' 			title='Italique' 			/>");
    print("<input id='BBCode' type='button' name='Highlight' 			value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/underline.png');  height:20px; width:20px;\" 				onclick=\"bbcomment('[u]', '[/u]')\" 					alt='Highlight' 			title='Highlight'			/>");
    print("<input id='BBCode' type='button' name='Barré' 				value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/strike.png');  height:20px; width:20px;\"			 		onclick=\"bbcomment('[s]', '[/s]')\" 					alt='Ligne Barrée' 		title='Ligne Barrée'		/>");
    print("<input id='BBCode' type='button' name='List' 				value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/indent.png');  height:20px; width:20px;\" 					onclick=\"bbcomment('[list]', '[/list]')\" 				alt='List' 				title='List'				/>");
    print("<input id='BBCode' type='button' name='Quote' 			       value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/quote.png');  height:20px; width:20px;\"			 		onclick=\"bbcomment('[quote]', '[/quote]')\" 			alt='Quote' 			title='Quote'			/>");
    print("<input id='BBCode' type='button' name='Code' 				value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/code.png');  height:20px; width:20px;\" 					onclick=\"bbcomment('[code]', '[/code]')\" 			alt='Code' 			title='Code'				/>");
    print("<input id='BBCode' type='button' name='Url' 				value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/Link.gif');  height:20px; width:20px;\" 					onclick=\"bbcomment('[url]', '[/url]')\"				alt='Lnk' 				title='Link'				/>");
    print("<input id='BBCode' type='button' name='Image' 				value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/image.png');  height:20px; width:20px;\" 					onclick=\"bbcomment('[img]', '[/img]')\"				alt='Image' 			title='Image'				/>");
    print("<input id='BBCode' type='button' name='Video' 				value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/Video.gif');  height:20px; width:20px;\" 					onclick=\"bbcomment('[video]', '[/video]')\" 			alt='Vidéo' 			title='Vidéo'				/>");
    print("<input id='BBCode' type='button' name='scroller' 			value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/scroller.png');  height:20px; width:20px;\"					onclick=\"bbcomment('[df]', '[/df]')\" 				alt='scroller' 			title='scroller'			/>");
    print("<input id='BBCode' type='button' name='Align Right'            	value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/right.gif');  height:20px; width:20px;\" 					onclick=\"bbcomment('[align=left]','[/align]')\" 			alt='Align Right' 		title='Align Right' 	/>");
    print("<input id='BBCode' type='button' name='Align Center'        	value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/center.gif');  height:20px; width:20px;\" 					onclick=\"bbcomment('[align=center]','[/align]')\" 		alt='Align Center' 		title='Align Center' 	/>");
    print("<input id='BBCode' type='button' name='Align Left'             	value='' style=\"background: url('" . URLROOT . "/assets/images/bbcodes/left.gif');  height:20px; width:20px;\" 					onclick=\"bbcomment('[align=right]','[/align]')\" 		alt='Align Left' 		       title='Align Left' 	/>");
    print("<a href='#' onClick=\"window.open('http://www.zupimages.net','_blank');return(false)\">		<input id='BBCode' type='button' value='' 	style=\"background: url('" . URLROOT . "/assets/images/bbcodes/imgur.gif');  height:20px; width:20px;\" 		alt='Upload Image' 			title='Upload Image' /></a>");
    print("<a href='#' onClick=\"window.open('http://www.youtube.com','_blank');return(false)\">		<input id='BBCode' type='button' value='' 	style=\"background: url('" . URLROOT . "/assets/images/bbcodes/youtube.gif');  height:20px; width:20px;\"										alt='YouTube' 				title='YouTube' /></a></center>");
    // History
    print("<center><a href=" . URLROOT . "/shoutbox/history  target='_blank'><b>History</b></a>
	&nbsp;&nbsp;<a  onclick='myFunction()'><img src='" . URLROOT . "/assets/images/smilies/grin.png' alt='' /></a>&nbsp;&nbsp;");
    // Choose the colour
	print("<select name='color' style='padding-bottom:3px;' onChange='bbcouleur(this.value);' title='Couleur'>");
    print("<option value='0' name='color'>Colour</option>");
    print("<option value='#000000' style='BACKGROUND-COLOR:#000000'>Black</option>");
    print("<option value='#686868' style='BACKGROUND-COLOR:#686868'>Grey</option>");
    print("<option value='#708090' style='BACKGROUND-COLOR:#708090'>Dark Grey</option>");
    print("<option value='#C0C0C0' style='BACKGROUND-COLOR:#C0C0C0'>Light Grey</option>");
    print("<option value='#FFFFFF' style='BACKGROUND-COLOR:#FFFFFF'>White</option>");
    print("<option value='#FFFFE0' style='BACKGROUND-COLOR:#FFFFE0'>Beech</option>");
    print("<option value='#880000' style='BACKGROUND-COLOR:#880000'>Dark Red</option>");
    print("<option value='#B82428' style='BACKGROUND-COLOR:#B82428'>Light Red</option>");
    print("<option value='#FF0000' style='BACKGROUND-COLOR:#FF0000'>Red</option>");
    print("<option value='#FF1490' style='BACKGROUND-COLOR:#FF1490'>Dark Pink</option>");
    print("<option value='#FF68B0' style='BACKGROUND-COLOR:#FF68B0'>Pink</option>");
    print("<option value='#FFC0C8' style='BACKGROUND-COLOR:#FFC0C8'>Light Pink</option>");
    print("<option value='#FF4400' style='BACKGROUND-COLOR:#FF4400'>Dark Orange</option>");
    print("<option value='#FF6448' style='BACKGROUND-COLOR:#FF6448'>Redish Orange</option>");
    print("<option value='#FFA800' style='BACKGROUND-COLOR:#FFA800'>Orange</option>");
    print("<option value='#FFD800' style='BACKGROUND-COLOR:#FFD800'>Dark Yellow</option>");
    print("<option value='#FFFF00' style='BACKGROUND-COLOR:#FFFF00'>Yellow</option>");
    print("<option value='#FF00FF' style='BACKGROUND-COLOR:#FF00FF'>Light Purple</option>");
    print("<option value='#C01480' style='BACKGROUND-COLOR:#C01480'>Dark Purple</option>");
    print("<option value='#B854D8' style='BACKGROUND-COLOR:#B854D8'>Dark Violet</option>");
    print("<option value='#D8A0D8' style='BACKGROUND-COLOR:#D8A0D8'>Light Violet</option>");
    print("<option value='#000080' style='BACKGROUND-COLOR:#000080'>Darkest Blue</option>");
    print("<option value='#0000FF' style='BACKGROUND-COLOR:#0000FF'>Dark Blue</option>");
    print("<option value='#2090FF' style='BACKGROUND-COLOR:#2090FF'>Ble</option>");
    print("<option value='#00BCFF' style='BACKGROUND-COLOR:#00BCFF'>Light Blue</option>");
    print("<option value='#B0E0E8' style='BACKGROUND-COLOR:#B0E0E8'>Faint Blue</option>");
    print("<option value='#A02828' style='BACKGROUND-COLOR:#A02828'>Brown</option>");
    print("<option value='#F0A460' style='BACKGROUND-COLOR:#F0A460'>Brown Creme</option>");
    print("<option value='#D0B488' style='BACKGROUND-COLOR:#D0B488'>Light Brown</option>");
    print("<option value='#B8B868' style='BACKGROUND-COLOR:#B8B868'>Brown/Green</option>");
    print("<option value='#008000' style='BACKGROUND-COLOR:#008000'>Dark Green</option>");
    print("<option value='#30CC30' style='BACKGROUND-COLOR:#30CC30'>Green</option>");
    print("<option value='#00FF80' style='BACKGROUND-COLOR:#00FF80'>Light Green</option>");
    print("<option value='#98FC98' style='BACKGROUND-COLOR:#98FC98'>Light Lime</option>");
    print("<option value='#98CC30' style='BACKGROUND-COLOR:#98CC30'>Dark Lime</option>");
    print("<option value='#40E0D0' style='BACKGROUND-COLOR:#40E0D0'>Turquois</option>");
    print("<option value='#20B4A8' style='BACKGROUND-COLOR:#20B4A8'>Aquarium</option></select>");
    print("&nbsp;&nbsp;</center>");
	// Smilies
    ?>
    <div class="container">
        <div class="row justify-content-md-center">
        <div class="col col-lg-10">
        <div id="myDIVsmileytog" style="display:none">
        <a href="javascript:SmileIT(':)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/smile.png" border="0" alt=':)' title=':)' /></a>
        <a href="javascript:SmileIT(':(','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/sad.png" border="0" alt=':(' title=':(' /></a>
        <a href="javascript:SmileIT(':D','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/grin.png" border="0" alt=':D' title=':D' /></a>
        <a href="javascript:SmileIT(':P','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/razz.png" border="0" alt=':P' title=':P' /></a>
        <a href="javascript:SmileIT(':-)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/smile-big.png" border="0" alt=':-)' title=':-)' /></a>
        <a href="javascript:SmileIT('B)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/cool.png" border="0" alt='B)' title='B)' /></a>
        <a href="javascript:SmileIT('8o','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/eek.png" border="0" alt='8o' title='8o' /></a>
        <a href="javascript:SmileIT(':?','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/confused.png" border="0" alt=':?' title=':?' /></a>
        <a href="javascript:SmileIT('8)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/glasses.png" border="0" alt='8)' title='8)' /></a>
        <a href="javascript:SmileIT(';)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/wink.png" border="0" alt=';)' title=';)' /></a>
        <a href="javascript:SmileIT(':-*','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/kiss.png" border="0" alt=':-*' title=':-*' /></a>
        <a href="javascript:SmileIT(':-(','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/crying.png" border="0" alt=':-(' title=':-(' /></a>
        <a href="javascript:SmileIT(':|','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/plain.png" border="0" alt=':|' title=':|' /></a>
        <a href="javascript:SmileIT('O:-D','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/angel.png" border="0" alt='O:-D' title='0:-D' /></a>
        <a href="javascript:SmileIT(':-@','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/devilish.png" border="0" alt=':-@' title=':-@' /></a>
        <a href="javascript:SmileIT(':o)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/monkey.png" border="0" alt=':o)' title=':o)' /></a>
        <a href="javascript:SmileIT('brb','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/brb.png" border="0" alt='brb' title='brb' /></a>
        <a href="javascript:SmileIT(':warn','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/warn.png" border="0" alt=':warn' title=':warn' /></a>
        <a href="javascript:SmileIT(':help','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/help.png" border="0" alt=':help' title=':help' /></a>
        <a href="javascript:SmileIT(':bad','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/bad.png" border="0" alt=':bad' title=':bad' /></a>
        <a href="javascript:SmileIT(':love','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/love.png" border="0" alt=':love' title=':love' /></a>
        <a href="javascript:SmileIT(':idea','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/idea.png" border="0" alt=':idea' title=':idea' /></a>
        <a href="javascript:SmileIT(':bomb','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/bomb.png" border="0" alt=':bomb' title=':bomb' /></a>
        <a href="javascript:SmileIT(':!','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/important.png" border="0" alt=':!' title=':!' /></a>
        <a href="javascript:SmileIT(':gigg','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/giggle.png" border="0" alt=':|' title=':|' /></a>
        <a href="javascript:SmileIT(':rofl','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/roflmao.png" border="0" alt='O:-D' title='0:-D' /></a>
        <a href="javascript:SmileIT(':slep','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/sleep.png" border="0" alt=':-@' title=':-@' /></a>
        <a href="javascript:SmileIT(':thum','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/thumbsup.png" border="0" alt=':o)' title=':o)' /></a>
        <a href="javascript:SmileIT(':0_0','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/zpo.png" border="0" alt=':|' title=':|' /></a>
        <a href="javascript:SmileIT(':poop','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/poop.png" border="0" alt='O:-D' title='0:-D' /></a>
        <a href="javascript:SmileIT(':spechles','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/speechless.png" border="0" alt=':-@' title=':-@' /></a>
        <a href="javascript:SmileIT(':unsure','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/unsure.png" border="0" alt=':o)' title=':o)' /></a>
        <a href="javascript:SmileIT(':mad','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/mad.png" border="0" alt=':|' title=':|' /></a>
        <a href="javascript:SmileIT(':roll','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/rolleyes.png" border="0" alt='O:-D' title='0:-D' /></a>
        <a href="javascript:SmileIT(':sick','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/sick.png" border="0" alt=':-@' title=':-@' /></a>
        <a href="javascript:SmileIT(':crylol','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/crylaugh.png" border="0" alt=':|' title=':|' /></a>
        <a href="javascript:SmileIT(':confos','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/confound.png" border="0" alt='O:-D' title='0:-D' /></a>
        <a href="javascript:SmileIT(':fire','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="<?php echo URLROOT; ?>/assets/images/smilies/fire.png" border="0" alt=':-@' title=':-@' /></a>
        </div>
		</div>

        <div class="row">
        <div class="col-md-11">
        <input  class="form-control shoutbox_msgbox" type='text' size='100%' name="<?php echo $name; ?>"><?php echo $content; ?>
        </div>
        <div class="col-md-1">
        <center><input type='submit' name='submit' value='<?php echo Lang::T("SHOUT") ?>' class='btn btn-sm btn-primary' /></center>
        </div>
        </div>
    </div>
    </div>
<?php
}